<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Google_api extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library("google");
        $this->load->library("google_calendar");
    }

    function index() {
        redirect("google_api/authorize");
    }

    //authorize google drive
    function authorize() {
        $this->access_only_admin();
        $this->google->authorize();
    }

    //get access token of drive and save
    function save_access_token() {
        $this->access_only_admin();

        if (!empty($_GET)) {
            $this->google->save_access_token(get_array_value($_GET, 'code'));
            redirect("settings/integration/google_drive");
        }
    }

    //authorize google calendar
    function authorize_calendar() {
        $this->google_calendar->authorize($this->login_user->id);
    }

    //get access code and save
    function save_access_token_of_calendar() {
        if (!empty($_GET)) {
            $this->google_calendar->save_access_token(get_array_value($_GET, 'code'), $this->login_user->id);
            redirect("events");
        }
    }

}

/* End of file Google_api.php */
/* Location: ./application/controllers/Google_api.php */