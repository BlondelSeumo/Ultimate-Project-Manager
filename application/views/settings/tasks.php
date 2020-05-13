<?php echo form_open(get_uri("settings/save_task_settings"), array("id" => "task-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

<div class="panel">
    <div class="panel-body">

        <div class="form-group">
            <label for="enable_recurring_option_for_tasks" class="col-md-3"><?php echo lang('enable_recurring_option_for_tasks'); ?> <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown(
                        "enable_recurring_option_for_tasks", array("1" => lang("yes"), "0" => lang("no")), get_setting('enable_recurring_option_for_tasks'), "class='select2 mini'"
                );
                ?>                     
            </div>
        </div>

        <div class="form-group">
            <label for="project_task_deadline_pre_reminder" class=" col-md-3"><?php echo lang('send_task_deadline_pre_reminder'); ?> <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown(
                        "project_task_deadline_pre_reminder", array(
                    "" => " - ",
                    "1" => "1 " . lang("day"),
                    "2" => "2 " . lang("days"),
                    "3" => "3 " . lang("days"),
                    "5" => "5 " . lang("days"),
                    "7" => "7 " . lang("days"),
                    "10" => "10 " . lang("days"),
                    "14" => "14 " . lang("days"),
                    "15" => "15 " . lang("days"),
                    "20" => "20 " . lang("days"),
                    "30" => "30 " . lang("days"),
                        ), get_setting('project_task_deadline_pre_reminder'), "class='select2 mini'"
                );
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="project_task_reminder_on_the_day_of_deadline" class="col-md-3"><?php echo lang('send_task_reminder_on_the_day_of_deadline'); ?> <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown(
                        "project_task_reminder_on_the_day_of_deadline", array("1" => lang("yes"), "0" => lang("no")), get_setting('project_task_reminder_on_the_day_of_deadline'), "class='select2 mini'"
                );
                ?>                     
            </div>
        </div>
        <div class="form-group">
            <label for="project_task_deadline_overdue_reminder" class=" col-md-3"><?php echo lang('send_task_deadline_overdue_reminder'); ?> <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown(
                        "project_task_deadline_overdue_reminder", array(
                    "" => " - ",
                    "1" => "1 " . lang("day"),
                    "2" => "2 " . lang("days"),
                    "3" => "3 " . lang("days"),
                    "5" => "5 " . lang("days"),
                    "7" => "7 " . lang("days"),
                    "10" => "10 " . lang("days"),
                    "14" => "14 " . lang("days"),
                    "15" => "15 " . lang("days"),
                    "20" => "20 " . lang("days"),
                    "30" => "30 " . lang("days"),
                        ), get_setting('project_task_deadline_overdue_reminder'), "class='select2 mini'"
                );
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="task_point_range" class="col-md-3"><?php echo lang('task_point_range'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown(
                        "task_point_range", array(
                    "5" => "5",
                    "10" => "10",
                    "15" => "15",
                    "20" => "20",
                        ), get_setting('task_point_range'), "class='select2 mini'"
                );
                ?>
            </div>
        </div>

    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
    </div>

    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#task-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        $("#task-settings-form .select2").select2();
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>