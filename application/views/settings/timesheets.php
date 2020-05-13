<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "timesheets";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_timesheet_settings"), array("id" => "timesheet-settings-form", "class" => "general-form", "role" => "form")); ?>
            <div class="panel">
                <div class="panel-default panel-heading">
                    <h4><?php echo lang("timesheet_settings"); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="users_can_start_multiple_timers_at_a_time" class=" col-md-3"><?php echo lang('users_can_start_multiple_timers_at_a_time'); ?></label>

                        <div class="col-md-9">
                            <?php
                            echo form_checkbox("users_can_start_multiple_timers_at_a_time", "1", get_setting("users_can_start_multiple_timers_at_a_time") ? true : false, "id='users_can_start_multiple_timers_at_a_time' class='ml15'");
                            ?> 
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#timesheet-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });
    });
</script>