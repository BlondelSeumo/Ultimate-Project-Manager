<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "events";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_event_settings"), array("id" => "event-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="panel">
                <div class="panel-default panel-heading">
                    <h4><?php echo lang("event_settings"); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="enable_google_calendar_api" class=" col-md-3"><?php echo lang('enable_google_calendar_api'); ?> <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>

                        <div class="col-md-9">
                            <?php
                            echo form_checkbox("enable_google_calendar_api", "1", get_setting("enable_google_calendar_api") ? true : false, "id='enable_google_calendar_api' class='ml15'");
                            ?> 
                            <span class="google-calendar-show-hide-text ml10 <?php echo get_setting("enable_google_calendar_api") ? "" : "hide" ?>"><i class="fa fa-warning text-warning"></i> <?php echo lang("now_every_user_can_integrate_with_their_google_calendar"); ?></span>
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
        $("#event-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

        //show/hide google calendar help text area
        $("#enable_google_calendar_api").click(function () {
            if ($(this).is(":checked")) {
                $(".google-calendar-show-hide-text").removeClass("hide");
            } else {
                $(".google-calendar-show-hide-text").addClass("hide");
            }
        });
    });
</script>