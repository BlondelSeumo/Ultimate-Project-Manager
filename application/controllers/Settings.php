<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
        redirect('settings/general');
    }

    function general() {
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $view_data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $view_data['timezone_dropdown'][$zone] = $zone;
        }

        $view_data['language_dropdown'] = get_language_list();

        $view_data["currency_dropdown"] = get_international_currency_code_dropdown();
        $this->template->rander("settings/general", $view_data);
    }

    function save_general_settings() {
        $settings = array("site_logo", "favicon", "show_background_image_in_signin_page", "show_logo_in_signin_page", "app_title", "language", "timezone", "date_format", "time_format", "first_day_of_week", "default_currency", "currency_symbol", "currency_position", "decimal_separator", "no_of_decimals", "accepted_file_formats", "rows_per_page", "item_purchase_code", "scrollbar", "enable_rich_text_editor", "rtl", "show_theme_color_changer", "default_theme_color");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if ($value || $value === "0") {
                if ($setting === "site_logo") {
                    $value = str_replace("~", ":", $value);
                    $value = serialize(move_temp_file("site-logo.png", get_setting("system_file_path"), "", $value));

                    //delete old file
                    delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("site_logo"));
                } else if ($setting === "item_purchase_code" && $value === "******") {
                    $value = get_setting('item_purchase_code');
                } else if ($setting === "favicon") {
                    $value = str_replace("~", ":", $value);
                    $value = serialize(move_temp_file("favicon.png", get_setting("system_file_path"), "", $value));

                    //delete old file
                    if (get_setting("favicon")) {
                        delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("favicon"));
                    }
                }


                $this->Settings_model->save_setting($setting, $value);
            }
        }

        //save signin page background
        $files_data = move_files_from_temp_dir_to_permanent_dir(get_setting("system_file_path"), "system");
        $unserialize_files_data = unserialize($files_data);
        $sigin_page_background = get_array_value($unserialize_files_data, 0);
        if ($sigin_page_background) {
            delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("signin_page_background"));
            $this->Settings_model->save_setting("signin_page_background", serialize($sigin_page_background));
        }

        if ($_FILES) {
            $site_logo_file = get_array_value($_FILES, "site_logo_file");
            $site_logo_file_name = get_array_value($site_logo_file, "tmp_name");
            if ($site_logo_file_name) {
                $site_logo = serialize(move_temp_file("site-logo.png", get_setting("system_file_path")));
                //delete old file
                delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("site_logo"));
                $this->Settings_model->save_setting("site_logo", $site_logo);
            }
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated'), 'reload_page' => $sigin_page_background));
    }

    function company() {
        $this->template->rander("settings/company");
    }

    function save_company_settings() {
        $settings = array("company_name", "company_address", "company_phone", "company_email", "company_website", "company_vat_number");

        foreach ($settings as $setting) {
            $this->Settings_model->save_setting($setting, $this->input->post($setting));
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function email() {
        $this->template->rander("settings/email");
    }

    function save_email_settings() {
        $settings = array("email_sent_from_address", "email_sent_from_name", "email_protocol", "email_smtp_host", "email_smtp_port", "email_smtp_user", "email_smtp_pass", "email_smtp_security_type");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (!$value) {
                $value = "";
            }

            if ($setting == "email_smtp_pass") {
                $value = encode_id($value, "email_smtp_pass");
            }

            $this->Settings_model->save_setting($setting, $value);
        }

        $test_email_to = $this->input->post("send_test_mail_to");
        if ($test_email_to) {
            $email_config = Array(
                'charset' => 'utf-8',
                'mailtype' => 'html'
            );
            if ($this->input->post("email_protocol") === "smtp") {
                $email_config["protocol"] = "smtp";
                $email_config["smtp_host"] = $this->input->post("email_smtp_host");
                $email_config["smtp_port"] = $this->input->post("email_smtp_port");
                $email_config["smtp_user"] = $this->input->post("email_smtp_user");
                $email_config["smtp_pass"] = $this->input->post("email_smtp_pass") ? $this->input->post("email_smtp_pass") : decode_password(get_setting('email_smtp_pass'), "email_smtp_pass");
                $email_config["smtp_crypto"] = $this->input->post("email_smtp_security_type");
                if ($email_config["smtp_crypto"] === "none") {
                    $email_config["smtp_crypto"] = "";
                }
            }

            $this->load->library('email', $email_config);
            $this->email->set_newline("\r\n");
            $this->email->set_crlf("\r\n");
            $this->email->from($this->input->post("email_sent_from_address"), $this->input->post("email_sent_from_name"));

            $this->email->to($test_email_to);
            $this->email->subject("Test message");
            $this->email->message("This is a test message to check mail configuration.");

            if ($this->email->send()) {
                echo json_encode(array("success" => true, 'message' => lang('test_mail_sent')));
                return false;
            } else {
                log_message('error', $this->email->print_debugger());
                echo json_encode(array("success" => false, 'message' => lang('test_mail_send_failed')));
                return false;
            }
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function ip_restriction() {
        $this->template->rander("settings/ip_restriction");
    }

    function save_ip_settings() {
        $this->Settings_model->save_setting("allowed_ip_addresses", $this->input->post("allowed_ip_addresses"));

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function db_backup() {
        $this->template->rander("settings/db_backup");
    }

    function client_permissions() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->result();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $hidden_menus = array(
            "announcements",
            "events",
            "estimates",
            "invoices",
            "knowledge_base",
            "projects",
            "payments",
            "tickets"
        );

        $hidden_menu_dropdown = array();
        foreach ($hidden_menus as $hidden_menu) {
            $hidden_menu_dropdown[] = array("id" => $hidden_menu, "text" => lang($hidden_menu));
        }

        $view_data['hidden_menu_dropdown'] = json_encode($hidden_menu_dropdown);
        $view_data['members_dropdown'] = json_encode($members_dropdown);
        $this->template->rander("settings/client_permissions", $view_data);
    }

    function save_client_settings() {
        $settings = array(
            "disable_client_login",
            "disable_client_signup",
            "client_message_users",
            "hidden_client_menus",
            "client_can_create_projects",
            "client_can_create_tasks",
            "client_can_edit_tasks",
            "client_can_view_tasks",
            "client_can_comment_on_tasks",
            "client_can_view_project_files",
            "client_can_add_project_files",
            "client_can_comment_on_files",
            "client_can_view_milestones",
            "client_can_view_overview",
            "client_can_view_gantt",
            "client_can_view_files",
            "client_can_add_files",
            "client_can_edit_projects",
            "client_can_view_activity",
            "client_message_own_contacts",
            "disable_user_invitation_option_by_clients"
        );

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function invoices() {
        $last_invoice_id = $this->Invoices_model->get_last_invoice_id();

        $view_data["last_id"] = $last_invoice_id;

        $this->template->rander("settings/invoices", $view_data);
    }

    function save_invoice_settings() {
        $settings = array("allow_partial_invoice_payment_from_clients", "invoice_color", "invoice_footer", "send_bcc_to", "invoice_prefix", "invoice_style", "invoice_logo", "send_invoice_due_pre_reminder", "send_invoice_due_after_reminder", "send_recurring_invoice_reminder_before_creation", "default_due_date_after_billing_date", "initial_number_of_the_invoice", "client_can_pay_invoice_without_login");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            $saveable = true;

            if ($setting == "invoice_footer") {
                $value = decode_ajax_post_data($value);
            } else if ($setting === "invoice_logo" && $value) {
                $value = str_replace("~", ":", $value);
                $value = serialize(move_temp_file("invoice-logo.png", get_setting("system_file_path"), "", $value));
            }

            //don't save blank image
            if ($setting === "invoice_logo" && !$value) {
                $saveable = false;
            }

            if ($saveable) {
                if ($setting === "invoice_logo") {
                    //delete old file
                    delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("invoice_logo"));
                }

                $this->Settings_model->save_setting($setting, $value);
            }

            if ($setting === "initial_number_of_the_invoice") {
                $this->Invoices_model->save_initial_number_of_invoice($value);
            }
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function events() {
        $this->template->rander("settings/events");
    }

    function save_event_settings() {
        $enable_google_calendar_api = $this->input->post("enable_google_calendar_api");
        $enable_google_calendar_api = is_null($enable_google_calendar_api) ? "" : $enable_google_calendar_api;

        $this->Settings_model->save_setting("enable_google_calendar_api", $enable_google_calendar_api);

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function notifications() {
        $category_suggestions = array(
            array("id" => "", "text" => "- " . lang('category') . " -"),
            array("id" => "announcement", "text" => lang("announcement")),
            array("id" => "client", "text" => lang("client")),
            array("id" => "event", "text" => lang("event")),
            array("id" => "estimate", "text" => lang("estimate")),
            array("id" => "invoice", "text" => lang("invoice")),
            array("id" => "leave", "text" => lang("leave")),
            array("id" => "lead", "text" => lang("lead")),
            array("id" => "message", "text" => lang("message")),
            array("id" => "project", "text" => lang("project")),
            array("id" => "ticket", "text" => lang("ticket"))
        );

        $view_data['categories_dropdown'] = json_encode($category_suggestions);
        $this->template->rander("settings/notifications/index", $view_data);
    }

    function notification_modal_form() {
        $id = $this->input->post("id");
        if ($id) {

            $this->load->helper('notifications');

            $model_info = $this->Notification_settings_model->get_details(array("id" => $id))->row();
            $notify_to = get_notification_config($model_info->event, "notify_to");

            if (!$notify_to) {
                $notify_to = array();
            }

            $members_dropdown = array();
            $team_dropdown = array();

            //prepare team dropdown list
            if (in_array("team_members", $notify_to)) {
                $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->result();

                foreach ($team_members as $team_member) {
                    $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
                }
            }


            //prepare team member dropdown list
            if (in_array("team", $notify_to)) {
                $teams = $this->Team_model->get_all_where(array("deleted" => 0))->result();
                foreach ($teams as $team) {
                    $team_dropdown[] = array("id" => $team->id, "text" => $team->title);
                }
            }

            //prepare notify to terms
            if ($model_info->notify_to_terms) {
                $model_info->notify_to_terms = explode(",", $model_info->notify_to_terms);
            } else {
                $model_info->notify_to_terms = array();
            }

            $view_data['members_dropdown'] = json_encode($members_dropdown);
            $view_data['team_dropdown'] = json_encode($team_dropdown);

            $view_data["notify_to"] = $notify_to;
            $view_data["model_info"] = $model_info;

            $this->load->view("settings/notifications/modal_form", $view_data);
        }
    }

    function notification_settings_list_data() {

        $options = array("category" => $this->input->post("category"));
        $list_data = $this->Notification_settings_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_notification_settings_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _notification_list_data($id) {
        $options = array("id" => $id);
        $data = $this->Notification_settings_model->get_details($options)->row();
        return $this->_make_notification_settings_row($data);
    }

    private function _make_notification_settings_row($data) {

        $yes = "<i class='fa fa-check-circle'></i>";
        $no = "<i class='fa fa-check-circle' style='opacity:0.2'></i>";

        $notify_to = "";

        if ($data->notify_to_terms) {
            $terms = explode(",", $data->notify_to_terms);
            foreach ($terms as $term) {
                if ($term) {
                    $notify_to .= "<li>" . lang($term) . "</li>";
                }
            }
        }

        if ($data->notify_to_team_members) {
            $notify_to .= "<li>" . lang("team_members") . ": " . $data->team_members_list . "</li>";
        }

        if ($data->notify_to_team) {
            $notify_to .= "<li>" . lang("team") . ": " . $data->team_list . "</li>";
        }

        if ($notify_to) {
            $notify_to = "<ul class='pl15'>" . $notify_to . "</ul>";
        }

        return array(
            $data->sort,
            lang($data->event),
            $notify_to,
            lang($data->category),
            $data->enable_email ? $yes : $no,
            $data->enable_web ? $yes : $no,
            modal_anchor(get_uri("settings/notification_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification'), "data-post-id" => $data->id))
        );
    }

    function save_notification_settings() {
        $id = $this->input->post("id");

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $data = array(
            "enable_web" => $this->input->post("enable_web"),
            "enable_email" => $this->input->post("enable_email"),
            "notify_to_team" => "",
            "notify_to_team_members" => "",
            "notify_to_terms" => "",
        );


        //get post data and prepare notificaton terms
        $notify_to_terms_list = $this->Notification_settings_model->notify_to_terms();
        $notify_to_terms = "";

        foreach ($notify_to_terms_list as $key => $term) {

            if ($term == "team") {
                $data["notify_to_team"] = $this->input->post("team"); //set team
            } else if ($term == "team_members") {
                $data["notify_to_team_members"] = $this->input->post("team_members"); //set team members
            } else {
                //prepare comma separated terms
                $other_term = $this->input->post($term);

                if ($other_term) {
                    if ($notify_to_terms) {
                        $notify_to_terms .= ",";
                    }

                    $notify_to_terms .= $term;
                }
            }
        }


        $data["notify_to_terms"] = $notify_to_terms;


        $save_id = $this->Notification_settings_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_notification_list_data($save_id), 'id' => $save_id, 'message' => lang('settings_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function modules() {
        $this->template->rander("settings/modules");
    }

    function save_module_settings() {

        $settings = array("module_timeline", "module_event", "module_todo", "module_note", "module_message", "module_chat", "module_invoice", "module_expense", "module_attendance", "module_leave", "module_estimate", "module_estimate_request", "module_lead", "module_ticket", "module_announcement", "module_project_timesheet", "module_help", "module_knowledge_base", "module_gantt");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* show the cron job tab */

    function cron_job() {
        $this->template->rander("settings/cron_job");
    }

    /* show the integration tab */

    function integration($tab = "") {
        $view_data["tab"] = $tab;
        $this->template->rander("settings/integration/index", $view_data);
    }

    /* load content in reCAPTCHA tab */

    function re_captcha() {
        $this->load->view("settings/integration/re_captcha");
    }

    /* save reCAPTCHA settings */

    function save_re_captcha_settings() {

        $settings = array("re_captcha_site_key", "re_captcha_secret_key");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* show the ticket settings tab */

    function tickets() {
        $this->load->view("settings/tickets/index");
    }

    /* save ticket settings */

    function save_ticket_settings() {

        $settings = array("show_recent_ticket_comments_at_the_top", "ticket_prefix", "project_reference_in_tickets", "auto_close_ticket_after");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    //show task settings
    function tasks() {
        $this->load->view("settings/tasks");
    }

    /* show imap settings tab */

    function imap_settings() {
        $this->load->view("settings/tickets/imap_settings");
    }

    /* push notification integration settings tab */

    function push_notification() {
        $this->load->view("settings/integration/push_notification/index");
    }

    //save task settings
    function save_task_settings() {

        $settings = array("project_task_reminder_on_the_day_of_deadline", "project_task_deadline_pre_reminder", "project_task_deadline_overdue_reminder", "enable_recurring_option_for_tasks", "task_point_range");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* save imap settings */

    function save_imap_settings() {
        $settings = array("enable_email_piping", "create_tickets_only_by_registered_emails", "imap_ssl_enabled", "imap_host", "imap_port", "imap_email", "imap_password");

        $enable_email_piping = $this->input->post("enable_email_piping");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            //if user change credentials, flag imap as unauthorized
            if (get_setting("imap_authorized") && $setting != "enable_email_piping" && $enable_email_piping && (($setting == "imap_password" && decode_password(get_setting('imap_password'), "imap_password") != $value) || get_setting($setting) != $value)) {
                $this->Settings_model->save_setting("imap_authorized", "0");
            }

            if ($setting == "imap_password") {
                $value = encode_id($value, "imap_password");
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* save push notification settings */

    function save_push_notification_settings() {
        $settings = array("enable_push_notification", "pusher_app_id", "pusher_key", "pusher_secret", "pusher_cluster");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* show the google drive settings tab */

    function google_drive() {
        $this->load->view("settings/integration/google_drive");
    }

    /* save google drive settings */

    function save_google_drive_settings() {
        $settings = array("enable_google_drive_api_to_upload_file", "google_drive_client_id", "google_drive_client_secret");

        $enable_google_drive = $this->input->post("enable_google_drive_api_to_upload_file");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            //if user change credentials, flag google drive as unauthorized
            if (get_setting("google_drive_authorized") && ($setting == "google_drive_client_id" || $setting == "google_drive_client_secret") && $enable_google_drive && get_setting($setting) != $value) {
                $this->Settings_model->save_setting("google_drive_authorized", "0");
            }

            $this->Settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    //authorize imap
    function authorize_imap() {
        if (get_setting("enable_email_piping")) {
            $this->load->library("imap");

            if (!$this->imap->authorize_imap_and_get_inbox()) {
                $this->session->set_flashdata("error_message", lang("imap_error_credentials_message"));
            }
            redirect("ticket_types/index/imap");
        }
    }

    function estimates() {
        $estimate_info = $this->Estimates_model->get_estimate_last_id();
        $view_data["last_id"] = $estimate_info;

        $this->template->rander("settings/estimates", $view_data);
    }

    function save_estimate_settings() {
        $settings = array("estimate_logo", "estimate_color", "estimate_footer", "send_estimate_bcc_to", "initial_number_of_the_estimate");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            $saveable = true;

            if ($setting === "estimate_footer") {
                $value = decode_ajax_post_data($value);
            } else if ($setting === "estimate_logo" && $value) {
                $value = str_replace("~", ":", $value);
                $value = serialize(move_temp_file("estimate-logo.png", get_setting("system_file_path"), "", $value));
            }
            if (is_null($value)) {
                $value = "";
            }


            //don't save blank image
            if ($setting === "estimate_logo" && !$value) {
                $saveable = false;
            }

            if ($saveable) {
                if ($setting === "estimate_logo") {
                    //delete old file
                    delete_app_files(get_setting("system_file_path"), get_system_files_setting_value("estimate_logo"));
                }

                $this->Settings_model->save_setting($setting, $value);
            }

            if ($saveable) {
                $this->Settings_model->save_setting($setting, $value);
            }

            if ($setting === "initial_number_of_the_estimate") {
                $this->Estimates_model->save_initial_number_of_estimate($value);
            }
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    //show a demo push notification
    function test_push_notification() {
        $this->load->helper('notifications');
        if (send_push_notifications("test_push_notification", $this->login_user->id, $this->login_user->id)) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('push_notification_error_message')));
        }
    }

    /* show timesheet settings tab */

    function timesheets() {
        $this->template->rander("settings/timesheets");
    }

    /* save timesheet settings */

    function save_timesheet_settings() {

        $users_can_start_multiple_timers_at_a_time = $this->input->post("users_can_start_multiple_timers_at_a_time");
        $users_can_start_multiple_timers_at_a_time = is_null($users_can_start_multiple_timers_at_a_time) ? "" : $users_can_start_multiple_timers_at_a_time;

        $this->Settings_model->save_setting("users_can_start_multiple_timers_at_a_time", $users_can_start_multiple_timers_at_a_time);

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function gdpr() {
        $this->template->rander("settings/gdpr");
    }

    function save_gdpr_settings() {
        $settings = array("enable_gdpr", "allow_clients_to_export_their_data", "clients_can_request_account_removal", "show_terms_and_conditions_in_client_signup_page", "gdpr_terms_and_conditions_link");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function footer() {
        //check available menus
        $footer_menus_data = "";

        $footer_menus = unserialize(get_setting("footer_menus"));
        if ($footer_menus && is_array($footer_menus)) {
            foreach ($footer_menus as $footer) {
                $footer_menus_data .= $this->_make_footer_menu_item_data($footer->menu_name, $footer->url);
            }
        }

        $view_data["footer_menus"] = $footer_menus_data;

        $this->template->rander("settings/footer/index", $view_data);
    }

    private function _make_footer_menu_item_data($menu_name, $url, $type = "") {
        $edit = modal_anchor(get_uri("settings/footer_item_edit_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "pull-right mr5 footer-menu-edit-btn", "title" => lang('edit_footer_menu'), "data-post-menu_name" => $menu_name, "data-post-url" => $url));
        $delete = "<span class='footer-menu-delete-btn'><i class='fa fa-times pull-right p3 clickable'></i></span>";

        if ($type == "data") {
            return "<a href='$url' target='_blank'>$menu_name</a>" . $delete . $edit;
        } else {
            return "<div class='list-group-item footer-menu-item' data-footer_menu_temp_id='" . rand(2000, 400000000) . "'><a href='$url' target='_blank'>$menu_name</a>" . $delete . $edit . "</div>";
        }
    }

    function save_footer_settings() {
        $settings = array("enable_footer", "footer_menus", "footer_copyright_text");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            if ($setting == "footer_menus") {
                $value = json_decode($value);
                $value = $value ? serialize($value) : serialize(array());
            }

            $this->Settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function footer_item_edit_modal_form() {
        $model_info = new stdClass();
        $model_info->menu_name = $this->input->post("menu_name");
        $model_info->url = $this->input->post("url");

        $view_data["model_info"] = $model_info;

        $this->load->view("settings/footer/modal_form", $view_data);
    }

    function save_footer_menu() {
        $menu_name = $this->input->post("menu_name");
        $url = $this->input->post("url");
        $type = $this->input->post("type");

        if ($menu_name && $url) {
            echo json_encode(array("success" => true, 'data' => $this->_make_footer_menu_item_data($menu_name, $url, $type)));
        }
    }

    function estimate_request_settings() {
        $hidden_fields = array(
            "first_name",
            "last_name",
            "email",
            "address",
            "city",
            "state",
            "zip",
            "country",
            "phone"
        );

        $hidden_fields_dropdown = array();
        foreach ($hidden_fields as $hidden_field) {
            $hidden_fields_dropdown[] = array("id" => $hidden_field, "text" => lang($hidden_field));
        }

        $view_data['hidden_fields_dropdown'] = json_encode($hidden_fields_dropdown);
        $this->load->view("settings/estimate_requests", $view_data);
    }

    function save_estimate_request_settings() {
        $settings = array(
            "hidden_client_fields_on_public_estimate_requests"
        );

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (is_null($value)) {
                $value = "";
            }

            //email can't be shown without first name or last name
            $value_array = explode(',', $value);
            if (in_array("first_name", $value_array) && in_array("last_name", $value_array) && !in_array("email", $value_array)) {
                echo json_encode(array("success" => false, 'message' => lang("estimate_request_name_email_error_message")));
                return false;
            }

            $this->Settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

}

/* End of file general_settings.php */
/* Location: ./application/controllers/general_settings.php */