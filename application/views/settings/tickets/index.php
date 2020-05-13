<?php echo form_open(get_uri("settings/save_ticket_settings"), array("id" => "ticket-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
<div class="panel">

    <div class="panel-body">
        <div class="form-group">
            <label for="show_recent_ticket_comments_at_the_top" class="col-md-4 col-xs-8 col-sm-4"><?php echo lang('show_most_recent_ticket_comments_at_the_top'); ?></label>
            <div class="col-md-8 col-xs-4 col-sm-8">
                <?php
                echo form_checkbox("show_recent_ticket_comments_at_the_top", "1", get_setting("show_recent_ticket_comments_at_the_top") ? true : false, "id='show_recent_ticket_comments_at_the_top' class='ml15'");
                ?>                       
            </div>
        </div>
        <div class="form-group">
            <label for="project_reference_in_tickets" class="col-md-4 col-xs-8 col-sm-4"><?php echo lang('project_reference_in_tickets'); ?></label>
            <div class="col-md-8 col-xs-4 col-sm-8">
                <?php
                echo form_checkbox("project_reference_in_tickets", "1", get_setting("project_reference_in_tickets") ? true : false, "id='project_reference_in_tickets' class='ml15'");
                ?>                       
            </div>
        </div>
        <div class="form-group">
            <label for="ticket_prefix" class=" col-md-4"><?php echo lang('ticket_prefix'); ?></label>
            <div class=" col-md-8">
                <?php
                echo form_input(array(
                    "id" => "ticket_prefix",
                    "name" => "ticket_prefix",
                    "value" => get_setting("ticket_prefix"),
                    "class" => "form-control",
                    "placeholder" => lang('ticket_prefix')
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="auto_close_ticket_after" class="col-md-4"><?php echo lang('auto_close_ticket_after'); ?>  <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class="col-md-8">
                <?php
                echo form_input(array(
                    "id" => "auto_close_ticket_after",
                    "name" => "auto_close_ticket_after",
                    "type" => "number",
                    "value" => get_setting("auto_close_ticket_after"),
                    "class" => "form-control mini pull-left",
                    "min" => 0
                ));
                ?>
                <label class="mt5 ml10 pull-left"><?php echo lang('days'); ?></label>
            </div>
        </div>
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
    </div>

</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#ticket-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>