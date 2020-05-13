<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class About extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("Pages_model");
    }

    function index($slug = "") {
        if ($slug) {
            $options = array("slug" => $slug);
            $view_data["model_info"] = $this->Pages_model->get_details($options)->row();

            $view_data['topbar'] = "includes/public/topbar";
            $view_data['left_menu'] = false;

            $this->template->rander("about/index", $view_data);
        }
    }

}

/* End of file About.php */
/* Location: ./application/controllers/About.php */