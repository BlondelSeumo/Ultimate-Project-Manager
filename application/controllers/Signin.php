<?php

class Signin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('email');
        $this->load->model("Verification_model");
    }

    function index() {
        if ($this->Users_model->login_user_id()) {
            redirect('dashboard/view');
        } else {

            $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }


            //check if there reCaptcha is enabled
            //if reCaptcha is enabled, check the validation
            if (get_setting("re_captcha_secret_key")) {
                $this->form_validation->set_rules('g-recaptcha-response', '', 'callback_check_recaptcha');
            }


            $this->form_validation->set_rules('email', '', 'callback_authenticate');
            $this->form_validation->set_error_delimiters('<span>', '</span>');


            if ($this->form_validation->run() == FALSE) {

                $this->load->view('signin/index', $view_data);
            } else {

                if ($view_data["redirect"]) {
                    redirect($view_data["redirect"]);
                } else {
                    redirect('dashboard/view');
                }
            }
        }
    }

    function check_recaptcha($recaptcha_post_data) {

        $response = $this->is_valid_recaptcha($recaptcha_post_data);

        if ($response === true) {
            return true;
        } else {
            $this->form_validation->set_message('check_recaptcha', lang("re_captcha_error-" . $response));
            return false;
        }
    }

    private function is_valid_recaptcha($recaptcha_post_data) {
        //load recaptcha lib
        require_once(APPPATH . "third_party/recaptcha/autoload.php");
        $recaptcha = new \ReCaptcha\ReCaptcha(get_setting("re_captcha_secret_key"));
        $resp = $recaptcha->verify($recaptcha_post_data, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            return true;
        } else {

            $error = "";
            foreach ($resp->getErrorCodes() as $code) {
                $error = $code;
            }

            return $error;
        }
    }

    // check authentication
    function authenticate($email) {

        //don't check password if there is any error
        if (validation_errors()) {
            $this->form_validation->set_message('authenticate', "");
            return false;
        }


        $password = $this->input->post("password");
        if (!$this->Users_model->authenticate($email, $password)) {
            $this->form_validation->set_message('authenticate', lang("authentication_failed"));
            return false;
        }
        return true;
    }

    function sign_out() {
        $this->Users_model->sign_out();
    }

    //send an email to users mail with reset password link
    function send_reset_password_mail() {
        validate_submitted_data(array(
            "email" => "required|valid_email"
        ));


        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {

            $response = $this->is_valid_recaptcha($this->input->post("g-recaptcha-response"));

            if ($response !== true) {

                if ($response) {
                    echo json_encode(array('success' => false, 'message' => lang("re_captcha_error-" . $response)));
                } else {
                    echo json_encode(array('success' => false, 'message' => lang("re_captcha_expired")));
                }

                return false;
            }
        }



        $email = $this->input->post("email");
        $existing_user = $this->Users_model->is_email_exists($email);

        //send reset password email if found account with this email
        if ($existing_user) {
            $email_template = $this->Email_templates_model->get_final_template("reset_password");

            $parser_data["ACCOUNT_HOLDER_NAME"] = $existing_user->first_name . " " . $existing_user->last_name;
            $parser_data["SIGNATURE"] = $email_template->signature;
            $parser_data["LOGO_URL"] = get_logo_url();
            $parser_data["SITE_URL"] = get_uri();

            $verification_data = array(
                "type" => "reset_password",
                "code" => make_random_string(),
                "params" => serialize(array(
                    "email" => $existing_user->email,
                    "expire_time" => time() + (24 * 60 * 60)
                ))
            );

            $save_id = $this->Verification_model->save($verification_data);

            $verification_info = $this->Verification_model->get_one($save_id);

            $parser_data['RESET_PASSWORD_URL'] = get_uri("signin/new_password/" . $verification_info->code);

            $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
            if (send_app_mail($email, $email_template->subject, $message)) {
                echo json_encode(array('success' => true, 'message' => lang("reset_info_send")));
            } else {
                echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => lang("no_acount_found_with_this_email")));
            return false;
        }
    }

    //show forgot password recovery form
    function request_reset_password() {
        $view_data["form_type"] = "request_reset_password";
        $this->load->view('signin/index', $view_data);
    }

    //when user clicks to reset password link from his/her email, redirect to this url
    function new_password($key) {
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");

            if ($this->Users_model->is_email_exists($email)) {
                $view_data["key"] = $key;
                $view_data["form_type"] = "new_password";
                $this->load->view('signin/index', $view_data);
                return false;
            }
        }

        //else show error
        $view_data["heading"] = "Invalid Request";
        $view_data["message"] = "The key has expaired or something went wrong!";
        $this->load->view("errors/html/error_general", $view_data);
    }

    //finally reset the old password and save the new password
    function do_reset_password() {

        validate_submitted_data(array(
            "key" => "required",
            "password" => "required"
        ));


        $key = $this->input->post("key");
        $password = $this->input->post("password");
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");
            $user = $this->Users_model->is_email_exists($email);
            $user_data = array("password" => password_hash($password, PASSWORD_DEFAULT));
            if ($user->id && $this->Users_model->save($user_data, $user->id)) {
                //user can't reset password two times with the same code
                $options = array("code" => $key, "type" => "reset_password");
                $verification_info = $this->Verification_model->get_details($options)->row();
                if ($verification_info->id) {
                    $this->Verification_model->delete($verification_info->id);
                }

                echo json_encode(array("success" => true, 'message' => lang("password_reset_successfully") . " " . anchor("signin", lang("signin"))));
                return true;
            }
        }
        echo json_encode(array("success" => false, 'message' => lang("error_occurred")));
    }

    //check valid key
    private function is_valid_reset_password_key($verification_code = "") {

        if ($verification_code) {
            $options = array("code" => $verification_code, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->row();

            if ($verification_info && $verification_info->id) {
                $reset_password_info = unserialize($verification_info->params);

                $email = get_array_value($reset_password_info, "email");
                $expire_time = get_array_value($reset_password_info, "expire_time");

                if ($email && valid_email($email) && $expire_time && $expire_time > time()) {
                    return array("email" => $email);
                }
            }
        }
    }

}
