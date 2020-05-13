<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projects extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Project_settings_model");
        $this->load->model("Checklist_items_model");
    }

    private function can_edit_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_projects") == "1") {
                return true;
            }
        } else {
            if (get_setting("client_can_edit_projects")) {
                return true;
            }
        }
    }

    private function can_delete_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_projects") == "1") {
                return true;
            }
        }
    }

    private function can_add_remove_project_members() {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin) {
                return true;
            } else {
                if (get_array_value($this->login_user->permissions, "show_assigned_tasks_only") !== "1") {
                    if ($this->can_manage_all_projects()) {
                        return true;
                    } else if (get_array_value($this->login_user->permissions, "can_add_remove_project_members") == "1") {
                        return true;
                    }
                }
            }
        }
    }

    private function can_view_tasks($project_id = "", $task_id = "") {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                if ($task_id && get_array_value($this->login_user->permissions, "show_assigned_tasks_only") == "1") {
                    //user has permission to view only assigned tasks
                    $task_info = $this->Tasks_model->get_one($task_id);
                    $collaborators_array = explode(',', $task_info->collaborators);
                    if ($task_info->assigned_to == $this->login_user->id || in_array($this->login_user->id, $collaborators_array)) {
                        return true;
                    }
                } else if ($this->is_user_a_project_member) {
                    //all team members who has access to project can view tasks
                    return true;
                }
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_edit_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_edit_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_delete_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_delete_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_comment_on_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_milestones")) {
                //even the settings allow to view milestones, the client can only create their own project's milestones
                return $this->is_clients_project;
            }
        }
    }

    private function can_create_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_edit_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_files") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_add_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_files")) {
                //even the settings allow to create/edit task, the client can only comment on their own project's files
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_gantt() {
        //check gantt module
        if (get_setting("module_gantt")) {
            if ($this->login_user->user_type == "staff") {
                if ($this->can_manage_all_projects()) {
                    return true;
                } else {
                    //check is user a project member
                    return $this->is_user_a_project_member;
                }
            } else {
                //check settings for client's project permission
                if (get_setting("client_can_view_gantt")) {
                    //even the settings allow to view gantt, the client can only view on their own project's gantt
                    return $this->is_clients_project;
                }
            }
        }
    }

    /* load the project settings into ci settings */

    private function init_project_settings($project_id) {
        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->result();
        foreach ($settings as $setting) {
            $this->config->set_item($setting->setting_name, $setting->setting_value);
        }
    }

    private function can_view_timesheet($project_id = 0, $show_all_personal_timesheets = false) {
        if (!get_setting("module_project_timesheet")) {
            return false;
        }

        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {


                if ($project_id) {
                    //check is user a project member
                    return $this->is_user_a_project_member;
                } else {
                    $access_info = $this->get_access_info("timesheet_manage_permission");

                    if ($access_info->access_type == "all") {
                        return true;
                    } else if (count($access_info->allowed_members)) {
                        return true;
                    } else if ($show_all_personal_timesheets) {
                        return true;
                    }
                }
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_timesheet")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    /* load project view */

    function index() {
        redirect("projects/all_projects");
    }

    function all_projects() {
        $label_suggestions = array(array("id" => "", "text" => "- " . lang("label") . " -"));
        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $temp_labels = array();

        foreach ($labels as $label) {
            if ($label && !in_array($label, $temp_labels)) {
                $temp_labels[] = $label;
                $label_suggestions[] = array("id" => $label, "text" => $label);
            }
        }

        $view_data['project_labels_dropdown'] = json_encode($label_suggestions);

        $view_data["can_create_projects"] = $this->can_create_projects();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        if ($this->login_user->user_type === "staff") {
            $view_data["can_edit_projects"] = $this->can_edit_projects();
            $view_data["can_delete_projects"] = $this->can_delete_projects();

            $this->template->rander("projects/index", $view_data);
        } else {
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            $this->template->rander("clients/projects/index", $view_data);
        }
    }

    /* load project  add/edit modal */

    function modal_form() {
        $project_id = $this->input->post('id');
        $client_id = $this->input->post('client_id');

        if ($project_id) {
            if (!$this->can_edit_projects()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_projects()) {
                redirect("forbidden");
            }
        }


        $view_data["client_id"] = $client_id;
        $view_data['model_info'] = $this->Projects_model->get_one($project_id);
        if ($client_id) {
            $view_data['model_info']->client_id = $client_id;
        }

        //check if it's from estimate. if so, then prepare for project
        $estimate_id = $this->input->post('estimate_id');
        if ($estimate_id) {
            $view_data['model_info']->estimate_id = $estimate_id;
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("projects", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));

        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;


        $this->load->view('projects/modal_form', $view_data);
    }

    /* insert or update a project */

    function save() {

        $id = $this->input->post('id');

        if ($id) {
            if (!$this->can_edit_projects()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_projects()) {
                redirect("forbidden");
            }
        }

        validate_submitted_data(array(
            "title" => "required"
        ));

        $estimate_id = $this->input->post('estimate_id');
        $status = $this->input->post('status');

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "client_id" => $this->input->post('client_id'),
            "start_date" => $this->input->post('start_date'),
            "deadline" => $this->input->post('deadline'),
            "price" => unformat_currency($this->input->post('price')),
            "labels" => $this->input->post('labels'),
            "status" => $status ? $status : "open",
            "estimate_id" => $estimate_id
        );

        if (!$id) {
            $data["created_date"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
        }


        //created by client? overwrite the client id for safety
        if ($this->login_user->user_type === "clinet") {
            $data["client_id"] = $this->login_user->client_id;
        }


        $data = clean_data($data);


        //set null value after cleaning the data
        if (!$data["start_date"]) {
            $data["start_date"] = NULL;
        }

        if (!$data["deadline"]) {
            $data["deadline"] = NULL;
        }

        $save_id = $this->Projects_model->save($data, $id);
        if ($save_id) {

            save_custom_fields("projects", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //send notification
            if ($status == "completed") {
                log_notification("project_completed", array("project_id" => $save_id));
            }

            if (!$id) {

                if ($this->login_user->user_type === "staff") {
                    //this is a new project and created by team members
                    //add default project member after project creation
                    $data = array(
                        "project_id" => $save_id,
                        "user_id" => $this->login_user->id,
                        "is_leader" => 1
                    );
                    $this->Project_members_model->save_member($data);
                }

                //created from estimate? save the project id
                if ($estimate_id) {
                    $data = array("project_id" => $save_id);
                    $this->Estimates_model->save($data, $estimate_id);
                }

                log_notification("project_created", array("project_id" => $save_id));
            }
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* Show a modal to clone a project */

    function clone_project_modal_form() {

        $project_id = $this->input->post('id');

        if (!$this->can_create_projects()) {
            redirect("forbidden");
        }


        $view_data['model_info'] = $this->Projects_model->get_one($project_id);

        $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));

        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("projects", $view_data['model_info']->id, 1, "staff")->result(); //we have to keep this regarding as an admin user because non-admin user also can acquire the access to clone a project

        $this->load->view('projects/clone_project_modal_form', $view_data);
    }

    /* create a new project from another project */

    function save_cloned_project() {

        ini_set('max_execution_time', 300); //300 seconds 

        $project_id = $this->input->post('project_id');

        if (!$this->can_create_projects()) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "title" => "required"
        ));


        $copy_same_assignee_and_collaborators = $this->input->post("copy_same_assignee_and_collaborators");
        $copy_milestones = $this->input->post("copy_milestones");
        $copy_tasks_start_date_and_deadline = $this->input->post("copy_tasks_start_date_and_deadline");


        //prepare new project data
        $now = get_current_utc_time();
        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "client_id" => $this->input->post('client_id'),
            "start_date" => $this->input->post('start_date'),
            "deadline" => $this->input->post('deadline'),
            "price" => unformat_currency($this->input->post('price')),
            "created_date" => $now,
            "created_by" => $this->login_user->id,
            "labels" => $this->input->post('labels'),
            "status" => "open",
        );

        if (!$data["start_date"]) {
            $data["start_date"] = NULL;
        }

        if (!$data["deadline"]) {
            $data["deadline"] = NULL;
        }


        //add new project
        $new_project_id = $this->Projects_model->save($data);



        //add milestones
        //when the new milestones will be created the ids will be different. so, we have to convert the milestone ids. 
        $milestones_array = array();

        if ($copy_milestones) {
            $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
            foreach ($milestones as $milestone) {
                $old_milestone_id = $milestone->id;

                //prepare new milestone data. remove id from existing data
                $milestone->project_id = $new_project_id;
                $milestone_data = (array) $milestone;
                unset($milestone_data["id"]);

                //add new milestone and keep a relation with new id and old id
                $milestones_array[$old_milestone_id] = $this->Milestones_model->save($milestone_data);
            }
        }


        //we'll keep all new task ids vs old task ids. by this way, we'll add the checklist easily 
        $task_ids = array();

        //add tasks
        $tasks = $this->Tasks_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        foreach ($tasks as $task) {

            //prepare new task data. 
            $task->project_id = $new_project_id;
            $milestone_id = get_array_value($milestones_array, $task->milestone_id);
            $task->milestone_id = $milestone_id ? $milestone_id : "";
            $task->status = "to_do";

            if (!$copy_same_assignee_and_collaborators) {
                $task->assigned_to = "";
                $task->collaborators = "";
            }

            if (!$copy_tasks_start_date_and_deadline) {
                $task->start_date = "";
                $task->deadline = "";
            }

            $task_data = (array) $task;
            unset($task_data["id"]); //remove id from existing data
            //add new task
            $new_taks_id = $this->Tasks_model->save($task_data);

            //bind old id with new
            $task_ids[$task->id] = $new_taks_id;

            //save custom fields of task
            $old_custom_fields = $this->Custom_field_values_model->get_all_where(array("related_to_type" => "tasks", "related_to_id" => $task->id, "deleted" => 0))->result();

            //prepare new custom fields data
            foreach ($old_custom_fields as $field) {
                $field->related_to_id = $new_taks_id;

                $fields_data = (array) $field;
                unset($fields_data["id"]); //remove id from existing data
                //save custom field
                $this->Custom_field_values_model->save($fields_data);
            }
        }

        //add project members
        $project_members = $this->Project_members_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();

        foreach ($project_members as $project_member) {
            //prepare new project member data. remove id from existing data
            $project_member->project_id = $new_project_id;
            $project_member_data = (array) $project_member;
            unset($project_member_data["id"]);

            $project_member_data["user_id"] = $project_member->user_id;

            $this->Project_members_model->save_member($project_member_data);
        }

        //add check lists
        $check_lists = $this->Checklist_items_model->get_all_checklist_of_project($project_id)->result();
        foreach ($check_lists as $list) {

            $checklist_data = array(
                "title" => $list->title,
                "task_id" => $task_ids[$list->task_id],
                "is_checked" => 0
            );

            $this->Checklist_items_model->save($checklist_data);
        }

        if ($new_project_id) {
            //save custom fields of project
            save_custom_fields("projects", $new_project_id, 1, "staff"); //we have to keep this regarding as an admin user because non-admin user also can acquire the access to clone a project

            log_notification("project_created", array("project_id" => $new_project_id));

            echo json_encode(array("success" => true, 'id' => $new_project_id, 'message' => lang('project_cloned_successfully')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete a project */

    function delete() {

        if (!$this->can_delete_projects()) {
            redirect("forbidden");
        }

        $id = $this->input->post('id');

        if ($this->Projects_model->delete_project_and_sub_items($id)) {
            log_notification("project_deleted", array("project_id" => $id));

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    /* list of projcts, prepared for datatable  */

    function list_data() {
        $this->access_only_team_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $statuses = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";

        $options = array(
            "statuses" => $statuses,
            "project_label" => $this->input->post("project_label"),
            "custom_fields" => $custom_fields,
            "deadline" => $this->input->post('deadline'),
        );

        //only admin/ the user has permission to manage all projects, can see all projects, other team mebers can see only their own projects.
        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of projcts, prepared for datatable  */

    function projects_list_data_of_team_member($team_member_id = 0) {
        $this->access_only_team_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "status" => $this->input->post("status"),
            "custom_fields" => $custom_fields
        );

        //add can see all members projects but team members can see only ther own projects
        if (!$this->can_manage_all_projects() && $team_member_id != $this->login_user->id) {
            redirect("forbidden");
        }

        $options["user_id"] = $team_member_id;


        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    function projects_list_data_of_client($client_id = 0) {

        $this->access_only_team_members_or_client_contact($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $statuses = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";

        $options = array(
            "client_id" => $client_id,
            "statuses" => $statuses,
            "project_label" => $this->input->post("project_label"),
            "custom_fields" => $custom_fields
        );

        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );

        $data = $this->Projects_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of project list table */

    private function _make_row($data, $custom_fields) {

        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;

        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";
        $start_date = is_date_exists($data->start_date) ? format_to_date($data->start_date, false) : "-";
        $dateline = is_date_exists($data->deadline) ? format_to_date($data->deadline, false) : "-";
        $price = $data->price ? to_currency($data->price, $data->currency_symbol) : "-";

        //has deadline? change the color of date based on status
        if (is_date_exists($data->deadline)) {
            if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") > $data->deadline) {
                $dateline = "<span class='text-danger mr5'>" . $dateline . "</span> ";
            } else if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") == $data->deadline) {
                $dateline = "<span class='text-warning mr5'>" . $dateline . "</span> ";
            }
        }

        $title = anchor(get_uri("projects/view/" . $data->id), $data->title);
        $project_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $project_labels .= "<span class='label label-info clickable'  title='Label'>" . $label . "</span> ";
            }
            $title .= "<br />" . $project_labels;
        }

        $optoins = "";
        if ($this->can_edit_projects()) {
            $optoins .= modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_project'), "data-post-id" => $data->id));
        }

        if ($this->can_delete_projects()) {
            $optoins .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_project'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete"), "data-action" => "delete-confirmation"));
        }

        //show the project price to them who has permission to create projects
        if ($this->login_user->user_type == "staff" && !$this->can_create_projects()) {
            $price = "-";
        }


        $row_data = array(
            anchor(get_uri("projects/view/" . $data->id), $data->id),
            $title,
            anchor(get_uri("clients/view/" . $data->client_id), $data->company_name),
            $price,
            $data->start_date,
            $start_date,
            $data->deadline,
            $dateline,
            $progress_bar,
            lang($data->status)
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = $optoins;

        return $row_data;
    }

    /* load project details view */

    function view($project_id = 0, $tab = "") {

        $this->init_project_permission_checker($project_id);

        $view_data = $this->_get_project_info_data($project_id);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;

        $expense_access_info = $this->get_access_info("expense");
        $view_data["show_expense_info"] = (get_setting("module_expense") && $expense_access_info->access_type == "all") ? true : false;

        $view_data["show_actions_dropdown"] = $this->can_create_projects();

        $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;

        $view_data["show_timmer"] = get_setting("module_project_timesheet") ? true : false;

        $this->init_project_settings($project_id);
        $view_data["show_timesheet_info"] = $this->can_view_timesheet($project_id);

        $view_data["show_tasks"] = true;

        $view_data["show_gantt_info"] = $this->can_view_gantt();
        $view_data["show_milestone_info"] = $this->can_view_milestones();


        if ($this->login_user->user_type === "client") {
            $view_data["show_timmer"] = false;
            $view_data["show_tasks"] = $this->can_view_tasks();

            if (!get_setting("client_can_edit_projects")) {
                $view_data["show_actions_dropdown"] = false;
            }
        }

        $view_data["show_files"] = $this->can_view_files();

        $view_data["tab"] = $tab;

        $view_data["is_starred"] = strpos($view_data['project_info']->starred_by, ":" . $this->login_user->id . ":") ? true : false;

        $this->template->rander("projects/details_view", $view_data);
    }

    /* prepare project info data for reuse */

    private function _get_project_info_data($project_id) {
        $options = array(
            "id" => $project_id,
            "client_id" => $this->login_user->client_id,
        );

        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $project_info = $this->Projects_model->get_details($options)->row();
        $view_data['project_info'] = $project_info;

        if ($project_info) {
            $view_data['project_info'] = $project_info;
            $timer = $this->Timesheets_model->get_timer_info($project_id, $this->login_user->id)->row();
            $user_has_any_timer_except_this_project = $this->Timesheets_model->user_has_any_timer_except_this_project($project_id, $this->login_user->id);

            //disable the start timer button if the setting is disabled
            $view_data["disable_timer"] = false;
            if ($user_has_any_timer_except_this_project && !get_setting("users_can_start_multiple_timers_at_a_time")) {
                $view_data["disable_timer"] = true;
            }

            if ($timer) {
                $view_data['timer_status'] = "open";
            } else {
                $view_data['timer_status'] = "";
            }

            $view_data['project_progress'] = $project_info->total_points ? round(($project_info->completed_points / $project_info->total_points) * 100) : 0;

            return $view_data;
        } else {
            show_404();
        }
    }

    function show_my_starred_projects() {
        $view_data["projects"] = $this->Projects_model->get_starred_projects($this->login_user->id)->result();
        $this->load->view('projects/star/projects_list', $view_data);
    }

    /* load project overview section */

    function overview($project_id) {
        $this->access_only_team_members();
        $this->init_project_permission_checker($project_id);

        $view_data = $this->_get_project_info_data($project_id);
        $view_data["task_statuses"] = $this->Tasks_model->get_task_statistics(array("project_id" => $project_id));


        $view_data['project_id'] = $project_id;
        $offset = 0;
        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => "project", "log_for_id" => $project_id, "limit" => 20, "offset" => $offset);

        $view_data["can_add_remove_project_members"] = $this->can_add_remove_project_members();
        $view_data["can_access_clients"] = $this->can_access_clients();

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("projects", $project_id, $this->login_user->is_admin, $this->login_user->user_type)->result();


        $this->load->view('projects/overview', $view_data);
    }

    private function can_access_clients() {
        if (get_setting("client_can_view_tasks")) {
            if ($this->login_user->is_admin) {
                return true;
            } else if ($this->login_user->user_type == "staff" && get_array_value($this->login_user->permissions, "client") && get_array_value($this->login_user->permissions, "show_assigned_tasks_only") !== "1") {
                return true;
            }
        }
    }

    /* add-remove start mark from project */

    function add_remove_star($project_id, $type = "add") {
        if ($project_id) {
            $view_data["project_id"] = $project_id;

            if ($type === "add") {
                $this->Projects_model->add_remove_star($project_id, $this->login_user->id, $type = "add");
                $this->load->view('projects/star/starred', $view_data);
            } else {
                $this->Projects_model->add_remove_star($project_id, $this->login_user->id, $type = "remove");
                $this->load->view('projects/star/not_starred', $view_data);
            }
        }
    }

    /* load project overview section */

    function overview_for_client($project_id) {
        if ($this->login_user->user_type === "client") {
            $view_data = $this->_get_project_info_data($project_id);

            $view_data['project_id'] = $project_id;

            $offset = 0;
            $view_data['offset'] = $offset;
            $view_data['show_activity'] = false;
            $view_data['show_overview'] = false;
            $view_data['activity_logs_params'] = array();

            if (get_setting("client_can_view_overview")) {
                $view_data['show_overview'] = true;
                $view_data["task_statuses"] = $this->Tasks_model->get_task_statistics(array("project_id" => $project_id));

                if (get_setting("client_can_view_activity")) {
                    $view_data['show_activity'] = true;
                    $view_data['activity_logs_params'] = array("log_for" => "project", "log_for_id" => $project_id, "limit" => 20, "offset" => $offset);
                }
            }

            $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("projects", $project_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $this->load->view('projects/overview_for_client', $view_data);
        }
    }

    /* load project members add/edit modal */

    function project_member_modal_form() {
        $view_data['model_info'] = $this->Project_members_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $add_user_type = $this->input->post("add_user_type");

        $users_dropdown = array();
        if ($add_user_type == "client_contacts") {
            if (!$this->can_access_clients()) {
                redirect("forbidden");
            }

            $contacts = $this->Project_members_model->get_client_contacts_of_the_project_client($project_id)->result();
            foreach ($contacts as $contact) {
                $users_dropdown[$contact->id] = $contact->contact_name;
            }
        } else {
            $users = $this->Project_members_model->get_rest_team_members_for_a_project($project_id)->result();
            foreach ($users as $user) {
                $users_dropdown[$user->id] = $user->member_name;
            }
        }

        $view_data["users_dropdown"] = $users_dropdown;
        $view_data["add_user_type"] = $add_user_type;

        $this->load->view('projects/project_members/modal_form', $view_data);
    }

    /* add a project members  */

    function save_project_member() {
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "user_id[]" => "required"
        ));


        $user_ids = $this->input->post('user_id');


        $save_ids = array();
        $already_exists = false;

        if ($user_ids) {
            foreach ($user_ids as $user_id) {
                if ($user_id) {
                    $data = array(
                        "project_id" => $project_id,
                        "user_id" => $user_id
                    );

                    $save_id = $this->Project_members_model->save_member($data);
                    if ($save_id && $save_id != "exists") {
                        $save_ids[] = $save_id;
                        log_notification("project_member_added", array("project_id" => $project_id, "to_user_id" => $user_id));
                    } else if ($save_id === "exists") {
                        $already_exists = true;
                    }
                }
            }
        }


        if (!count($save_ids) && $already_exists) {
            //this member already exists.
            echo json_encode(array("success" => true, 'id' => "exists"));
        } else if (count($save_ids)) {
            $project_member_row = array();
            foreach ($save_ids as $id) {
                $project_member_row[] = $this->_project_member_row_data($id);
            }
            echo json_encode(array("success" => true, "data" => $project_member_row, 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a project members  */

    function delete_project_member() {
        $id = $this->input->post('id');
        $project_member_info = $this->Project_members_model->get_one($id);

        $this->init_project_permission_checker($project_member_info->project_id);
        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }


        if ($this->input->post('undo')) {
            if ($this->Project_members_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_project_member_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Project_members_model->delete($id)) {

                $project_member_info = $this->Project_members_model->get_one($id);

                log_notification("project_member_deleted", array("project_id" => $project_member_info->project_id, "to_user_id" => $project_member_info->user_id));
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of project members, prepared for datatable  */

    function project_member_list_data($project_id = 0, $user_type = "") {
        $this->access_only_team_members();
        $this->init_project_permission_checker($project_id);


        //show the message icon to client contacts list only if the user can send message to client. 
        $can_send_message_to_client = false;
        $client_message_users = get_setting("client_message_users");
        $client_message_users_array = explode(",", $client_message_users);
        if (in_array($this->login_user->id, $client_message_users_array)) {

            $can_send_message_to_client = true;
        }

        $options = array("project_id" => $project_id, "user_type" => $user_type, "show_user_wise" => true);
        $list_data = $this->Project_members_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_project_member_row($data, $can_send_message_to_client);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project member list */

    private function _project_member_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_members_model->get_details($options)->row();
        return $this->_make_project_member_row($data);
    }

    /* prepare a row of project member list */

    private function _make_project_member_row($data, $can_send_message_to_client = false) {
        $member_image = "<span class='avatar avatar-xs mr10'><img src='" . get_avatar($data->member_image) . "' alt='...'></span> " . $data->member_name;

        if ($data->user_type == "staff") {
            $member = get_team_member_profile_link($data->user_id, $member_image);
        } else {
            $member = get_client_contact_profile_link($data->user_id, $member_image);
        }

        $link = "";

        //check message module availability and show message button
        if (get_setting("module_message") && ($this->login_user->id != $data->user_id)) {
            $link = modal_anchor(get_uri("messages/modal_form/" . $data->user_id), "<i class='fa fa-envelope-o'></i>", array("class" => "edit", "title" => lang('send_message')));
        }

        //check message icon permission for client contacts
        if (!$can_send_message_to_client && $data->user_type === "client") {
            $link = "";
        }


        if ($this->can_add_remove_project_members()) {
            $delete_link = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_member'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_project_member"), "data-action" => "delete"));

            if (!$this->can_manage_all_projects() && ($this->login_user->id === $data->user_id)) {
                $delete_link = "";
            }
            $link .= $delete_link;
        }

        $member = '<div class="pull-left">' . $member . '</div><div class="pull-right"><label class="label label-light ml10">' . $data->job_title . '</label></div>';

        return array($member, $link);
    }

    //stop timer note modal
    function stop_timer_modal_form($project_id) {
        $this->access_only_team_members();

        if ($project_id) {
            $view_data["project_id"] = $project_id;
            $view_data["tasks_dropdown"] = $this->_get_timesheet_tasks_dropdown($project_id);

            $this->load->view('projects/timesheets/stop_timer_modal_form', $view_data);
        }
    }

    //show timer note modal
    function timer_note_modal_form() {

        $id = $this->input->post("id");
        if ($id) {
            $model_info = $this->Timesheets_model->get_one($id);

            $this->init_project_permission_checker($model_info->project_id);
            $this->init_project_settings($model_info->project_id); //since we'll check this permission project wise


            if (!$this->can_view_timesheet($model_info->project_id)) {
                redirect("forbidden");
            }

            $view_data["model_info"] = $model_info;
            $this->load->view('projects/timesheets/note_modal_form', $view_data);
        }
    }

    private function _get_timesheet_tasks_dropdown($project_id, $return_json = false) {
        $tasks_dropdown = array("" => "-");
        $tasks_dropdown_json = array(array("id" => "", "text" => "- " . lang("task") . " -"));

        $options = array(
            "project_id" => $project_id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );

        $tasks = $this->Tasks_model->get_details($options)->result();

        foreach ($tasks as $task) {
            $tasks_dropdown_json[] = array("id" => $task->id, "text" => $task->id . "-" . $task->title);
            $tasks_dropdown[$task->id] = $task->id . " - " . $task->title;
        }

        if ($return_json) {
            return json_encode($tasks_dropdown_json);
        } else {
            return $tasks_dropdown;
        }
    }

    /* start/stop project timer */

    function timer($project_id, $timer_status = "start") {
        $this->access_only_team_members();
        $note = $this->input->post("note");
        $task_id = $this->input->post("task_id");

        $data = array(
            "project_id" => $project_id,
            "user_id" => $this->login_user->id,
            "status" => $timer_status,
            "note" => $note ? $note : "",
            "task_id" => $task_id ? $task_id : 0,
        );

        $user_has_any_timer_except_this_project = $this->Timesheets_model->user_has_any_timer_except_this_project($project_id, $this->login_user->id);

        if ($timer_status == "start" && $user_has_any_timer_except_this_project && !get_setting("users_can_start_multiple_timers_at_a_time")) {
            redirect("forbidden");
        }

        $this->Timesheets_model->process_timer($data);
        if ($timer_status === "start") {
            $view_data = $this->_get_project_info_data($project_id);
            $this->load->view('projects/project_timer', $view_data);
        } else {
            echo json_encode(array("success" => true));
        }
    }

    /* load timesheets view for a project */

    function timesheets($project_id) {

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise


        if (!$this->can_view_timesheet($project_id)) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;

        //client can't add log or update settings
        $view_data['can_add_log'] = false;
        $view_data['can_update_settings'] = false;

        if ($this->login_user->user_type === "staff") {
            $view_data['can_add_log'] = true;
            $view_data['can_update_settings'] = $this->can_create_projects(); //settings can update only the allowed members
        }

        $view_data['project_members_dropdown'] = json_encode($this->_get_project_members_dropdown_list_for_filter($project_id));
        $view_data['tasks_dropdown'] = $this->_get_timesheet_tasks_dropdown($project_id, true);

        $this->load->view("projects/timesheets/index", $view_data);
    }

    /* prepare project members dropdown */

    private function _get_project_members_dropdown_list_for_filter($project_id) {

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result();
        $project_members_dropdown = array(array("id" => "", "text" => "- " . lang("member") . " -"));
        foreach ($project_members as $member) {
            $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
        }
        return $project_members_dropdown;
    }

    /* load timelog add/edit modal */

    function timelog_modal_form() {
        $this->access_only_team_members();
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;
        $model_info = $this->Timesheets_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $model_info->project_id;

        //set the login user as a default selected member
        if (!$model_info->user_id) {
            $model_info->user_id = $this->login_user->id;
        }

        //get related data
        $related_data = $this->_prepare_all_related_data_for_timelog($project_id);
        $show_porject_members_dropdown = get_array_value($related_data, "show_porject_members_dropdown");
        $view_data["tasks_dropdown"] = get_array_value($related_data, "tasks_dropdown");
        $view_data["project_members_dropdown"] = get_array_value($related_data, "project_members_dropdown");

        $view_data["model_info"] = $model_info;

        if ($model_info->id) {
            $show_porject_members_dropdown = false; //don't allow to edit the user on update.
        }

        $view_data["project_id"] = $project_id;
        $view_data['show_porject_members_dropdown'] = $show_porject_members_dropdown;
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown();

        $this->load->view('projects/timesheets/modal_form', $view_data);
    }

    private function _prepare_all_related_data_for_timelog($project_id = 0) {
        //we have to check if any defined project exists, then go through with the project id
        $show_porject_members_dropdown = false;
        if ($project_id) {
            $tasks_dropdown = $this->_get_timesheet_tasks_dropdown($project_id, true);

            //prepare members dropdown list
            $allowed_members = $this->_get_members_to_manage_timesheet();
            $project_members = "";

            if ($allowed_members === "all") {
                $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result(); //get all members of this project
            } else {
                $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id, $allowed_members)->result();
            }

            $project_members_dropdown = array();
            if ($project_members) {
                foreach ($project_members as $member) {

                    if ($member->user_id !== $this->login_user->id) {
                        $show_porject_members_dropdown = true; //user can manage other users time.
                    }

                    $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
                }
            }
        } else {
            //we have show an empty dropdown when there is no project_id defined
            $tasks_dropdown = json_encode(array(array("id" => "", "text" => "-")));
            $project_members_dropdown = array(array("id" => "", "text" => "-"));
            $show_porject_members_dropdown = true;
        }

        return array(
            "project_members_dropdown" => $project_members_dropdown,
            "tasks_dropdown" => $tasks_dropdown,
            "show_porject_members_dropdown" => $show_porject_members_dropdown
        );
    }

    function get_all_related_data_of_selected_project_for_timelog($project_id = "") {
        if ($project_id) {
            $related_data = $this->_prepare_all_related_data_for_timelog($project_id);

            echo json_encode(array(
                "project_members_dropdown" => get_array_value($related_data, "project_members_dropdown"),
                "tasks_dropdown" => json_decode(get_array_value($related_data, "tasks_dropdown"))
            ));
        }
    }

    /* insert/update a timelog */

    function save_timelog() {
        $this->access_only_team_members();
        $id = $this->input->post('id');

        //convert to 24hrs time format
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');
        $note = $this->input->post("note");
        $task_id = $this->input->post("task_id");


        if (get_setting("time_format") != "24_hours") {
            $start_time = convert_time_to_24hours_format($start_time);
            $end_time = convert_time_to_24hours_format($end_time);
        }

        //join date with time
        $start_date_time = $this->input->post('start_date') . " " . $start_time;
        $end_date_time = $this->input->post('end_date') . " " . $end_time;

        //add time offset
        $start_date_time = convert_date_local_to_utc($start_date_time);
        $end_date_time = convert_date_local_to_utc($end_date_time);

        $data = array(
            "project_id" => $this->input->post('project_id'),
            "start_time" => $start_date_time,
            "end_time" => $end_date_time,
            "note" => $note ? $note : "",
            "task_id" => $task_id ? $task_id : 0
        );

        //save user_id only on insert and it will not be editable
        if (!$id) {
            //insert mode
            $data["user_id"] = $this->input->post('user_id') ? $this->input->post('user_id') : $this->login_user->id;
        }

        $this->check_timelog_update_permission($id, get_array_value($data, "user_id"));

        $save_id = $this->Timesheets_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* insert/update a timelog */

    function save_timelog_note() {
        $this->access_only_team_members();

        validate_submitted_data(array(
            "id" => "required"
        ));

        $id = $this->input->post('id');
        $data = array(
            "note" => $this->input->post("note")
        );


        //check edit permission
        $this->check_timelog_update_permission($id);



        $save_id = $this->Timesheets_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a timelog */

    function delete_timelog() {
        $this->access_only_team_members();



        $id = $this->input->post('id');

        $this->check_timelog_update_permission($id);

        if ($this->input->post('undo')) {
            if ($this->Timesheets_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Timesheets_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    private function check_timelog_update_permission($log_id = null, $user_id = null) {
        //check delete permission
        $members = $this->_get_members_to_manage_timesheet();

        if ($log_id) {
            $info = $this->Timesheets_model->get_one($log_id);
            $user_id = $info->user_id;
        }


        if ($members != "all" && !in_array($user_id, $members)) {
            redirect("forbidden");
        }
    }

    /* list of timesheets, prepared for datatable  */

    function timesheet_list_data() {

        $project_id = $this->input->post("project_id");

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise


        if (!$this->can_view_timesheet($project_id, true)) {
            redirect("forbidden");
        }

        $options = array(
            "project_id" => $project_id,
            "status" => "none_open",
            "user_id" => $this->input->post("user_id"),
            "start_date" => $this->input->post("start_date"),
            "end_date" => $this->input->post("end_date"),
            "task_id" => $this->input->post("task_id"),
            "client_id" => $this->input->post("client_id")
        );

        //get allowed member ids
        $members = $this->_get_members_to_manage_timesheet();
        if ($members != "all" && $this->login_user->user_type == "staff") {
            //if user has permission to access all members, query param is not required
            //client can view all timesheet
            $options["allowed_members"] = $members;
        }

        $list_data = $this->Timesheets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_timesheet_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of timesheet list  table */

    private function _timesheet_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Timesheets_model->get_details($options)->row();
        return $this->_make_timesheet_row($data);
    }

    /* prepare a row of timesheet list table */

    private function _make_timesheet_row($data) {
        $image_url = get_avatar($data->logged_by_avatar);
        $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->logged_by_user";

        $start_time = $data->start_time;
        $end_time = $data->end_time;
        $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);
        $task_title = modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id));

        //if the rich text editor is enabled, don't show the note as title
        $note_title = $data->note;
        if (get_setting('enable_rich_text_editor')) {
            $note_title = "";
        }

        $note_link = modal_anchor(get_uri("projects/timer_note_modal_form/"), "<i class='fa fa-comment-o p10'></i>", array("class" => "edit text-muted", "title" => lang("note"), "data-post-id" => $data->id));
        if ($data->note) {
            $note_link = modal_anchor(get_uri("projects/timer_note_modal_form/"), "<i class='fa fa-comment p10'></i>", array("class" => "edit text-muted", "title" => $note_title, "data-modal-title" => lang("note"), "data-post-id" => $data->id));
        }

        return array(
            get_team_member_profile_link($data->user_id, $user),
            $project_title,
            anchor(get_uri("clients/view/" . $data->timesheet_client_id), $data->timesheet_client_company_name),
            $task_title,
            $data->start_time,
            format_to_datetime($data->start_time),
            $data->end_time,
            format_to_datetime($data->end_time),
            convert_seconds_to_time_format(abs(strtotime($end_time) - strtotime($start_time))),
            $note_link,
            modal_anchor(get_uri("projects/timelog_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_timelog'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_timelog'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_timelog"), "data-action" => "delete"))
        );
    }

    /* load timesheets summary view for a project */

    function timesheet_summary($project_id) {

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise

        if (!$this->can_view_timesheet($project_id)) {
            redirect("forbidden");
        }



        $view_data['project_id'] = $project_id;

        $view_data['group_by_dropdown'] = json_encode(
                array(
                    array("id" => "", "text" => "- " . lang("group_by") . " -"),
                    array("id" => "member", "text" => lang("member")),
                    array("id" => "task", "text" => lang("task"))
        ));

        $view_data['project_members_dropdown'] = json_encode($this->_get_project_members_dropdown_list_for_filter($project_id));
        $view_data['tasks_dropdown'] = $this->_get_timesheet_tasks_dropdown($project_id, true);

        $this->load->view("projects/timesheets/summary_list", $view_data);
    }

    /* list of timesheets summary, prepared for datatable  */

    function timesheet_summary_list_data() {

        $project_id = $this->input->post("project_id");


        //client can't view all projects timesheet. project id is required.
        if (!$project_id) {
            $this->access_only_team_members();
        }

        if ($project_id) {
            $this->init_project_permission_checker($project_id);
            $this->init_project_settings($project_id); //since we'll check this permission project wise

            if (!$this->can_view_timesheet($project_id, true)) {
                redirect("forbidden");
            }
        }


        $group_by = $this->input->post("group_by");

        $options = array(
            "project_id" => $project_id,
            "status" => "none_open",
            "user_id" => $this->input->post("user_id"),
            "start_date" => $this->input->post("start_date"),
            "end_date" => $this->input->post("end_date"),
            "task_id" => $this->input->post("task_id"),
            "group_by" => $group_by,
            "client_id" => $this->input->post("client_id")
        );

        //get allowed member ids
        $members = $this->_get_members_to_manage_timesheet();
        if ($members != "all" && $this->login_user->user_type == "staff") {
            //if user has permission to access all members, query param is not required
            //client can view all timesheet
            $options["allowed_members"] = $members;
        }

        $list_data = $this->Timesheets_model->get_summary_details($options)->result();

        $result = array();
        foreach ($list_data as $data) {


            $member = "-";
            $task_title = "-";

            if ($group_by != "task") {
                $image_url = get_avatar($data->logged_by_avatar);
                $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->logged_by_user";

                $member = get_team_member_profile_link($data->user_id, $user);
            }

            $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);

            if ($group_by != "member") {
                $task_title = modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id));
                if (!$data->task_title) {
                    $task_title = lang("not_specified");
                }
            }


            $duration = convert_seconds_to_time_format(abs($data->total_duration));


            $result[] = array(
                $project_title,
                anchor(get_uri("clients/view/" . $data->timesheet_client_id), $data->timesheet_client_company_name),
                $member,
                $task_title,
                $duration,
                to_decimal_format(convert_time_string_to_decimal($duration))
            );
        }
        echo json_encode(array("data" => $result));
    }

    /* get all projects list */

    private function _get_all_projects_dropdown_list() {
        $projects = $this->Projects_model->get_dropdown_list(array("title"));

        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $id => $title) {
            $projects_dropdown[] = array("id" => $id, "text" => $title);
        }
        return $projects_dropdown;
    }

    /* get all projects list according to the login user */

    private function _get_all_projects_dropdown_list_for_timesheets_filter() {
        $options = array();

        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $projects = $this->Projects_model->get_details($options)->result();

        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
        }

        return $projects_dropdown;
    }

    /*
     * admin can manage all members timesheet
     * allowed member can manage other members timesheet accroding to permission
     */

    private function _get_members_to_manage_timesheet() {

        $access_info = $this->get_access_info("timesheet_manage_permission");

        if ($access_info->access_type == "all") {
            return "all"; //can access all member's timelogs
        } else if (count($access_info->allowed_members)) {
            return $access_info->allowed_members; //can access allowed member's timelogs
        } else {
            return array($this->login_user->id); //can access own timelogs
        }
    }

    /* prepare dropdown list */

    private function _prepare_members_dropdown_for_timesheet_filter($members) {
        $where = array("user_type" => "staff");

        if ($members != "all") {
            $where["where_in"] = array("id" => $members);
        }

        $users = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", $where);

        $members_dropdown = array(array("id" => "", "text" => "- " . lang("member") . " -"));
        foreach ($users as $id => $name) {
            $members_dropdown[] = array("id" => $id, "text" => $name);
        }
        return $members_dropdown;
    }

    /* load all time sheets view  */

    function all_timesheets() {
        $this->access_only_team_members();
        $members = $this->_get_members_to_manage_timesheet();

        $view_data['members_dropdown'] = json_encode($this->_prepare_members_dropdown_for_timesheet_filter($members));
        $view_data['projects_dropdown'] = json_encode($this->_get_all_projects_dropdown_list_for_timesheets_filter());
        $view_data['clients_dropdown'] = json_encode($this->_get_clients_dropdown());

        $this->template->rander("projects/timesheets/all_timesheets", $view_data);
    }

    /* load all timesheets summary view */

    function all_timesheet_summary() {
        $this->access_only_team_members();

        $members = $this->_get_members_to_manage_timesheet();

        $view_data['group_by_dropdown'] = json_encode(
                array(
                    array("id" => "", "text" => "- " . lang("group_by") . " -"),
                    array("id" => "member", "text" => lang("member")),
                    array("id" => "project", "text" => lang("project")),
                    array("id" => "task", "text" => lang("task"))
        ));


        $view_data['members_dropdown'] = json_encode($this->_prepare_members_dropdown_for_timesheet_filter($members));
        $view_data['projects_dropdown'] = json_encode($this->_get_all_projects_dropdown_list_for_timesheets_filter());
        $view_data['clients_dropdown'] = json_encode($this->_get_clients_dropdown());

        $this->load->view("projects/timesheets/all_summary_list", $view_data);
    }

    /* load milestones view */

    function milestones($project_id) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_milestones()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;

        $view_data["can_create_milestones"] = $this->can_create_milestones();
        $view_data["can_edit_milestones"] = $this->can_edit_milestones();
        $view_data["can_delete_milestones"] = $this->can_delete_milestones();

        $this->load->view("projects/milestones/index", $view_data);
    }

    /* load milestone add/edit modal */

    function milestone_modal_form() {
        $id = $this->input->post('id');
        $view_data['model_info'] = $this->Milestones_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_milestones()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_milestones()) {
                redirect("forbidden");
            }
        }

        $view_data['project_id'] = $project_id;

        $this->load->view('projects/milestones/modal_form', $view_data);
    }

    /* insert/update a milestone */

    function save_milestone() {

        $id = $this->input->post('id');
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_milestones()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_milestones()) {
                redirect("forbidden");
            }
        }

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "project_id" => $this->input->post('project_id'),
            "due_date" => $this->input->post('due_date')
        );
        $save_id = $this->Milestones_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a milestone */

    function delete_milestone() {

        $id = $this->input->post('id');
        $info = $this->Milestones_model->get_one($id);
        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_milestones()) {
            redirect("forbidden");
        }

        if ($this->input->post('undo')) {
            if ($this->Milestones_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Milestones_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of milestones, prepared for datatable  */

    function milestones_list_data($project_id = 0) {
        $this->init_project_permission_checker($project_id);

        $options = array("project_id" => $project_id);
        $list_data = $this->Milestones_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_milestone_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of milestone list  table */

    private function _milestone_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Milestones_model->get_details($options)->row();
        $this->init_project_permission_checker($data->project_id);

        return $this->_make_milestone_row($data);
    }

    /* prepare a row of milestone list table */

    private function _make_milestone_row($data) {

        //calculate milestone progress
        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;
        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";

        //define milesone color based on due date
        $due_date = date("L", strtotime($data->due_date));
        $label_class = "";
        if ($progress == 100) {
            $label_class = "label-success";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") > $data->due_date) {
            $label_class = "label-danger";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") == $data->due_date) {
            $label_class = "label-warning";
        } else {
            $label_class = "label-primary";
        }

        $day_or_year_name = "";
        if (date("Y", strtotime(get_current_utc_time())) === date("Y", strtotime($data->due_date))) {
            $day_or_year_name = lang(strtolower(date("l", strtotime($data->due_date)))); //get day name from language
        } else {
            $day_or_year_name = date("Y", strtotime($data->due_date)); //get current year
        }

        $month_name = lang(strtolower(date("F", strtotime($data->due_date)))); //get month name from language

        $due_date = "<div class='milestone pull-left' title='" . format_to_date($data->due_date) . "'>
            <span class='label $label_class'>" . $month_name . "</span>
            <h1>" . date("d", strtotime($data->due_date)) . "</h1>
            <span>" . $day_or_year_name . "</span>
            </div>
            "
        ;

        $optoins = "";
        if ($this->can_edit_milestones()) {
            $optoins .= modal_anchor(get_uri("projects/milestone_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_milestone'), "data-post-id" => $data->id));
        }

        if ($this->can_delete_milestones()) {
            $optoins .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_milestone'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_milestone"), "data-action" => "delete"));
        }


        $title = "<div><b>" . $data->title . "</b></div>";
        if ($data->description) {
            $title .= "<div>" . nl2br($data->description) . "<div>";
        }

        return array(
            $data->due_date,
            $due_date,
            $title,
            $progress_bar,
            $optoins
        );
    }

    /* load task list view tab */

    function tasks($project_id) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $view_data['view_type'] = "project_tasks";

        $view_data['can_create_tasks'] = $this->can_create_tasks();
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['can_delete_tasks'] = $this->can_delete_tasks();

        $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
        $view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list($project_id);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->result();

        $this->load->view("projects/tasks/index", $view_data);
    }

    /* load task kanban view of view tab */

    function tasks_kanban($project_id) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;

        $view_data['can_create_tasks'] = $this->can_create_tasks();


        $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
        $view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list($project_id);
        $view_data['task_statuses'] = $this->Task_status_model->get_details()->result();
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();

        $this->load->view("projects/tasks/kanban/project_tasks", $view_data);
    }

    /* get list of milestones for filter */

    function get_milestones_for_filter() {

        $this->access_only_team_members();
        $project_id = $this->input->post("project_id");
        if ($project_id) {
            echo $this->_get_milestones_dropdown_list($project_id);
        }
    }

    private function _get_milestones_dropdown_list($project_id = 0) {
        $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        $milestone_dropdown = array(array("id" => "", "text" => "- " . lang("milestone") . " -"));

        foreach ($milestones as $milestone) {
            $milestone_dropdown[] = array("id" => $milestone->id, "text" => $milestone->title);
        }
        return json_encode($milestone_dropdown);
    }

    private function _get_project_members_dropdown_list($project_id = 0) {
        $assigned_to_dropdown = array(array("id" => "", "text" => "- " . lang("assigned_to") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {
            $assigned_to_dropdown[] = array("id" => $key, "text" => $value);
        }

        //add client contacts
        if ($project_id) {
            $contacts = $this->Project_members_model->get_client_contacts_of_the_project_client($project_id, true)->result();
            foreach ($contacts as $contact) {
                $assigned_to_dropdown[] = array("id" => $contact->id, "text" => $contact->contact_name);
            }
        }

        return json_encode($assigned_to_dropdown);
    }

    function all_tasks() {
        $this->access_only_team_members();
        $view_data['project_id'] = 0;
        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->result();
        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . lang("team_member") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value, "isSelected" => true);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }


        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->result();

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);
        $view_data['can_create_tasks'] = $this->can_create_tasks(false);

        $this->template->rander("projects/tasks/my_tasks", $view_data);
    }

    function all_tasks_kanban() {

        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->result();
        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . lang("team_member") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value, "isSelected" => true);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);
        $view_data['can_create_tasks'] = $this->can_create_tasks(false);

        $this->template->rander("projects/tasks/kanban/all_tasks", $view_data);
    }

    //check user's task editting permission on changing of project
    function can_edit_task_of_the_project($project_id = 0) {
        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            if ($this->can_edit_tasks()) {
                echo json_encode(array("success" => true));
            } else {
                echo json_encode(array("success" => false));
            }
        }
    }

    function all_tasks_kanban_data() {

        $this->access_only_team_members();

        $status = $this->input->post('status_id') ? implode(",", $this->input->post('status_id')) : "";
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        $specific_user_id = $this->input->post('specific_user_id');

        $options = array(
            "specific_user_id" => $specific_user_id,
            "status_ids" => $status,
            "project_id" => $project_id,
            "milestone_id" => $this->input->post('milestone_id'),
            "deadline" => $this->input->post('deadline'),
            "search" => $this->input->post('search'),
            "project_status" => "open",
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );

        if (!$this->can_manage_all_projects()) {
            $options["project_member_id"] = $this->login_user->id; //don't show all tasks to non-admin users
        }

        $view_data["tasks"] = $this->Tasks_model->get_kanban_details($options)->result();
        $statuses = $this->Task_status_model->get_details();

        $view_data["total_columns"] = $statuses->num_rows();
        $view_data["columns"] = $statuses->result();
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['project_id'] = $project_id;

        $this->load->view('projects/tasks/kanban/kanban_view', $view_data);
    }

    /* prepare data for the projuect view's kanban tab  */

    function project_tasks_kanban_data($project_id = 0) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }

        $specific_user_id = $this->input->post('specific_user_id');

        $options = array(
            "specific_user_id" => $specific_user_id,
            "project_id" => $project_id,
            "assigned_to" => $this->input->post('assigned_to'),
            "milestone_id" => $this->input->post('milestone_id'),
            "deadline" => $this->input->post('deadline'),
            "search" => $this->input->post('search'),
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );


        $view_data["tasks"] = $this->Tasks_model->get_kanban_details($options)->result();
        $statuses = $this->Task_status_model->get_details();

        $view_data["total_columns"] = $statuses->num_rows();
        $view_data["columns"] = $statuses->result();
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['project_id'] = $project_id;

        $this->load->view('projects/tasks/kanban/kanban_view', $view_data);
    }

    function set_task_comments_as_read($task_id = 0) {
        if ($task_id) {
            $this->Tasks_model->set_task_comments_as_read($task_id, $this->login_user->id);
        }
    }

    function task_view($task_id = 0) {
        $view_type = "";

        if ($task_id) { //details page
            $view_type = "details";
        } else { //modal view
            $task_id = $this->input->post('id');
        }

        $model_info = $this->Tasks_model->get_details(array("id" => $task_id))->row();
        if (!$model_info->id) {
            show_404();
        }
        $this->init_project_permission_checker($model_info->project_id);

        if (!$this->can_view_tasks($model_info->project_id, $task_id)) {
            redirect("forbidden");
        }

        $view_data = $this->_initialize_all_related_data_of_project($model_info->project_id, $model_info->collaborators);

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client") {
            $view_data['show_assign_to_dropdown'] = false;
        }

        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['can_comment_on_tasks'] = $this->can_comment_on_tasks();

        $view_data['model_info'] = $model_info;
        $view_data['collaborators'] = $this->_get_collaborators($model_info->collaborator_list, false);

        $view_data['labels'] = $this->_make_labels_view_data($model_info->labels);

        $options = array("task_id" => $task_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['task_id'] = $task_id;

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("tasks", $task_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        //get checklist items
        $checklist_items_array = array();
        $checklist_items = $this->Checklist_items_model->get_details(array("task_id" => $task_id))->result();
        foreach ($checklist_items as $checklist_item) {
            $checklist_items_array[] = $this->_make_checklist_item_row($checklist_item);
        }
        $view_data["checklist_items"] = json_encode($checklist_items_array);

        //get sub tasks
        $sub_tasks_array = array();
        $sub_tasks = $this->Tasks_model->get_details(array("parent_task_id" => $task_id))->result();
        foreach ($sub_tasks as $sub_task) {
            $sub_tasks_array[] = $this->_make_sub_task_row($sub_task);
        }
        $view_data["sub_tasks"] = json_encode($sub_tasks_array);

        $view_data['project_id'] = $model_info->project_id;

        $view_data['can_create_tasks'] = $this->can_create_tasks();

        $view_data['parent_task_title'] = $this->Tasks_model->get_one($model_info->parent_task_id)->title;

        $view_data["view_type"] = $view_type;

        $view_data["blocked_by"] = $this->_make_dependency_tasks_view_data($this->_get_all_dependency_for_this_task_specific($model_info->blocked_by, $task_id, "blocked_by"), $task_id, "blocked_by");
        $view_data["blocking"] = $this->_make_dependency_tasks_view_data($this->_get_all_dependency_for_this_task_specific($model_info->blocking, $task_id, "blocking"), $task_id, "blocking");

        $project_deadline_date = "";
        $project_deadline = $this->Projects_model->get_one($model_info->project_id)->deadline;
        if (is_date_exists($project_deadline)) {
            $project_deadline_date = format_to_date($project_deadline);
        }

        $view_data["project_deadline"] = $project_deadline_date;

        if ($view_type == "details") {
            $this->template->rander('projects/tasks/view', $view_data);
        } else {
            $this->load->view('projects/tasks/view', $view_data);
        }
    }

    private function _make_labels_view_data($labels) {
        $task_labels = "";
        if ($labels) {
            $labels = explode(",", $labels);
            foreach ($labels as $label) {
                $task_labels .= "<span class='label label-info'  title='Label'>" . $label . "</span> ";
            }
        }

        return $task_labels;
    }

    private function _initialize_all_related_data_of_project($project_id = 0, $collaborators = "") {
        //we have to check if any defined project exists, then go through with the project id
        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            $related_data = $this->get_all_related_data_of_project($project_id, $collaborators);

            $view_data['milestones_dropdown'] = $related_data["milestones_dropdown"];
            $view_data['assign_to_dropdown'] = $related_data["assign_to_dropdown"];
            $view_data['collaborators_dropdown'] = $related_data["collaborators_dropdown"];
            $view_data['label_suggestions'] = $related_data["label_suggestions"];
        } else {
            $view_data["projects_dropdown"] = $this->_get_projects_dropdown();

            //we have to show an empty dropdown when there is no project_id defined
            $view_data['milestones_dropdown'] = array(array("id" => "", "text" => "-"));
            $view_data['assign_to_dropdown'] = array(array("id" => "", "text" => "-"));
            $view_data['collaborators_dropdown'] = array();
            $view_data['label_suggestions'] = array();
        }

        $task_points = array();
        for ($i = 1; $i <= get_setting("task_point_range"); $i++) {
            if ($i == 1) {
                $task_points[$i] = $i . " " . lang('point');
            } else {
                $task_points[$i] = $i . " " . lang('points');
            }
        }

        $view_data['points_dropdown'] = $task_points;

        $view_data['statuses'] = $this->Task_status_model->get_details()->result();

        return $view_data;
    }

    /* task add/edit modal */

    function task_modal_form() {
        $id = $this->input->post('id');
        $add_type = $this->input->post('add_type');
        $last_id = $this->input->post('last_id');
        $ticket_id = $this->input->post('ticket_id');

        $model_info = $this->Tasks_model->get_one($id);
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $model_info->project_id;

        $final_project_id = $project_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_project_id = $model_info->project_id;
        }

        $view_data = $this->_initialize_all_related_data_of_project($final_project_id, $model_info->collaborators);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks($project_id ? true : false)) {
                redirect("forbidden");
            }
        }

        $view_data['model_info'] = $model_info;
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown(); //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;
        $view_data['project_id'] = $project_id;
        $view_data['ticket_id'] = $ticket_id;

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client") {
            $view_data['show_assign_to_dropdown'] = false;
        } else {
            //set default assigne to for new tasks
            if (!$id && !$view_data['model_info']->assigned_to) {
                $view_data['model_info']->assigned_to = $this->login_user->id;
            }
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        //clone task
        $is_clone = $this->input->post('is_clone');
        $view_data['is_clone'] = $is_clone;

        $view_data['view_type'] = $this->input->post("view_type");

        $view_data['has_checklist'] = $this->Checklist_items_model->get_details(array("task_id" => $id))->num_rows();

        $project_deadline_date = "";
        $project_deadline = $this->Projects_model->get_one($project_id)->deadline;
        if (is_date_exists($project_deadline)) {
            $project_deadline_date = format_to_date($project_deadline);
        }

        $view_data["project_deadline"] = $project_deadline_date;

        $this->load->view('projects/tasks/modal_form', $view_data);
    }

    //get projects dropdown
    private function _get_projects_dropdown() {
        $project_options = array("status" => "open");
        if ($this->login_user->user_type == "staff") {
            if (!$this->can_manage_all_projects()) {
                $project_options["user_id"] = $this->login_user->id; //normal user's should be able to see only the projects where they are added as a team mmeber.
            }
        } else {
            $project_options["client_id"] = $this->login_user->client_id; //get client's projects
        }

        $projects = $this->Projects_model->get_details($project_options)->result();
        $projects_dropdown = array("" => "-");

        if ($projects) {
            foreach ($projects as $project) {
                $projects_dropdown[$project->id] = $project->title;
            }
        }

        return $projects_dropdown;
    }

    private function get_all_related_data_of_project($project_id, $collaborators = "") {

        if ($project_id) {

            //get milestone dropdown
            $milestones = $this->Milestones_model->get_dropdown_list(array("title"), "id", array("project_id" => $project_id));
            $milestones_dropdown = array(array("id" => "", "text" => "-"));
            foreach ($milestones as $key => $value) {
                $milestones_dropdown[] = array("id" => $key, "text" => $value);
            }

            //get project members and collaborators dropdown
            $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id, "", $this->can_access_clients())->result();
            $project_members_dropdown = array(array("id" => "", "text" => "-"));
            $collaborators_dropdown = array();
            $collaborators_array = $collaborators ? explode(",", $collaborators) : array();
            foreach ($project_members as $member) {
                $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);

                //if there is already any inactive user in collaborators list
                //we've to show the user(s) for furthur operation
                //exclude client contacts
                if ($member->user_type == "staff" && (in_array($member->user_id, $collaborators_array) || $member->member_status == "active")) {
                    $collaborators_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
                }
            }

            //get labels suggestion
            $labels = explode(",", $this->Tasks_model->get_label_suggestions($project_id));
            $label_suggestions = array();
            foreach ($labels as $label) {
                if ($label && !in_array($label, $label_suggestions)) {
                    $label_suggestions[] = $label;
                }
            }
            if (!count($label_suggestions)) {
                $label_suggestions = array("0" => "");
            }

            return array(
                "milestones_dropdown" => $milestones_dropdown,
                "assign_to_dropdown" => $project_members_dropdown,
                "collaborators_dropdown" => $collaborators_dropdown,
                "label_suggestions" => $label_suggestions
            );
        }
    }

    /* get all related data of selected project */

    function get_all_related_data_of_selected_project($project_id) {

        if ($project_id) {
            $related_data = $this->get_all_related_data_of_project($project_id);

            echo json_encode(array(
                "milestones_dropdown" => $related_data["milestones_dropdown"],
                "assign_to_dropdown" => $related_data["assign_to_dropdown"],
                "collaborators_dropdown" => $related_data["collaborators_dropdown"],
                "label_suggestions" => $related_data["label_suggestions"],
            ));
        }
    }

    /* insert/upadate/clone a task */

    function save_task() {

        $project_id = $this->input->post('project_id');
        $id = $this->input->post('id');
        $add_type = $this->input->post('add_type');
        $ticket_id = $this->input->post('ticket_id');

        $is_clone = $this->input->post('is_clone');
        $main_task_id = "";
        if ($is_clone && $id) {
            $main_task_id = $id; //store main task id to get items later
            $id = ""; //on cloning task, save as new
        }

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks()) {
                redirect("forbidden");
            }
        }

        $start_date = $this->input->post('start_date');
        $assigned_to = $this->input->post('assigned_to');
        $collaborators = $this->input->post('collaborators');
        $recurring = $this->input->post('recurring') ? 1 : 0;
        $repeat_every = $this->input->post('repeat_every');
        $repeat_type = $this->input->post('repeat_type');
        $no_of_cycles = $this->input->post('no_of_cycles');

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "project_id" => $project_id,
            "milestone_id" => $this->input->post('milestone_id'),
            "points" => $this->input->post('points'),
            "status_id" => $this->input->post('status_id'),
            "labels" => $this->input->post('labels'),
            "start_date" => $start_date,
            "deadline" => $this->input->post('deadline'),
            "recurring" => $recurring,
            "repeat_every" => $repeat_every ? $repeat_every : 0,
            "repeat_type" => $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => $no_of_cycles ? $no_of_cycles : 0,
        );

        if (!$id) {
            $data["created_date"] = get_current_utc_time();
        }

        if ($ticket_id) {
            $data["ticket_id"] = $ticket_id;
        }

        //clint can't save the assign to and collaborators
        if ($this->login_user->user_type == "client") {
            if (!$id) { //it's new data to save
                $data["assigned_to"] = 0;
                $data["collaborators"] = "";
            }
        } else {
            $data["assigned_to"] = $assigned_to;
            $data["collaborators"] = $collaborators;
        }

        $data = clean_data($data);

        //set null value after cleaning the data
        if (!$data["start_date"]) {
            $data["start_date"] = NULL;
        }

        if (!$data["deadline"]) {
            $data["deadline"] = NULL;
        }

        $copy_checklist = $this->input->post("copy_checklist");

        $next_recurring_date = "";

        if ($recurring && get_setting("enable_recurring_option_for_tasks")) {
            //set next recurring date for recurring tasks

            if ($id) {
                //update
                if ($this->input->post('next_recurring_date')) { //submitted any recurring date? set it.
                    $next_recurring_date = $this->input->post('next_recurring_date');
                } else {
                    //re-calculate the next recurring date, if any recurring fields has changed.
                    $task_info = $this->Tasks_model->get_one($id);
                    if ($task_info->recurring != $data['recurring'] || $task_info->repeat_every != $data['repeat_every'] || $task_info->repeat_type != $data['repeat_type'] || $task_info->start_date != $data['start_date']) {
                        $recurring_start_date = $start_date ? $start_date : $task_info->created_date;
                        $next_recurring_date = add_period_to_date($recurring_start_date, $repeat_every, $repeat_type);
                    }
                }
            } else {
                //insert new
                $recurring_start_date = $start_date ? $start_date : get_array_value($data, "created_date");
                $next_recurring_date = add_period_to_date($recurring_start_date, $repeat_every, $repeat_type);
            }


            //recurring date must have to set a future date
            if ($next_recurring_date && get_today_date() >= $next_recurring_date) {
                echo json_encode(array("success" => false, 'message' => lang('past_recurring_date_error_message_title_for_tasks'), 'next_recurring_date_error' => lang('past_recurring_date_error_message'), "next_recurring_date_value" => $next_recurring_date));
                return false;
            }
        }


        $save_id = $this->Tasks_model->save($data, $id);
        if ($save_id) {

            if ($is_clone && $main_task_id && $copy_checklist) {
                //add task checklist

                $checklist_items = $this->Checklist_items_model->get_all_where(array("task_id" => $main_task_id, "deleted" => 0))->result();

                foreach ($checklist_items as $checklist_item) {
                    //prepare new invoice item data
                    $checklist_item_data = (array) $checklist_item;
                    unset($checklist_item_data["id"]);
                    $checklist_item_data['task_id'] = $save_id;

                    $checklist_item = $this->Checklist_items_model->save($checklist_item_data);
                }
            }

            //save next recurring date 
            if ($next_recurring_date) {
                $recurring_task_data = array(
                    "next_recurring_date" => $next_recurring_date
                );
                $this->Tasks_model->save_reminder_date($recurring_task_data, $save_id);
            }

            // if created from ticket then save the task id
            if ($ticket_id) {
                $data = array("task_id" => $save_id);
                $this->Tickets_model->save($data, $ticket_id);
            }

            $activity_log_id = get_array_value($data, "activity_log_id");

            $new_activity_log_id = save_custom_fields("tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type, $activity_log_id);

            if ($id) {
                //updated
                log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => $new_activity_log_id ? $new_activity_log_id : $activity_log_id));
            } else {
                //created
                log_notification("project_task_created", array("project_id" => $project_id, "task_id" => $save_id));

                //save uploaded files as comment
                $target_path = get_setting("timeline_file_path");
                $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "project_comment");

                if ($files_data && $files_data != "a:0:{}") {
                    $comment_data = array(
                        "created_by" => $this->login_user->id,
                        "created_at" => get_current_utc_time(),
                        "project_id" => $project_id,
                        "task_id" => $save_id
                    );

                    $comment_data = clean_data($comment_data);

                    $comment_data["files"] = $files_data; //don't clean serilized data

                    $this->Project_comments_model->save_comment($comment_data);
                }
            }

            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved'), "add_type" => $add_type));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function save_sub_task() {
        $project_id = $this->input->post('project_id');

        validate_submitted_data(array(
            "project_id" => "required|numeric",
            "parent_task_id" => "required|numeric"
        ));

        $this->init_project_permission_checker($project_id);
        if (!$this->can_create_tasks()) {
            redirect("forbidden");
        }

        $data = array(
            "title" => $this->input->post('sub-task-title'),
            "project_id" => $project_id,
            "milestone_id" => $this->input->post('milestone_id'),
            "parent_task_id" => $this->input->post('parent_task_id'),
            "status_id" => 1,
            "created_date" => get_current_utc_time()
        );

        $data = clean_data($data);

        $save_id = $this->Tasks_model->save($data);

        if ($save_id) {
            log_notification("project_task_created", array("project_id" => $project_id, "task_id" => $save_id));

            $task_info = $this->Tasks_model->get_details(array("id" => $save_id))->row();

            echo json_encode(array("success" => true, "task_data" => $this->_make_sub_task_row($task_info), "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    private function _make_sub_task_row($data, $return_type = "row") {

        $checkbox_class = "checkbox-blank";
        $title_class = "";

        if ($data->status_key_name == "done") {
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
        }

        $status = "";
        if ($this->can_edit_tasks()) {
            $status = js_anchor("<span class='$checkbox_class'></span>", array('title' => "", "data-id" => $data->id, "data-value" => $data->status_key_name === "done" ? "1" : "3", "data-act" => "update-sub-task-status-checkbox"));
        }

        $title = anchor(get_uri("projects/task_view/$data->id"), $data->title, array("class" => "font-13", "target" => "_blank"));

        $status_label = "<span class='pull-right'><span class='label mt0' style='background: $data->status_color;'>" . ($data->status_key_name ? lang($data->status_key_name) : $data->status_title) . "</span></span>";

        if ($return_type == "data") {
            return $status . $title . $status_label;
        }

        return "<div class='list-group-item mb5' data-id='$data->id'>" . $status . $title . $status_label . "</div>";
    }

    /* upadate a task status */

    function save_task_status($id = 0) {
        $this->access_only_team_members();
        $data = array(
            "status_id" => $this->input->post('value')
        );

        $save_id = $this->Tasks_model->save($data, $id);

        if ($save_id) {
            $task_info = $this->Tasks_model->get_details(array("id" => $save_id))->row();

            echo json_encode(array("success" => true, "data" => (($this->input->post("type") == "sub_task") ? $this->_make_sub_task_row($task_info, "data") : $this->_task_row_data($save_id)), 'id' => $save_id, "message" => lang('record_saved')));

            log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    function update_task_info($id = 0, $data_field = "") {
        if ($id) {
            $task_info = $this->Tasks_model->get_one($id);
            $this->init_project_permission_checker($task_info->project_id);

            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }

            $value = $this->input->post('value');
            if ($data_field == "labels") {
                $value = implode(",", $value);
            } else if ($data_field == "collaborators") {
                $value = implode(",", $value);
            }

            $data = array(
                $data_field => $value
            );

            $save_id = $this->Tasks_model->save($data, $id);

            if ($save_id) {
                $task_info = $this->Tasks_model->get_details(array("id" => $save_id))->row();

                $success_array = array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, "message" => lang('record_saved'));

                if ($data_field == "assigned_to") {
                    $success_array["assigned_to_avatar"] = get_avatar($task_info->assigned_to_avatar);
                    $success_array["assigned_to_id"] = $task_info->assigned_to;
                }

                if ($data_field == "labels") {
                    $success_array["labels"] = $this->_make_labels_view_data($task_info->labels);
                }

                if ($data_field == "points") {
                    $success_array["points"] = $task_info->points;
                }

                if ($data_field == "status_id") {
                    $success_array["status_color"] = $task_info->status_color;
                }

                if ($data_field == "collaborators") {
                    $success_array["collaborators"] = $this->_get_collaborators($task_info->collaborator_list, false);
                }

                if ($data_field == "start_date" || $data_field == "deadline") {
                    $date = "-";
                    if (is_date_exists($task_info->$data_field)) {
                        $date = format_to_date($task_info->$data_field, false);
                    }
                    $success_array["date"] = $date;
                }

                echo json_encode($success_array);

                log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        }
    }

    /* upadate a task status */

    function save_task_sort_and_status() {
        $project_id = $this->input->post('project_id');
        $this->init_project_permission_checker($project_id);

        if (!$this->can_edit_tasks()) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        $status_id = $this->input->post('status_id');
        $data = array(
            "sort" => $this->input->post('sort')
        );

        if ($status_id) {
            $data["status_id"] = $status_id;
        }

        $save_id = $this->Tasks_model->save($data, $id);

        if ($save_id) {
            if ($status_id) {
                $task_info = $this->Tasks_model->get_one($save_id);
                log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            }
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    /* delete or undo a task */

    function delete_task() {

        $id = $this->input->post('id');
        $info = $this->Tasks_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_tasks()) {
            redirect("forbidden");
        }

        if ($this->input->post('undo')) {
            if ($this->Tasks_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_task_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Tasks_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));

                $task_info = $this->Tasks_model->get_one($id);
                log_notification("project_task_deleted", array("project_id" => $task_info->project_id, "task_id" => $id));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of tasks, prepared for datatable  */

    function tasks_list_data($project_id = 0) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $status = $this->input->post('status_id') ? implode(",", $this->input->post('status_id')) : "";
        $milestone_id = $this->input->post('milestone_id');
        $options = array(
            "project_id" => $project_id,
            "assigned_to" => $this->input->post('assigned_to'),
            "deadline" => $this->input->post('deadline'),
            "status_ids" => $status,
            "milestone_id" => $milestone_id,
            "custom_fields" => $custom_fields,
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );

        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();

        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of tasks, prepared for datatable  */

    function my_tasks_list_data($is_widget = 0) {
        $this->access_only_team_members();

        $status = $this->input->post('status_id') ? implode(",", $this->input->post('status_id')) : "";
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        $specific_user_id = $this->input->post('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "specific_user_id" => $specific_user_id,
            "project_id" => $project_id,
            "milestone_id" => $this->input->post('milestone_id'),
            "deadline" => $this->input->post('deadline'),
            "custom_fields" => $custom_fields,
            "project_status" => "open",
            "status_ids" => $status,
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );

        if ($is_widget) {
            $todo_status_id = $this->Task_status_model->get_one_where(array("key_name" => "done", "deleted" => 0));
            if ($todo_status_id) {
                $options["exclude_status_id"] = $todo_status_id->id;
                $options["specific_user_id"] = $this->login_user->id;
            }
        }

        if (!$this->can_manage_all_projects()) {
            $options["project_member_id"] = $this->login_user->id; //don't show all tasks to non-admin users
        }


        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of task list table */

    private function _task_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Tasks_model->get_details($options)->row();

        $this->init_project_permission_checker($data->project_id);

        return $this->_make_task_row($data, $custom_fields);
    }

    /* prepare a row of task list table */

    private function _make_task_row($data, $custom_fields) {
        $unread_comments_class = "";
        $icon = "";
        if (isset($data->unread) && $data->unread && $data->unread != "0") {
            $unread_comments_class = "unread-comments-of-tasks";
            $icon = "<i class='fa fa-comment'></i>";
        }

        $title = "";
        if ($data->parent_task_id) {
            $title = "<span class='sub-task-icon mr5' title='" . lang("sub_task") . "'><i class='fa fa-code-fork'></i></span>";
        }

        $title .= modal_anchor(get_uri("projects/task_view"), $data->title . $icon, array("title" => lang('task_info') . " #$data->id", "data-post-id" => $data->id, "class" => $unread_comments_class));

        $task_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $task_labels .= "<span class='label label-info clickable'  title='Label'>" . $label . "</span> ";
            }
        }
        $title .= "<span class='pull-right ml5'>" . $task_labels . "</span>";

        $task_point = "";
        if ($data->points > 1) {
            $task_point .= "<span class='label label-light clickable'  title='Point'>" . $data->points . "</span> ";
        }
        $title .= "<span class='pull-right'>" . $task_point . "</span>";

        $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);

        $assigned_to = "-";

        if ($data->assigned_to) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $assigned_to_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->assigned_to_user";
            $assigned_to = get_team_member_profile_link($data->assigned_to, $assigned_to_user);

            if ($data->user_type == "staff") {
                $assigned_to = get_team_member_profile_link($data->assigned_to, $assigned_to_user);
            } else {
                $assigned_to = get_client_contact_profile_link($data->assigned_to, $assigned_to_user);
            }
        }


        $collaborators = $this->_get_collaborators($data->collaborator_list);

        if (!$collaborators) {
            $collaborators = "-";
        }


        $checkbox_class = "checkbox-blank";
        if ($data->status_key_name === "done") {
            $checkbox_class = "checkbox-checked";
        }

        if ($this->login_user->user_type == "staff") {
            //show changeable status checkbox and link to team members
            $check_status = js_anchor("<span class='$checkbox_class'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id, "data-value" => $data->status_key_name === "done" ? "1" : "3", "data-act" => "update-task-status-checkbox")) . $data->id;
            $status = js_anchor($data->status_key_name ? lang($data->status_key_name) : $data->status_title, array('title' => "", "class" => "", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-task-status"));
        } else {
            //don't show clickable checkboxes/status to client
            if ($checkbox_class == "checkbox-blank") {
                $checkbox_class = "checkbox-un-checked";
            }
            $check_status = "<span class='$checkbox_class'></span> " . $data->id;
            $status = $data->status_key_name ? lang($data->status_key_name) : $data->status_title;
        }



        $deadline_text = "-";
        if ($data->deadline && is_date_exists($data->deadline)) {
            $deadline_text = format_to_date($data->deadline, false);
            if (get_my_local_time("Y-m-d") > $data->deadline && $data->status != "done") {
                $deadline_text = "<span class='text-danger'>" . $deadline_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->deadline && $data->status != "done") {
                $deadline_text = "<span class='text-warning'>" . $deadline_text . "</span> ";
            }
        }


        $start_date = "-";
        if (is_date_exists($data->start_date)) {
            $start_date = format_to_date($data->start_date, false);
        }

        $options = "";
        if ($this->can_edit_tasks()) {
            $options .= modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_task'), "data-post-id" => $data->id));
        }
        if ($this->can_delete_tasks()) {
            $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_task'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_task"), "data-action" => "delete"));
        }

        $row_data = array(
            $data->status_color,
            $check_status,
            $title,
            $data->start_date,
            $start_date,
            $data->deadline,
            $deadline_text,
            $project_title,
            $assigned_to,
            $collaborators,
            $status
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = $options;

        return $row_data;
    }

    private function _get_collaborators($collaborator_list, $clickable = true) {
        $collaborators = "";
        if ($collaborator_list) {

            $collaborators_array = explode(",", $collaborator_list);
            foreach ($collaborators_array as $collaborator) {
                $collaborator_parts = explode("--::--", $collaborator);

                $collaborator_id = get_array_value($collaborator_parts, 0);
                $collaborator_name = get_array_value($collaborator_parts, 1);

                $image_url = get_avatar(get_array_value($collaborator_parts, 2));
                $collaboratr_image = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span>";

                if ($clickable) {
                    $collaborators .= get_team_member_profile_link($collaborator_id, $collaboratr_image, array("title" => $collaborator_name));
                } else {
                    $collaborators .= "<span title='$collaborator_name'>$collaboratr_image</span>";
                }
            }
        }
        return $collaborators;
    }

    /* load comments view */

    function comments($project_id) {
        $this->access_only_team_members();

        $options = array("project_id" => $project_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* load comments view */

    function customer_feedback($project_id) {
        $options = array("customer_feedback_id" => $project_id); //customer feedback id and project id is same
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['customer_feedback_id'] = $project_id;
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* save project comments */

    function save_comment() {
        $id = $this->input->post('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "project_comment");

        $project_id = $this->input->post('project_id');
        $task_id = $this->input->post('task_id');
        $file_id = $this->input->post('file_id');
        $customer_feedback_id = $this->input->post('customer_feedback_id');
        $comment_id = $this->input->post('comment_id');
        $description = $this->input->post('description');

        $data = array(
            "created_by" => $this->login_user->id,
            "created_at" => get_current_utc_time(),
            "project_id" => $project_id,
            "file_id" => $file_id ? $file_id : 0,
            "task_id" => $task_id ? $task_id : 0,
            "customer_feedback_id" => $customer_feedback_id ? $customer_feedback_id : 0,
            "comment_id" => $comment_id ? $comment_id : 0,
            "description" => $description
        );

        $data = clean_data($data);

        $data["files"] = $files_data; //don't clean serilized data

        $save_id = $this->Project_comments_model->save_comment($data, $id);
        if ($save_id) {
            $response_data = "";
            $options = array("id" => $save_id);

            if ($this->input->post("reload_list")) {
                $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
                $response_data = $this->load->view("projects/comments/comment_list", $view_data, true);
            }
            echo json_encode(array("success" => true, "data" => $response_data, 'message' => lang('comment_submited')));


            $comment_info = $this->Project_comments_model->get_one($save_id);

            $notification_options = array("project_id" => $comment_info->project_id, "project_comment_id" => $save_id);

            if ($comment_info->file_id) { //file comment
                $notification_options["project_file_id"] = $comment_info->file_id;
                log_notification("project_file_commented", $notification_options);
            } else if ($comment_info->task_id) { //task comment
                $notification_options["task_id"] = $comment_info->task_id;
                log_notification("project_task_commented", $notification_options);
            } else if ($comment_info->customer_feedback_id) {  //customer feedback comment
                if ($comment_id) {
                    log_notification("project_customer_feedback_replied", $notification_options);
                } else {
                    log_notification("project_customer_feedback_added", $notification_options);
                }
            } else {  //project comment
                if ($comment_id) {
                    log_notification("project_comment_replied", $notification_options);
                } else {
                    log_notification("project_comment_added", $notification_options);
                }
            }
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function delete_comment($id = 0) {

        if (!$id) {
            exit();
        }

        $comment_info = $this->Project_comments_model->get_one($id);

        //only admin and creator can delete the comment
        if (!($this->login_user->is_admin || $comment_info->created_by == $this->login_user->id)) {
            redirect("forbidden");
        }


        //delete the comment and files
        if ($this->Project_comments_model->delete($id) && $comment_info->files) {

            //delete the files
            $file_path = get_setting("timeline_file_path");
            $files = unserialize($comment_info->files);

            foreach ($files as $file) {
                delete_app_files($file_path, array($file));
            }
        }
    }

    /* load all replies of a comment */

    function view_comment_replies($comment_id) {
        $view_data['reply_list'] = $this->Project_comments_model->get_details(array("comment_id" => $comment_id))->result();
        $this->load->view("projects/comments/reply_list", $view_data);
    }

    /* show comment reply form */

    function comment_reply_form($comment_id, $type = "project", $type_id = 0) {
        $view_data['comment_id'] = $comment_id;

        if ($type === "project") {
            $view_data['project_id'] = $type_id;
        } else if ($type === "task") {
            $view_data['task_id'] = $type_id;
        } else if ($type === "file") {
            $view_data['file_id'] = $type_id;
        } else if ($type == "customer_feedback") {
            $view_data['project_id'] = $type_id;
        }
        $this->load->view("projects/comments/reply_form", $view_data);
    }

    /* load files view */

    function files($project_id) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_files()) {
            redirect("forbidden");
        }

        $view_data['can_add_files'] = $this->can_add_files();
        $options = array("project_id" => $project_id);
        $view_data['files'] = $this->Project_files_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/files/index", $view_data);
    }

    function view_file($file_id = 0) {
        $file_info = $this->Project_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {

            $this->init_project_permission_checker($file_info->project_id);

            if (!$this->can_view_files()) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = $this->can_comment_on_files();

            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_setting("project_file_path") . $file_info->project_id . "/");

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;

            $view_data["file_info"] = $file_info;
            $options = array("file_id" => $file_id);
            $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
            $view_data['file_id'] = $file_id;
            $view_data['project_id'] = $file_info->project_id;
            $this->load->view("projects/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->Project_files_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_files()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $this->load->view('projects/files/modal_form', $view_data);
    }

    /* save project file data and move temp file to parmanent file directory */

    function save_file() {

        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_files()) {
            redirect("forbidden");
        }


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_setting("project_file_path") . $project_id . "/";

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path, "");
                if ($file_info) {
                    $data = array(
                        "project_id" => $project_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );

                    $data = clean_data($data);

                    $success = $this->Project_files_model->save($data);

                    log_notification("project_file_added", array("project_id" => $project_id, "project_file_id" => $success));
                } else {
                    $success = false;
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $description = $this->input->post('description');
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];

                    $file_info = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($file_info) {
                        $data = array(
                            "project_id" => $project_id,
                            "file_name" => get_array_value($file_info, 'file_name'),
                            "file_id" => get_array_value($file_info, 'file_id'),
                            "service_type" => get_array_value($file_info, 'service_type'),
                            "description" => get_array_value($description, $key),
                            "file_size" => $file_size,
                            "created_at" => $now,
                            "uploaded_by" => $this->login_user->id
                        );
                        $success = $this->Project_files_model->save($data);

                        log_notification("project_file_added", array("project_id" => $project_id, "project_file_id" => $success));
                    }
                }
            }
        }

        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for project */

    function validate_project_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->Project_files_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_files()) {
            redirect("forbidden");
        }

        if ($this->Project_files_model->delete($id)) {

            //delete the files
            $file_path = get_setting("project_file_path");
            delete_app_files($file_path . $info->project_id . "/", array(make_array_of_file($info)));

            log_notification("project_file_deleted", array("project_id" => $info->project_id, "project_file_id" => $id));
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->Project_files_model->get_one($id);

        $this->init_project_permission_checker($file_info->project_id);
        if (!$this->can_view_files()) {
            redirect("forbidden");
        }

        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->project_id . "/" . $file_info->file_name, "file_id" => $file_info->file_id, "service_type" => $file_info->service_type)));

        //delete the file
        download_app_files(get_setting("project_file_path"), $file_data);
    }

    /* download multiple files as zip */

    function download_multiple_files($files_ids = "") {

        if ($files_ids) {


            $files_ids_array = explode('-', $files_ids);

            $files = $this->Project_files_model->get_files($files_ids_array);

            if ($files) {
                $file_path_array = array();
                $project_id = 0;

                foreach ($files->result() as $file_info) {

                    //we have to check the permission for each file
                    //initialize the permission check only if the project id is different

                    if ($project_id != $file_info->project_id) {
                        $this->init_project_permission_checker($file_info->project_id);
                        $project_id = $file_info->project_id;
                    }

                    if (!$this->can_view_files()) {
                        redirect("forbidden");
                    }

                    $file_path_array[] = array("file_name" => $file_info->project_id . "/" . $file_info->file_name, "file_id" => $file_info->file_id, "service_type" => $file_info->service_type);
                }

                $serialized_file_data = serialize($file_path_array);

                download_app_files(get_setting("project_file_path"), $serialized_file_data);
            }
        }
    }

    /* batch update modal form */

    function batch_update_modal_form($task_ids = "") {
        $this->access_only_team_members();
        $project_id = $this->input->post("project_id");

        if ($task_ids && $project_id) {
            $view_data = $this->_initialize_all_related_data_of_project($project_id);
            $view_data["task_ids"] = $task_ids;
            $view_data["project_id"] = $project_id;

            $this->load->view("projects/tasks/batch_update/modal_form", $view_data);
        } else {
            show_404();
        }
    }

    /* save batch tasks */

    function save_batch_update() {
        $this->access_only_team_members();

        validate_submitted_data(array(
            "project_id" => "required|numeric"
        ));

        $project_id = $this->input->post('project_id');
        $this->init_project_permission_checker($project_id);

        if (!$this->can_edit_tasks()) {
            redirect("forbidden");
        }

        $batch_fields = $this->input->post("batch_fields");
        if ($batch_fields) {
            $fields_array = explode('-', $batch_fields);

            $data = array();
            foreach ($fields_array as $field) {
                if ($field != "project_id") {
                    $data[$field] = $this->input->post($field);
                }
            }

            $data = clean_data($data);

            $task_ids = $this->input->post("task_ids");
            if ($task_ids) {
                $tasks_ids_array = explode('-', $task_ids);

                foreach ($tasks_ids_array as $id) {
                    unset($data["activity_log_id"]);

                    //check user's permission on this task's project
                    $task_info = $this->Tasks_model->get_one($id);
                    $this->init_project_permission_checker($task_info->project_id);
                    if (!$this->can_edit_tasks()) {
                        redirect("forbidden");
                    }

                    $save_id = $this->Tasks_model->save($data, $id);

                    if ($save_id) {
                        //we don't send notification if the task is changing on the same position
                        $activity_log_id = get_array_value($data, "activity_log_id");
                        if ($activity_log_id) {
                            log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => $activity_log_id));
                        }
                    }
                }

                echo json_encode(array("success" => true, 'message' => lang('record_saved')));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => lang('no_field_has_selected')));
            return false;
        }
    }

    /* download files by zip */

    function download_comment_files($id) {

        $info = $this->Project_comments_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);
        if ($this->login_user->user_type == "client" && !$this->is_clients_project) {

            redirect("forbidden");
        } else if ($this->login_user->user_type == "user" && !$this->can_view_tasks()) {
            redirect("forbidden");
        }

        download_app_files(get_setting("timeline_file_path"), $info->files);
    }

    /* list of files, prepared for datatable  */

    function files_list_data($project_id = 0) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_files()) {
            redirect("forbidden");
        }


        $options = array("project_id" => $project_id);
        $list_data = $this->Project_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of file list table */

    private function _file_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_files_model->get_details($options)->row();

        $this->init_project_permission_checker($data->project_id);
        return $this->_make_file_row($data);
    }

    /* prepare a row of file list table */

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        if ($data->uploaded_by_user_type == "staff") {
            $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);
        } else {
            $uploaded_by = get_client_contact_profile_link($data->uploaded_by, $uploaded_by);
        }

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "1", "data-url" => get_uri("projects/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("projects/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
        if ($this->can_delete_files()) {
            $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_file"), "data-action" => "delete-confirmation"));
        }

        //show checkmark to download multiple files
        $checkmark = js_anchor("<span class='checkbox-blank'></span>", array('title' => "", "class" => "", "data-id" => $data->id, "data-act" => "download-multiple-file-checkbox")) . $data->id;

        return array(
            $checkmark,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    /* load notes view */

    function notes($project_id) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/notes/index", $view_data);
    }

    /* load history view */

    function history($offset = 0, $log_for = "", $log_for_id = "", $log_type = "", $log_type_id = "") {
        if ($this->login_user->user_type !== "staff" && ($this->login_user->user_type == "client" && get_setting("client_can_view_activity") !== "1")) {
            redirect("forbidden");
        }

        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => $log_for, "log_for_id" => $log_for_id, "log_type" => $log_type, "log_type_id" => $log_type_id, "limit" => 20, "offset" => $offset);
        $this->load->view("projects/history/index", $view_data);
    }

    /* load project members view */

    function members($project_id = 0) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/project_members/index", $view_data);
    }

    /* load payments tab  */

    function payments($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();
            $view_data['project_id'] = $project_id;
            $this->load->view("projects/payments/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_id'] = $project_id;
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("projects/invoices/index", $view_data);
        }
    }

    /* load payments tab  */

    function expenses($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_id'] = $project_id;
            $this->load->view("projects/expenses/index", $view_data);
        }
    }

    //save project status
    function change_status($project_id, $status) {
        if ($project_id && $this->can_create_projects() && ($status == "completed" || $status == "hold" || $status == "canceled" || $status == "open" )) {
            $status_data = array("status" => $status);
            $save_id = $this->Projects_model->save($status_data, $project_id);

            //send notification
            if ($status == "completed") {
                log_notification("project_completed", array("project_id" => $save_id));
            }
        }
    }

    //load gantt tab
    function gantt($project_id = 0) {

        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            if (!$this->can_view_gantt()) {
                redirect("forbidden");
            }

            $view_data['project_id'] = $project_id;

            //prepare members list
            $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
            $view_data['project_members_dropdown'] = $this->_get_project_members_dropdown_list($project_id);

            $view_data['show_project_members_dropdown'] = true;
            if ($this->login_user->user_type == "client") {
                $view_data['show_project_members_dropdown'] = false;
            }


            $statuses = $this->Task_status_model->get_details()->result();

            $status_dropdown = array(
                array("id" => "", "text" => "- " . lang("status") . " -")
            );

            foreach ($statuses as $status) {
                $status_dropdown[] = array("id" => $status->id, "text" => ( $status->key_name ? lang($status->key_name) : $status->title));
            }

            $view_data['status_dropdown'] = json_encode($status_dropdown);

            $this->load->view("projects/gantt/index", $view_data);
        }
    }

    //prepare gantt data for gantt chart
    function gantt_data($project_id = 0, $group_by = "milestones", $filter_id = 0, $status = "") {
        if ($project_id) {
            $this->init_project_permission_checker($project_id);
            if (!$this->can_view_gantt()) {
                redirect("forbidden");
            }
        }

        $options = array(
            "status_id" => $status,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id()
        );


        if (!$status) {
            $options["exclude_status"] = 3; //don't show completed tasks by default
        }


        if ($group_by == "milestones") {
            $options["milestone_id"] = $filter_id;
        } else if ($group_by == "members" || $group_by == "projects") {
            $options["assigned_to"] = $filter_id;
        }

        $options["project_id"] = $project_id;

        if ($this->login_user->user_type == "staff" && !$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $gantt_data = $this->Projects_model->get_gantt_data($options);
        $now = get_current_utc_time("Y-m-d");

        $group_array = array();
        $series = array();

        foreach ($gantt_data as $data) {

            $start_date = is_date_exists($data->start_date) ? $data->start_date : $now;
            $end_date = is_date_exists($data->end_date) ? $data->end_date : $data->milestone_due_date;

            if (!is_date_exists($end_date)) {
                $end_date = $start_date;
            }

            $group_id = 0;

            if ($group_by === "milestones") {
                $group_id = $data->milestone_id;
                $group_array[$group_id] = array("id" => $group_id, "name" => $data->milestone_title);
            } else if ($group_by === "members") {
                $group_id = $data->assigned_to;
                $group_array[$data->assigned_to] = array("id" => $group_id, "name" => $data->assigned_to_name);
            } else if ($group_by === "projects") {
                $group_id = $data->project_id;
                $group_array[$data->project_id] = array("id" => $group_id, "name" => $data->project_name);
            }

            $color = $data->status_color;

            //has deadline? change the color of date based on status
            if ($data->status_id == "1" && is_date_exists($data->end_date) && get_my_local_time("Y-m-d") > $data->end_date) {
                $color = "#d9534f";
            }

            $series[$group_id][] = array("name" => modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id)), "start" => $start_date, "end" => $end_date, "color" => $color);
        }

        $gantt = array();
        foreach ($group_array as $group_value) {
            $gantt_section = $group_value;

            if (!get_array_value($group_value, "name")) {
                $gantt_section["name"] = lang("not_specified");
            } else {
                $gantt_section["name"] = get_array_value($group_value, "name");
            }

            $gantt_section["id"] = get_array_value($group_value, "id");
            $gantt_section["series"] = get_array_value($series, get_array_value($group_value, "id"));
            $gantt[] = $gantt_section;
        }
        echo json_encode($gantt);
    }

    /* load project settings modal */

    function settings_modal_form() {

        $project_id = $this->input->post('project_id');

        //onle team members who can create project, he/she can update settings
        if (!$project_id || !($this->login_user->user_type == "staff" && $this->can_create_projects())) {
            redirect("forbidden");
        }


        $this->init_project_settings($project_id);

        $view_data['project_id'] = $project_id;

        $this->load->view('projects/settings/modal_form', $view_data);
    }

    /* save project settings */

    function save_settings() {

        $project_id = $this->input->post('project_id');

        //onle team members who can create project, he/she can update settings
        if (!$project_id || !($this->login_user->user_type == "staff" && $this->can_create_projects())) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "project_id" => "required|numeric"
        ));

        $settings = array("client_can_view_timesheet");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (!$value) {
                $value = "";
            }

            $this->Project_settings_model->save_setting($project_id, $setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    /* checklist */

    function save_checklist_item() {

        $task_id = $this->input->post("task_id");

        validate_submitted_data(array(
            "task_id" => "required|numeric"
        ));

        $project_id = $this->Tasks_model->get_one($task_id)->project_id;

        $this->init_project_permission_checker($project_id);

        if ($task_id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        }

        $data = array(
            "task_id" => $task_id,
            "title" => $this->input->post("checklist-add-item")
        );

        $save_id = $this->Checklist_items_model->save($data);

        if ($save_id) {
            $item_info = $this->Checklist_items_model->get_one($save_id);
            echo json_encode(array("success" => true, "data" => $this->_make_checklist_item_row($item_info), 'id' => $save_id));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    private function _make_checklist_item_row($data = array(), $return_type = "row") {
        $checkbox_class = "checkbox-blank";
        $title_class = "";
        $is_checked_value = 1;
        $title_value = link_it($data->title);

        if ($data->is_checked == 1) {
            $is_checked_value = 0;
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
            $title_value = $data->title;
        }

        $status = js_anchor("<span class='$checkbox_class'></span>", array('title' => "", "data-id" => $data->id, "data-value" => $is_checked_value, "data-act" => "update-checklist-item-status-checkbox"));
        if (!$this->can_edit_tasks()) {
            $status = "";
        }

        $title = "<span class='font-13 $title_class'>" . $title_value . "</span>";

        $delete = ajax_anchor(get_uri("projects/delete_checklist_item/$data->id"), "<i class='fa fa-times pull-right p3'></i>", array("class" => "delete-checklist-item", "title" => lang("delete_checklist_item"), "data-fade-out-on-success" => "#checklist-item-row-$data->id"));
        if (!$this->can_edit_tasks()) {
            $delete = "";
        }

        if ($return_type == "data") {
            return $status . $title . $delete;
        }

        return "<div id='checklist-item-row-$data->id' class='list-group-item mb5 checklist-item-row' data-id='$data->id'>" . $status . $title . $delete . "</div>";
    }

    function save_checklist_item_status($id = 0) {
        $task_id = $this->Checklist_items_model->get_one($id)->task_id;
        $project_id = $this->Tasks_model->get_one($task_id)->project_id;

        $this->init_project_permission_checker($project_id);

        if (!$this->can_edit_tasks()) {
            redirect("forbidden");
        }

        $data = array(
            "is_checked" => $this->input->post('value')
        );

        $save_id = $this->Checklist_items_model->save($data, $id);

        if ($save_id) {
            $item_info = $this->Checklist_items_model->get_one($save_id);
            echo json_encode(array("success" => true, "data" => $this->_make_checklist_item_row($item_info, "data"), 'id' => $save_id));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    function save_checklist_items_sort() {
        $sort_values = $this->input->post("sort_values");
        if ($sort_values) {
            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);

            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                validate_numeric_value($id);

                $data = array("sort" => $sort);
                $this->Checklist_items_model->save($data, $id);
            }
        }
    }

    function delete_checklist_item($id) {

        $task_id = $this->Checklist_items_model->get_one($id)->task_id;
        $project_id = $this->Tasks_model->get_one($task_id)->project_id;

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        }

        if ($this->Checklist_items_model->delete($id)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    /* get member suggestion with start typing '@' */

    function get_member_suggestion_to_mention() {

        validate_submitted_data(array(
            "project_id" => "required|numeric"
        ));

        $project_id = $this->input->post("project_id");

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id, "", $this->can_access_clients())->result();
        $project_members_dropdown = array();
        foreach ($project_members as $member) {
            $project_members_dropdown[] = array("name" => $member->member_name, "content" => "@[" . $member->member_name . " :" . $member->user_id . "]");
        }

        if ($project_members_dropdown) {
            echo json_encode(array("success" => TRUE, "data" => $project_members_dropdown));
        } else {
            echo json_encode(array("success" => FALSE));
        }
    }

    //reset projects dropdown on changing of client 
    function get_projects_of_selected_client_for_filter() {
        $this->access_only_team_members();
        $client_id = $this->input->post("client_id");
        if ($client_id) {
            $projects = $this->Projects_model->get_all_where(array("client_id" => $client_id, "deleted" => 0), "", "", "title")->result();
            $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
            foreach ($projects as $project) {
                $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
            }
            echo json_encode($projects_dropdown);
        } else {
            //we have show all projects by de-selecting client
            echo json_encode($this->_get_all_projects_dropdown_list());
        }
    }

    //get clients dropdown
    private function _get_clients_dropdown() {
        $clients_dropdown = array(array("id" => "", "text" => "- " . lang("client") . " -"));
        $clients = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        foreach ($clients as $key => $value) {
            $clients_dropdown[] = array("id" => $key, "text" => $value);
        }
        return $clients_dropdown;
    }

    //show timesheets chart
    function timesheet_chart($project_id = 0) {
        $this->access_only_team_members();
        $members = $this->_get_members_to_manage_timesheet();

        $view_data['members_dropdown'] = json_encode($this->_prepare_members_dropdown_for_timesheet_filter($members));
        $view_data['projects_dropdown'] = json_encode($this->_get_all_projects_dropdown_list_for_timesheets_filter());
        $view_data["project_id"] = $project_id;

        $this->load->view("projects/timesheets/timesheet_chart", $view_data);
    }

    //load global gantt view
    function all_gantt() {
        $this->access_only_team_members();

        //only admin/ the user has permission to manage all projects, can see all projects, other team mebers can see only their own projects.
        $options = array("status" => "open");
        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $projects = $this->Projects_model->get_details($options)->result();


        if ($projects) {
            $this->init_project_permission_checker(get_array_value($projects, 0)->id);
            if (!$this->can_view_gantt()) {
                redirect("forbidden");
            }
        }

        //get projects dropdown
        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
        }

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);

        $project_id = 0;
        $view_data['project_id'] = $project_id;

        //prepare members list
        $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
        $view_data['project_members_dropdown'] = $this->_get_project_members_dropdown_list();

        $view_data['show_project_members_dropdown'] = true;
        if ($this->login_user->user_type == "client") {
            $view_data['show_project_members_dropdown'] = false;
        }

        $statuses = $this->Task_status_model->get_details()->result();

        $status_dropdown = array(
            array("id" => "", "text" => "- " . lang("status") . " -")
        );

        foreach ($statuses as $status) {
            $status_dropdown[] = array("id" => $status->id, "text" => ( $status->key_name ? lang($status->key_name) : $status->title));
        }

        $view_data['status_dropdown'] = json_encode($status_dropdown);

        $this->template->rander("projects/gantt/index", $view_data);
    }

    //timesheets chart data
    function timesheet_chart_data($project_id = 0) {
        if (!$project_id) {
            $project_id = $this->input->post("project_id");
        }

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise

        if (!$this->can_view_timesheet($project_id, true)) {
            redirect("forbidden");
        }

        $timesheets = array();
        $timesheets_array = array();
        $ticks = array();

        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $user_id = $this->input->post("user_id");

        $options = array(
            "start_date" => $start_date,
            "end_date" => $end_date,
            "user_id" => $user_id,
            "project_id" => $project_id
        );

        //get allowed member ids
        $members = $this->_get_members_to_manage_timesheet();
        if ($members != "all" && $this->login_user->user_type == "staff") {
            //if user has permission to access all members, query param is not required
            //client can view all timesheet
            $options["allowed_members"] = $members;
        }

        $timesheets_result = $this->Timesheets_model->get_timesheet_statistics($options)->result();

        $days_of_month = date("t", strtotime($start_date));

        for ($i = 0; $i <= $days_of_month; $i++) {
            $timesheets[$i] = 0;
        }

        foreach ($timesheets_result as $value) {
            $timesheets[$value->day * 1] = $value->total_sec / 60;
        }

        foreach ($timesheets as $key => $value) {
            $timesheets_array[] = array($key, $value);
        }

        for ($i = 0; $i <= $days_of_month; $i++) {
            $title = "";
            if ($i === 1) {
                $title = "01";
            } else if ($i === 5) {
                $title = "05";
            } else if ($i === 10) {
                $title = "10";
            } else if ($i === 15) {
                $title = "15";
            } else if ($i === 20) {
                $title = "20";
            } else if ($i === 25) {
                $title = "25";
            } else if ($i === 30) {
                $title = "30";
            }
            $ticks[] = array($i, $title);
        }

        echo json_encode(array("timesheets" => $timesheets_array, "ticks" => $ticks));
    }

    function save_dependency_tasks() {
        $task_id = $this->input->post("task_id");
        if ($task_id) {
            $dependency_task = $this->input->post("dependency_task");
            $dependency_type = $this->input->post("dependency_type");

            if ($dependency_task) {
                //add the new task with old
                $task_info = $this->Tasks_model->get_one($task_id);
                $this->init_project_permission_checker($task_info->project_id);
                if (!$this->can_edit_tasks()) {
                    redirect("forbidden");
                }

                $dependency_tasks = $task_info->$dependency_type;
                if ($dependency_tasks) {
                    $dependency_tasks .= "," . $dependency_task;
                } else {
                    $dependency_tasks = $dependency_task;
                }

                $data = array(
                    $dependency_type => $dependency_tasks
                );

                $data = clean_data($data);

                $this->Tasks_model->update_custom_data($data, $task_id);
                $dependency_task_info = $this->Tasks_model->get_details(array("id" => $dependency_task))->row();

                echo json_encode(array("success" => true, "data" => $this->_make_dependency_tasks_row_data($dependency_task_info, $task_id, $dependency_type), 'message' => lang('record_saved')));
            }
        }
    }

    private function _get_all_dependency_for_this_task($task_id) {
        $task_info = $this->Tasks_model->get_one($task_id);
        $blocked_by = $this->_get_all_dependency_for_this_task_specific($task_info->blocked_by, $task_id, "blocked_by");
        $blocking = $this->_get_all_dependency_for_this_task_specific($task_info->blocking, $task_id, "blocking");

        $all_tasks = $blocked_by;
        if ($blocking) {
            if ($all_tasks) {
                $all_tasks .= "," . $blocking;
            } else {
                $all_tasks = $blocking;
            }
        }

        return $all_tasks;
    }

    function get_existing_dependency_tasks($task_id = 0) {
        if ($task_id) {
            $model_info = $this->Tasks_model->get_details(array("id" => $task_id))->row();

            $this->init_project_permission_checker($model_info->project_id);

            if (!$this->can_view_tasks($model_info->project_id, $task_id)) {
                redirect("forbidden");
            }

            $all_dependency_tasks = $this->_get_all_dependency_for_this_task($task_id);

            //add this task id
            if ($all_dependency_tasks) {
                $all_dependency_tasks .= "," . $task_id;
            } else {
                $all_dependency_tasks = $task_id;
            }

            //make tasks dropdown
            $tasks_dropdown = array();
            $tasks = $this->Tasks_model->get_details(array("project_id" => $model_info->project_id, "exclude_task_ids" => $all_dependency_tasks))->result();
            foreach ($tasks as $task) {
                $tasks_dropdown[] = array("id" => $task->id, "text" => $task->id . " - " . $task->title);
            }

            echo json_encode(array("success" => true, "tasks_dropdown" => $tasks_dropdown));
        }
    }

    private function _get_all_dependency_for_this_task_specific($task_ids = "", $task_id = 0, $type = "") {
        if ($task_id && $type) {
            //find the other tasks dependency with this task
            $dependency_tasks = $this->Tasks_model->get_all_dependency_for_this_task($task_id, $type);

            if ($dependency_tasks) {
                if ($task_ids) {
                    $task_ids .= "," . $dependency_tasks;
                } else {
                    $task_ids = $dependency_tasks;
                }
            }

            return $task_ids;
        }
    }

    private function _make_dependency_tasks_view_data($task_ids = "", $task_id = 0, $type = "") {
        if ($task_ids) {
            $tasks = "";

            $tasks_list = $this->Tasks_model->get_details(array("task_ids" => $task_ids))->result();

            foreach ($tasks_list as $task) {
                $tasks .= $this->_make_dependency_tasks_row_data($task, $task_id, $type);
            }

            return $tasks;
        }
    }

    private function _make_dependency_tasks_row_data($task_info, $task_id, $type) {
        $tasks = "";

        $tasks .= "<div id='dependency-task-row-$task_info->id' class='list-group-item mb5 dependency-task-row' style='border-left: 5px solid $task_info->status_color !important;'>";
        $tasks .= modal_anchor(get_uri("projects/task_view"), $task_info->title, array("data-post-id" => $task_info->id));

        if ($this->can_edit_tasks()) {
            $tasks .= ajax_anchor(get_uri("projects/delete_dependency_task/$task_info->id/$task_id/$type"), "<i class='fa fa-times pull-right p3'></i>", array("class" => "delete-dependency-task", "title" => lang("delete"), "data-fade-out-on-success" => "#dependency-task-row-$task_info->id", "data-dependency-type" => $type));
        }

        $tasks .= "</div>";

        return $tasks;
    }

    function delete_dependency_task($dependency_task_id, $task_id, $type) {
        $task_info = $this->Tasks_model->get_one($task_id);
        $this->init_project_permission_checker($task_info->project_id);
        if (!$this->can_edit_tasks()) {
            redirect("forbidden");
        }

        //the dependency task could be resided in both place
        //so, we've to search on both        
        $dependency_tasks_of_own = $task_info->$type;
        if ($type == "blocked_by") {
            $dependency_tasks_of_others = $this->Tasks_model->get_one($dependency_task_id)->blocking;
        } else {
            $dependency_tasks_of_others = $this->Tasks_model->get_one($dependency_task_id)->blocked_by;
        }

        //first check if it contains only a single task
        if (!strpos($dependency_tasks_of_own, ',') && $dependency_tasks_of_own == $dependency_task_id) {
            $data = array($type => "");
            $this->Tasks_model->update_custom_data($data, $task_id);
        } else if (!strpos($dependency_tasks_of_others, ',') && $dependency_tasks_of_others == $task_id) {
            $data = array((($type == "blocked_by") ? "blocking" : "blocked_by") => "");
            $this->Tasks_model->update_custom_data($data, $dependency_task_id);
        } else {
            //have multiple values
            $dependency_tasks_of_own_array = explode(',', $dependency_tasks_of_own);
            $dependency_tasks_of_others_array = explode(',', $dependency_tasks_of_others);

            if (in_array($dependency_task_id, $dependency_tasks_of_own_array)) {
                unset($dependency_tasks_of_own_array[array_search($dependency_task_id, $dependency_tasks_of_own_array)]);
                $dependency_tasks_of_own_array = implode(',', $dependency_tasks_of_own_array);
                $data = array($type => $dependency_tasks_of_own_array);
                $this->Tasks_model->update_custom_data($data, $task_id);
            } else if (in_array($task_id, $dependency_tasks_of_others_array)) {
                unset($dependency_tasks_of_others_array[array_search($task_id, $dependency_tasks_of_others_array)]);
                $dependency_tasks_of_others_array = implode(',', $dependency_tasks_of_others_array);
                $data = array((($type == "blocked_by") ? "blocking" : "blocked_by") => $dependency_tasks_of_others_array);
                $this->Tasks_model->update_custom_data($data, $dependency_task_id);
            }
        }

        echo json_encode(array("success" => true));
    }

}

/* End of file projects.php */
/* Location: ./application/controllers/projects.php */