<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("lead");
    }

    /* load leads list view */

    function index() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['lead_statuses'] = $this->Lead_status_model->get_details()->result();
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->result();
        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");

        $this->template->rander("leads/index", $view_data);
    }

    /* load lead add/edit modal */

    function modal_form() {
        $lead_id = $this->input->post('id');
        $view_data = $this->make_lead_modal_form_data($lead_id);
        $this->load->view('leads/modal_form', $view_data);
    }

    private function make_lead_modal_form_data($lead_id = 0) {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the lead's details view
        $view_data['model_info'] = $this->Clients_model->get_one($lead_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

        $view_data['statuses'] = $this->Lead_status_model->get_details()->result();
        $view_data['sources'] = $this->Lead_source_model->get_details()->result();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $lead_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        return $view_data;
    }

    //get owners dropdown
    //owner will be team member
    private function _get_owners_dropdown($view_type = "") {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->result();
        $team_members_dropdown = array();

        if ($view_type == "filter") {
            $team_members_dropdown = array(array("id" => "", "text" => "- " . lang("owner") . " -"));
        }

        foreach ($team_members as $member) {
            $team_members_dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
        }

        return $team_members_dropdown;
    }

    /* insert or update a lead */

    function save() {
        $client_id = $this->input->post('id');

        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required"
        ));

        $data = array(
            "company_name" => $this->input->post('company_name'),
            "address" => $this->input->post('address'),
            "city" => $this->input->post('city'),
            "state" => $this->input->post('state'),
            "zip" => $this->input->post('zip'),
            "country" => $this->input->post('country'),
            "phone" => $this->input->post('phone'),
            "website" => $this->input->post('website'),
            "vat_number" => $this->input->post('vat_number'),
            "currency_symbol" => $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "",
            "currency" => $this->input->post('currency') ? $this->input->post('currency') : "",
            "is_lead" => 1,
            "lead_status_id" => $this->input->post('lead_status_id'),
            "lead_source_id" => $this->input->post('lead_source_id'),
            "owner_id" => $this->input->post('owner_id') ? $this->input->post('owner_id') : $this->login_user->id
        );


        if (!$client_id) {
            $data["created_date"] = get_current_utc_time();
        }


        $data = clean_data($data);

        $save_id = $this->Clients_model->save($data, $client_id);
        if ($save_id) {
            save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            if (!$client_id) {
                log_notification("lead_created", array("lead_id" => $save_id), $this->login_user->id);
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a lead */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        if ($this->Clients_model->delete_client_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    /* list of leads, prepared for datatable  */

    function list_data() {
        $this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "custom_fields" => $custom_fields,
            "leads_only" => true,
            "status" => $this->input->post('status'),
            "source" => $this->input->post('source'),
            "owner_id" => $this->input->post('owner_id')
        );
        $list_data = $this->Clients_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of lead list table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields,
            "leads_only" => true
        );
        $data = $this->Clients_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of lead list table */

    private function _make_row($data, $custom_fields) {
        //primary contact 
        $image_url = get_avatar($data->contact_avatar);
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = get_lead_contact_profile_link($data->primary_contact_id, $contact);

        //lead owner
        $owner = "-";
        if ($data->owner_id) {
            $owner_image_url = get_avatar($data->owner_avatar);
            $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->owner_name";
            $owner = get_team_member_profile_link($data->owner_id, $owner_user);
        }

        $row_data = array(
            anchor(get_uri("leads/view/" . $data->id), $data->company_name),
            $data->primary_contact ? $primary_contact : "",
            $owner
        );

        $row_data[] = js_anchor($data->lead_status_title, array("style" => "background-color: $data->lead_status_color", "class" => "label", "data-id" => $data->id, "data-value" => $data->lead_status_id, "data-act" => "update-lead-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = modal_anchor(get_uri("leads/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_lead'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_lead'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("leads/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load lead details view */

    function view($client_id = 0, $tab = "") {
        $this->check_module_availability("module_lead");
        $this->access_only_allowed_members();

        if ($client_id) {
            $options = array("id" => $client_id);
            $lead_info = $this->Clients_model->get_details($options)->row();
            if ($lead_info && $lead_info->is_lead) {

                $access_info = $this->get_access_info("estimate");
                $view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("estimate_request");
                $view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_info->access_type == "all") ? true : false;

                /*
                  $access_info = $this->get_access_info("ticket");
                  $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_info->access_type == "all") ? true : false;
                 */

                $view_data["show_ticket_info"] = false; //don't show tickets for now.

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $view_data['lead_info'] = $lead_info;

                $view_data["tab"] = $tab;

                $this->template->rander("leads/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["lead_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("estimates", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("leads/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("leads/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("leads/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("lead");
            $this->load->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id) {

        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->result();
        $view_data['client_id'] = $client_id;
        $this->load->view("leads/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->input->post('id'));
        $client_id = $this->input->post('client_id') ? $this->input->post('client_id') : $view_data['model_info']->client_id;

        $this->access_only_allowed_members();

        $view_data['client_id'] = $client_id;
        $this->load->view('leads/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');
        $this->access_only_allowed_members();


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "client_id" => $client_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("leads/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("leads/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

        $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("leads/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->client_id) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = false;

            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("client", $file_info->client_id));

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            $this->load->view("leads/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for lead */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->check_module_availability("module_lead");
        $this->access_only_allowed_members();

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['lead_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = $tab;
        if ($view_data['user_info']->user_type === "lead") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            $this->template->rander("leads/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    /* load contacts tab  */

    function contacts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("leads/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->input->post('client_id');

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();
        $this->load->view('leads/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members();

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('leads/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members();

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $view_data['statuses'] = $this->Lead_status_model->get_details()->result();
            $view_data['sources'] = $this->Lead_source_model->get_details()->result();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";

            $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

            $this->load->view('leads/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members();

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "lead";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $this->load->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->input->post('contact_id');
        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members();

        $user_data = array(
            "first_name" => $this->input->post('first_name'),
            "last_name" => $this->input->post('last_name'),
            "phone" => $this->input->post('phone'),
            "skype" => $this->input->post('skype'),
            "job_title" => $this->input->post('job_title'),
            "gender" => $this->input->post('gender'),
            "note" => $this->input->post('note'),
            "user_type" => "lead"
        );

        validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric",
            "email" => "required|valid_email"
        ));

        $user_data["email"] = trim($this->input->post('email'));

        //validate duplicate email address
        if ($this->Users_model->is_email_exists($user_data["email"])) {
            echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
            exit();
        }

        if (!$contact_id) {
            //inserting new contact. client_id is required
            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["created_at"] = get_current_utc_time();
        }

        //by default, the first contact of a lead is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->input->post('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Users_model->save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("lead_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->save($user_data, $primary_contact);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $this->access_only_allowed_members();

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->input->post('facebook'),
            "twitter" => $this->input->post('twitter'),
            "linkedin" => $this->input->post('linkedin'),
            "googleplus" => $this->input->post('googleplus'),
            "digg" => $this->input->post('digg'),
            "youtube" => $this->input->post('youtube'),
            "pinterest" => $this->input->post('pinterest'),
            "instagram" => $this->input->post('instagram'),
            "github" => $this->input->post('github'),
            "tumblr" => $this->input->post('tumblr'),
            "vine" => $this->input->post('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => lang('record_updated')));
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members();

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->input->post("profile_image"));

        if ($profile_image) {
            $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image);
            $image_data = array("image" => $profile_image);
            $this->Users_model->save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name);
                $image_data = array("image" => $profile_image);
                $this->Users_model->save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->input->post('id');

        if ($this->input->post('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        $this->access_only_allowed_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "lead", "client_id" => $client_id, "custom_fields" => $custom_fields);
        $list_data = $this->Users_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "lead",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->row();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='label-info label'>" . lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("leads/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "lead") {
            $contact_link = $full_name; //don't show clickable link to lead
        }


        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("leads/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* upadate a lead status */

    function save_lead_status($id = 0) {
        $this->access_only_allowed_members();

        $data = array(
            "lead_status_id" => $this->input->post('value')
        );

        $save_id = $this->Clients_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    function all_leads_kanban() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->result();

        $this->template->rander("leads/kanban/all_leads", $view_data);
    }

    function all_leads_kanban_data() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $options = array(
            "status" => $this->input->post('status'),
            "owner_id" => $this->input->post('owner_id'),
            "source" => $this->input->post('source'),
        );

        $view_data["leads"] = $this->Clients_model->get_leads_kanban_details($options)->result();

        $statuses = $this->Lead_status_model->get_details();
        $view_data["total_columns"] = $statuses->num_rows();
        $view_data["columns"] = $statuses->result();

        $this->load->view('leads/kanban/kanban_view', $view_data);
    }

    function save_lead_sort_and_status() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        $lead_status_id = $this->input->post('lead_status_id');
        $data = array(
            "sort" => $this->input->post('sort')
        );

        if ($lead_status_id) {
            $data["lead_status_id"] = $lead_status_id;
        }

        $this->Clients_model->save($data, $id);
    }

    function make_client_modal_form($lead_id = 0) {
        $this->access_only_allowed_members();

        //prepare company details
        $view_data["lead_info"] = $this->make_lead_modal_form_data($lead_id);
        $view_data["lead_info"]["to_custom_field_type"] = "clients";

        //prepare contacts info
        $final_contacts = array();
        $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $lead_id))->result();

        //add custom fields for contacts
        foreach ($contacts as $contact) {
            $contact->custom_fields = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact->id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $final_contacts[] = $contact;
        }

        $view_data["contacts"] = $final_contacts;

        $this->load->view('leads/migration/modal_form', $view_data);
    }

    function save_as_client() {
        $this->access_only_allowed_members();

        $client_id = $this->input->post('main_client_id');

        if ($client_id) {
            //save client info
            validate_submitted_data(array(
                "main_client_id" => "numeric",
                "company_name" => "required"
            ));

            $company_name = $this->input->post('company_name');

            $client_info = $this->Clients_model->get_details(array("id" => $client_id))->row();

            $data = array(
                "company_name" => $company_name,
                "address" => $this->input->post('address'),
                "city" => $this->input->post('city'),
                "state" => $this->input->post('state'),
                "zip" => $this->input->post('zip'),
                "country" => $this->input->post('country'),
                "phone" => $this->input->post('phone'),
                "website" => $this->input->post('website'),
                "vat_number" => $this->input->post('vat_number'),
                "group_ids" => $this->input->post('group_ids') ? $this->input->post('group_ids') : "",
                "is_lead" => 0,
                "client_migration_date" => get_current_utc_time(),
                "last_lead_status" => $client_info->lead_status_title
            );

            if ($this->login_user->is_admin) {
                $data["currency_symbol"] = $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "";
                $data["currency"] = $this->input->post('currency') ? $this->input->post('currency') : "";
                $data["disable_online_payment"] = $this->input->post('disable_online_payment') ? $this->input->post('disable_online_payment') : 0;
            }

            $data = clean_data($data);

            //check duplicate company name, if found then show an error message
            if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($company_name, $client_id)) {
                echo json_encode(array("success" => false, 'message' => lang("account_already_exists_for_your_company_name")));
                exit();
            }

            $save_client_id = $this->Clients_model->save($data, $client_id);

            //save contacts
            if ($save_client_id) {
                log_notification("client_created_from_lead", array("client_id" => $save_client_id), $this->login_user->id);

                //save custom field for client
                if ($this->input->post("merge_custom_fields-$client_id")) {
                    save_custom_fields("leads", $save_client_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "clients");
                }

                $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $client_id))->result();
                $found_primary_contact = false;

                foreach ($contacts as $contact) {
                    validate_submitted_data(array(
                        'first_name-' . $contact->id => "required",
                        'last_name-' . $contact->id => "required",
                        'email-' . $contact->id => "required|valid_email"
                    ));

                    $user_data = array(
                        "first_name" => $this->input->post('first_name-' . $contact->id),
                        "last_name" => $this->input->post('last_name-' . $contact->id),
                        "phone" => $this->input->post('contact_phone-' . $contact->id),
                        "skype" => $this->input->post('skype-' . $contact->id),
                        "job_title" => $this->input->post('job_title-' . $contact->id),
                        "gender" => $this->input->post('gender-' . $contact->id),
                        "email" => trim($this->input->post('email-' . $contact->id)),
                        "password" => md5($this->input->post('login_password-' . $contact->id)),
                        "user_type" => "client"
                    );

                    if ($this->input->post('is_primary_contact_value-' . $contact->id) && !$found_primary_contact) {
                        $user_data["is_primary_contact"] = 1;
                        $found_primary_contact = true; //flag that, a primary contact found
                    } else {
                        $user_data["is_primary_contact"] = 0;
                    }

                    if ($this->Users_model->is_email_exists($user_data["email"], $contact->id)) {
                        echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
                        exit();
                    }

                    $user_data = clean_data($user_data);

                    $save_contact_id = $this->Users_model->save($user_data, $contact->id);

                    if ($save_contact_id) {
                        //save custom fields for client contacts
                        if ($this->input->post("merge_custom_fields-$contact->id")) {
                            save_custom_fields("lead_contacts", $save_contact_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "client_contacts", $contact->id);
                        }

                        if ($this->input->post('email_login_details-' . $contact->id)) {
                            $email_template = $this->Email_templates_model->get_final_template("login_info");

                            $parser_data["SIGNATURE"] = $email_template->signature;
                            $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                            $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                            $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                            $parser_data["USER_LOGIN_PASSWORD"] = $this->input->post('login_password-' . $contact->id);
                            $parser_data["DASHBOARD_URL"] = base_url();
                            $parser_data["LOGO_URL"] = get_logo_url();

                            $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
                            send_app_mail($this->input->post('email-' . $contact->id), $email_template->subject, $message);
                        }
                    }
                }

                echo json_encode(array("success" => true, 'redirect_to' => get_uri("clients/view/$save_client_id"), "message" => lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        }
    }

}

/* End of file leads.php */
/* Location: ./application/controllers/leads.php */