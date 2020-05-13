<?php
//get the array of hidden menu
$hidden_menu = explode(",", get_setting("hidden_client_menus"));
$permissions = $this->login_user->permissions;

$links = "";

if (($this->login_user->user_type == "staff" && ($this->login_user->is_admin || get_array_value($permissions, "can_manage_all_projects") == "1" || get_array_value($permissions, "can_create_tasks") == "1")) || ($this->login_user->user_type == "client" && get_setting("client_can_create_tasks"))) {
    //add tasks 
    $links .= modal_anchor(get_uri("projects/task_modal_form"), lang('add_task'), array("class" => "clearfix", "title" => lang('add_task'), "id" => "js-quick-add-task"));

    //add multiple tasks
    $links .= modal_anchor(get_uri("projects/task_modal_form"), lang('add_multiple_tasks'), array("class" => "clearfix", "title" => lang('add_multiple_tasks'), "data-post-add_type" => "multiple", "id" => "js-quick-add-multiple-task"));
}

//add project time
if ($this->login_user->user_type == "staff") {
    $links .= modal_anchor(get_uri("projects/timelog_modal_form"), lang('add_project_time'), array("class" => "clearfix", "title" => lang('add_project_time'), "id" => "js-quick-add-project-time"));
}

//add event
if (get_setting("module_event") == "1" && (($this->login_user->user_type == "client" && !in_array("events", $hidden_menu)) || $this->login_user->user_type == "staff")) {
    $links .= modal_anchor(get_uri("events/modal_form"), lang('add_event'), array("class" => "clearfix", "title" => lang('add_event'), "data-post-client_id" => $this->login_user->user_type == "client" ? $this->login_user->client_id : "", "id" => "js-quick-add-event"));
}

//add note
if (get_setting("module_note") == "1" && $this->login_user->user_type == "staff") {
    $links .= modal_anchor(get_uri("notes/modal_form"), lang('add_note'), array("class" => "clearfix", "title" => lang('add_note'), "id" => "js-quick-add-note"));
}

//add todo
if (get_setting("module_todo") == "1") {
    $links .= modal_anchor(get_uri("todo/modal_form"), lang("add_to_do"), array("class" => "clearfix", "title" => lang('add_to_do'), "id" => "js-quick-add-to-do"));
}

//add ticket
if (get_setting("module_ticket") == "1" && ($this->login_user->is_admin || get_array_value($permissions, "ticket"))) {
    $links .= modal_anchor(get_uri("tickets/modal_form"), lang('add_ticket'), array("class" => "clearfix", "title" => lang('add_ticket'), "id" => "js-quick-add-ticket"));
}

if ($links) {
    ?>
    <li class="quick-add-option">
        <?php echo js_anchor("<i class='fa fa-plus-circle'></i>", array("id" => "quick-add-icon", "class" => "dropdown-toggle", "data-toggle" => "dropdown")); ?>

        <ul class="dropdown-menu p0 w200">
            <li>
                <?php echo $links; ?></li>
        </ul>
    </li>
    <?php
} 
