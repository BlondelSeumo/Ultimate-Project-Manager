<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pay_invoice extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("Verification_model");
    }

    function index($verification_code = "") {
        if (!get_setting("client_can_pay_invoice_without_login")) {
            redirect("forbidden");
        }

        if ($verification_code) {
            $options = array("code" => $verification_code, "type" => "invoice_payment");
            $verification_info = $this->Verification_model->get_details($options)->row();

            if ($verification_info && $verification_info->id) {
                $invoice_data = unserialize($verification_info->params);

                $invoice_id = get_array_value($invoice_data, "invoice_id");
                $client_id = get_array_value($invoice_data, "client_id");
                $contact_id = get_array_value($invoice_data, "contact_id");

                $this->_log("invoice_id:$invoice_id, client_id:$client_id, contact_id:$contact_id");

                if ($invoice_id && is_numeric($invoice_id) && $client_id && is_numeric($client_id) && $contact_id && is_numeric($contact_id)) {
                    $view_data = get_invoice_making_data($invoice_id);
                    $view_data['payment_methods'] = $this->Payment_methods_model->get_available_online_payment_methods();

                    //check access of this invoice
                    $this->_check_access_of_invoice($view_data);

                    $view_data['invoice_preview'] = prepare_invoice_pdf($view_data, "html");

                    $view_data['invoice_id'] = $invoice_id;

                    $this->load->library("paypal");
                    $view_data['paypal_url'] = $this->paypal->get_paypal_url();

                    $view_data['contact_id'] = $contact_id;
                    $view_data['verification_code'] = $verification_code;

                    $this->load->view("invoices/public_invoice_preview", $view_data);
                } else {
                    show_404();
                }
            } else {
                show_404();
            }
        }
    }

    private function _check_access_of_invoice($view_data) {
        if (count($view_data) && count(get_array_value($view_data, 'payment_methods')) && !get_array_value($view_data, "client_info")->disable_online_payment) {
            return true;
        } else {
            redirect("forbidden");
        }
    }

    private function validate_verification_code($code = "", $given_invoice_data = array()) {
        if ($code) {
            $options = array("code" => $code, "type" => "invoice_payment");
            $verification_info = $this->Verification_model->get_details($options)->row();

            if ($verification_info && $verification_info->id) {
                $existing_invoice_data = unserialize($verification_info->params);

                //existing data
                $existing_invoice_id = get_array_value($existing_invoice_data, "invoice_id");
                $existing_client_id = get_array_value($existing_invoice_data, "client_id");
                $existing_contact_id = get_array_value($existing_invoice_data, "contact_id");

                //given data 
                $given_invoice_id = get_array_value($given_invoice_data, "invoice_id");
                $given_client_id = get_array_value($given_invoice_data, "client_id");
                $given_contact_id = get_array_value($given_invoice_data, "contact_id");

                if ($existing_invoice_id === $given_invoice_id && $existing_client_id === $given_client_id && $existing_contact_id === $given_contact_id) {
                    return true;
                }
            }
        }
    }

    function pay_invoice_via_stripe() {
        if (!get_setting("client_can_pay_invoice_without_login")) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "stripe_token" => "required",
            "invoice_id" => "required",
            "verification_code" => "required"
        ));

        $invoice_id = $this->input->post('invoice_id');
        $method_info = $this->Payment_methods_model->get_oneline_payment_method("stripe");

        //load stripe lib
        require_once(APPPATH . "third_party/Stripe/init.php");
        \Stripe\Stripe::setApiKey($method_info->secret_key);


        if (!$invoice_id) {
            redirect("forbidden");
        }

        $verification_code = $this->input->post('verification_code');
        $redirect_to = "pay_invoice/index/$verification_code";

        try {

            //check payment token
            $card = $this->input->post('stripe_token');

            $invoice_data = (Object) get_invoice_making_data($invoice_id);
            $currency = $invoice_data->invoice_total_summary->currency;


            //check if partial payment allowed or not
            if (get_setting("allow_partial_invoice_payment_from_clients")) {
                $payment_amount = unformat_currency($this->input->post('payment_amount'));
            } else {
                $payment_amount = $invoice_data->invoice_total_summary->balance_due;
            }


            //validate payment amount
            if ($payment_amount < $method_info->minimum_payment_amount * 1) {
                $error_message = lang('minimum_payment_validation_message') . " " . to_currency($method_info->minimum_payment_amount, $currency . " ");
                $this->session->set_flashdata("error_message", $error_message);
                redirect($redirect_to);
            }



            //prepare stripe payment data

            $contact_user_id = $this->input->post('contact_user_id');

            //validate payment information
            if (!$this->validate_verification_code($verification_code, array("invoice_id" => $invoice_id, "client_id" => $invoice_data->client_info->id, "contact_id" => $contact_user_id))) {
                redirect("forbidden");
            }

            $metadata = array(
                "invoice_id" => $invoice_id,
                "contact_user_id" => $contact_user_id,
                "client_id" => $invoice_data->client_info->id
            );

            $stripe_data = array(
                "amount" => $payment_amount * 100, //convert to cents
                "currency" => $currency,
                "card" => $card,
                "metadata" => $metadata,
                "description" => get_invoice_id($invoice_id) . ", " . lang('amount') . ": " . to_currency($payment_amount, $currency . " ")
            );

            $charge = \Stripe\Charge::create($stripe_data);

            if ($charge->paid) {

                //payment complete, insert payment record
                $invoice_payment_data = array(
                    "invoice_id" => $invoice_id,
                    "payment_date" => get_my_local_time(),
                    "payment_method_id" => $method_info->id,
                    "note" => $this->input->post('invoice_payment_note'),
                    "amount" => $payment_amount,
                    "transaction_id" => $charge->id,
                    "created_at" => get_current_utc_time(),
                    "created_by" => $contact_user_id,
                );

                $invoice_payment_id = $this->Invoice_payments_model->save($invoice_payment_data);
                if ($invoice_payment_id) {

                    //As receiving payment for the invoice, we'll remove the 'draft' status from the invoice 
                    $this->Invoices_model->update_invoice_status($invoice_id);

                    log_notification("invoice_payment_confirmation", array("invoice_payment_id" => $invoice_payment_id, "invoice_id" => $invoice_id), "0");
                    log_notification("invoice_online_payment_received", array("invoice_payment_id" => $invoice_payment_id, "invoice_id" => $invoice_id));
                    $this->session->set_flashdata("success_message", lang("payment_success_message"));
                    redirect($redirect_to);
                } else {
                    $this->session->set_flashdata("error_message", lang("payment_card_charged_but_system_error_message"));
                    redirect($redirect_to);
                }
            } else {
                $this->session->set_flashdata("error_message", lang("card_payment_failed_error_message"));
                redirect($redirect_to);
            }
        } catch (Stripe_CardError $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        } catch (Stripe_InvalidRequestError $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        } catch (Stripe_AuthenticationError $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        } catch (Stripe_ApiConnectionError $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        } catch (Stripe_Error $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        } catch (Exception $e) {

            $error_data = $e->getJsonBody();
            $this->session->set_flashdata("error_message", $error_data['error']['message']);
            redirect($redirect_to);
        }
    }

    private function _log($text = "") {
        if ($text && get_setting("enable_public_pay_invoice_logging")) {
            error_log(date('[Y-m-d H:i e] ') . $text . PHP_EOL, 3, "public_pay_invoice_logs.txt");
        }
    }

}

/* End of file Pay_invoice.php */
/* Location: ./application/controllers/Pay_invoice.php */