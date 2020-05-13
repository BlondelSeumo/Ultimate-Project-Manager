<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lead_status extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
        $this->template->rander("lead_status/index");
    }

    function modal_form() {
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Lead_status_model->get_one($this->input->post('id'));
        $this->load->view('lead_status/modal_form', $view_data);
    }

    function save() {
        validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->input->post('id');

        $data = array(
            "color" => $this->input->post('color'),
            "title" => $this->input->post('title')
        );

        if (!$id) {
            //get sort value
            $max_sort_value = $this->Lead_status_model->get_max_sort_value();
            $data["sort"] = $max_sort_value * 1 + 1; //increase sort value
        }

        $save_id = $this->Lead_status_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //update the sort value for the fields
    function update_field_sort_values($id = 0) {

        $sort_values = $this->input->post("sort_values");
        if ($sort_values) {

            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);


            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                $data = array("sort" => $sort);
                $this->Lead_status_model->save($data, $id);
            }
        }
    }

    function delete() {
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        if ($this->input->post('undo')) {
            if ($this->Lead_status_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Lead_status_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    function list_data() {
        $list_data = $this->Lead_status_model->get_details()->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Lead_status_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        $edit = modal_anchor(get_uri("lead_status/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_lead_status'), "data-post-id" => $data->id));

        $delete_attributes = array('title' => lang('delete_lead_status'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("lead_status/delete"), "data-action" => "delete-confirmation");
        if ($data->total_leads) {
            //the status shouldn't be deleted if there is any leads
            $delete_attributes = array('title' => lang('there_has_leads_with_this_status'), "class" => "delete not-clickable text-off",);
        }

        $delete = js_anchor("<i class='fa fa-times fa-fw'></i>", $delete_attributes);

        return array(
            $data->sort,
            "<div class='pt10 pb10 field-row'  data-id='$data->id'><i class='fa fa-bars pull-left move-icon'></i> <span style='background-color:" . $data->color . "' class='color-tag  pull-left'></span>" . $data->title . '</div>',
            $edit . $delete
        );
    }

}

/* End of file Lead_status.php */
/* Location: ./application/controllers/Lead_status.php */