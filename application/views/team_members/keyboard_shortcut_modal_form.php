<?php
//get the array of hidden menu
$hidden_menu = explode(",", get_setting("hidden_client_menus"));
$permissions = $this->login_user->permissions;
?>

<div id="keyboard-shortcut-modal-form" class="modal-body clearfix general-form white">
    <?php if (($this->login_user->user_type == "staff" && ($this->login_user->is_admin || get_array_value($permissions, "can_manage_all_projects") == "1" || get_array_value($permissions, "can_create_tasks") == "1")) || ($this->login_user->user_type == "client" && get_setting("client_can_create_tasks"))) { ?>
        <div class = "form-group">
            <label for = "add_new_task" class = "col-md-10"><?php echo lang('add_new_task'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">t</span>
            </div>
        </div>
        <div class="form-group">
            <label for="add_multiple_tasks" class="col-md-10"><?php echo lang('add_multiple_tasks'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">m</span>
            </div>
        </div> 
    <?php } ?>
    <?php if ($this->login_user->user_type == "staff") { ?>
        <div class="form-group">
            <label for="add_project_time" class="col-md-10"><?php echo lang('add_project_time'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">i</span>
            </div>
        </div>
    <?php } ?>
    <?php if (get_setting("module_event") == "1" && (($this->login_user->user_type == "client" && !in_array("events", $hidden_menu)) || $this->login_user->user_type == "staff")) { ?>
        <div class="form-group">
            <label for="add_event" class="col-md-10"><?php echo lang('add_event'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">e</span>
            </div>
        </div>
    <?php } ?>
    <?php if (get_setting("module_note") == "1" && $this->login_user->user_type == "staff") { ?>
        <div class="form-group">
            <label for="add_note" class="col-md-10"><?php echo lang('add_note'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">n</span>
            </div>
        </div>
    <?php } ?>
    <?php if (get_setting("module_todo") == "1") { ?>
        <div class="form-group">
            <label for="add_to_do" class="col-md-10"><?php echo lang('add_to_do'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">d</span>
            </div>
        </div>
    <?php } ?>
    <?php if (get_setting("module_ticket") == "1" && ($this->login_user->is_admin || get_array_value($permissions, "ticket"))) { ?>
        <div class="form-group">
            <label for="add_ticket" class="col-md-10"><?php echo lang('add_ticket'); ?></label>
            <div class="col-md-2">
                <span class="label label-white">s</span>
            </div>
        </div>
    <?php } ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>