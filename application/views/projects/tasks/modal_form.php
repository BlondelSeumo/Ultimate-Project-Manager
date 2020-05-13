<?php echo form_open(get_uri("projects/save_task"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>
<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
        <input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />
        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />

        <?php if ($is_clone) { ?>
            <input type="hidden" name="is_clone" value="1" />
        <?php } ?>

        <div class="form-group">
            <label for="title" class=" col-md-3"><?php echo lang('title'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_input(array(
                    "id" => "title",
                    "name" => "title",
                    "value" => $add_type == "multiple" ? "" : $model_info->title,
                    "class" => "form-control",
                    "placeholder" => lang('title'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_textarea(array(
                    "id" => "description",
                    "name" => "description",
                    "value" => $add_type == "multiple" ? "" : $model_info->description,
                    "class" => "form-control",
                    "placeholder" => lang('description'),
                    "data-rich-text-editor" => true
                ));
                ?>
            </div>
        </div>
        <?php if (!$project_id) { ?>
            <div class="form-group">
                <label for="project_id" class=" col-md-3"><?php echo lang('project'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("project_id", $projects_dropdown, array($model_info->project_id), "class='select2 validate-hidden' id='project_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                    ?>
                </div>
            </div>
        <?php } ?>
        <div class="form-group">
            <label for="points" class="col-md-3"><?php echo lang('points'); ?>
                <span class="help" data-toggle="tooltip" title="<?php echo lang('task_point_help_text'); ?>"><i class="fa fa-question-circle"></i></span>
            </label>

            <div class="col-md-9">
                <?php
                echo form_dropdown("points", $points_dropdown, array($model_info->points), "class='select2'");
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="milestone_id" class=" col-md-3"><?php echo lang('milestone'); ?></label>
            <div class="col-md-9" id="dropdown-apploader-section">
                <?php
                echo form_input(array(
                    "id" => "milestone_id",
                    "name" => "milestone_id",
                    "value" => $model_info->milestone_id,
                    "class" => "form-control",
                    "placeholder" => lang('milestone')
                ));
                ?>
            </div>
        </div>

        <?php if ($show_assign_to_dropdown) { ?>
            <div class="form-group">
                <label for="assigned_to" class=" col-md-3"><?php echo lang('assign_to'); ?></label>
                <div class="col-md-9" id="dropdown-apploader-section">
                    <?php
                    echo form_input(array(
                        "id" => "assigned_to",
                        "name" => "assigned_to",
                        "value" => $model_info->assigned_to,
                        "class" => "form-control",
                        "placeholder" => lang('assign_to')
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="collaborators" class=" col-md-3"><?php echo lang('collaborators'); ?></label>
                <div class="col-md-9" id="dropdown-apploader-section">
                    <?php
                    echo form_input(array(
                        "id" => "collaborators",
                        "name" => "collaborators",
                        "value" => $model_info->collaborators,
                        "class" => "form-control",
                        "placeholder" => lang('collaborators')
                    ));
                    ?>
                </div>
            </div>

        <?php } ?>

        <div class="form-group">
            <label for="status_id" class=" col-md-3"><?php echo lang('status'); ?></label>
            <div class="col-md-9">
                <?php
                foreach ($statuses as $status) {
                    $task_status[$status->id] = $status->key_name ? lang($status->key_name) : $status->title;
                }

                if ($is_clone) {
                    echo form_dropdown("status_id", $task_status, 1, "class='select2'");
                } else {
                    echo form_dropdown("status_id", $task_status, array($model_info->status_id), "class='select2'");
                }
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="project_labels" class=" col-md-3"><?php echo lang('labels'); ?></label>
            <div class=" col-md-9" id="dropdown-apploader-section">
                <?php
                echo form_input(array(
                    "id" => "project_labels",
                    "name" => "labels",
                    "value" => $model_info->labels,
                    "class" => "form-control",
                    "placeholder" => lang('labels')
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="start_date" class=" col-md-3"><?php echo lang('start_date'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_input(array(
                    "id" => "start_date",
                    "name" => "start_date",
                    "autocomplete" => "off",
                    "value" => is_date_exists($model_info->start_date) ? $model_info->start_date : "",
                    "class" => "form-control",
                    "placeholder" => "YYYY-MM-DD"
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="deadline" class=" col-md-3"><?php echo lang('deadline'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_input(array(
                    "id" => "deadline",
                    "name" => "deadline",
                    "autocomplete" => "off",
                    "value" => is_date_exists($model_info->deadline) ? $model_info->deadline : "",
                    "class" => "form-control",
                    "placeholder" => "YYYY-MM-DD"
                ));
                ?>
            </div>
        </div>

        <?php if (get_setting("enable_recurring_option_for_tasks")) { ?>

            <div class="form-group">
                <label for="recurring" class=" col-md-3"><?php echo lang('recurring'); ?>  <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
                <div class=" col-md-9">
                    <?php
                    echo form_checkbox("recurring", "1", $model_info->recurring ? true : false, "id='recurring'");
                    ?>
                </div>
            </div>   

            <div id="recurring_fields" class="<?php if (!$model_info->recurring) echo "hide"; ?>"> 
                <div class="form-group">
                    <label for="repeat_every" class=" col-md-3"><?php echo lang('repeat_every'); ?></label>
                    <div class="col-md-4">
                        <?php
                        echo form_input(array(
                            "id" => "repeat_every",
                            "name" => "repeat_every",
                            "type" => "number",
                            "value" => $model_info->repeat_every ? $model_info->repeat_every : 1,
                            "min" => 1,
                            "class" => "form-control recurring_element",
                            "placeholder" => lang('repeat_every'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required")
                        ));
                        ?>
                    </div>
                    <div class="col-md-5">
                        <?php
                        echo form_dropdown(
                                "repeat_type", array(
                            "days" => lang("interval_days"),
                            "weeks" => lang("interval_weeks"),
                            "months" => lang("interval_months"),
                            "years" => lang("interval_years"),
                                ), $model_info->repeat_type ? $model_info->repeat_type : "months", "class='select2 recurring_element' id='repeat_type'"
                        );
                        ?>
                    </div>
                </div>    

                <div class="form-group">
                    <label for="no_of_cycles" class=" col-md-3"><?php echo lang('cycles'); ?></label>
                    <div class="col-md-4">
                        <?php
                        echo form_input(array(
                            "id" => "no_of_cycles",
                            "name" => "no_of_cycles",
                            "type" => "number",
                            "min" => 1,
                            "value" => $model_info->no_of_cycles ? $model_info->no_of_cycles : "",
                            "class" => "form-control",
                            "placeholder" => lang('cycles')
                        ));
                        ?>
                    </div>
                    <div class="col-md-5 mt5">
                        <span class="help" data-toggle="tooltip" title="<?php echo lang('recurring_cycle_instructions'); ?>"><i class="fa fa-question-circle"></i></span>
                    </div>
                </div>  

                <div class = "form-group hide" id = "next_recurring_date_container" >
                    <label for = "next_recurring_date" class = " col-md-3"><?php echo lang('next_recurring_date'); ?>  </label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "next_recurring_date",
                            "name" => "next_recurring_date",
                            "class" => "form-control",
                            "placeholder" => lang('next_recurring_date'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>  

        <?php } ?>

        <?php $this->load->view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

        <?php $this->load->view("includes/dropzone_preview"); ?>

        <?php if ($is_clone && $has_checklist) { ?>
            <div class="form-group">
                <label for="copy_checklist" class=" col-md-12">
                    <?php
                    echo form_checkbox("copy_checklist", "1", true, "id='copy_checklist' class='pull-left mr15'");
                    ?>    
                    <?php echo lang('copy_checklist'); ?>
                </label>
            </div>
        <?php } ?>
    </div>

    <div class="modal-footer">
        <div id="link-of-new-view" class="hide">
            <?php
            echo modal_anchor(get_uri("projects/task_view"), "", array());
            ?>
        </div>

        <?php if (!$model_info->id || $add_type == "multiple") { ?>
            <button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class="fa fa-camera"></i> <?php echo lang("upload_file"); ?></button>
        <?php } ?>

        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>

        <?php if ($add_type == "multiple") { ?>
            <button id="save-and-add-button" type="button" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_add_more'); ?></button>
        <?php } else { ?>
            <?php if ($view_type !== "details") { ?>
                <button id="save-and-show-button" type="button" class="btn btn-info"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_show'); ?></button>
            <?php } ?>
            <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

<?php if (!$model_info->id || $add_type == "multiple") { ?>
            var uploadUrl = "<?php echo get_uri('projects/upload_file'); ?>";
            var validationUri = "<?php echo get_uri('projects/validate_project_file'); ?>";

            var dropzone = attachDropzoneWithForm("#tasks-dropzone", uploadUrl, validationUri);
<?php } ?>
        //send data to show the task after save
        window.showAddNewModal = false;

        $("#save-and-show-button, #save-and-add-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        var taskShowText = "<?php echo lang('task_info') ?>",
                multipleTaskAddText = "<?php echo lang('add_multiple_tasks') ?>",
                addType = "<?php echo $add_type; ?>";

        window.taskForm = $("#task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                $("#task-table").appTable({newData: result.data, dataId: result.id});
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-new-view").find("a");

                    if (addType === "multiple") {
                        //add multiple tasks
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("projects/task_modal_form"); ?>");
                        $taskViewLink.attr("data-title", multipleTaskAddText);
                        $taskViewLink.attr("data-post-last_id", result.id);
                        $taskViewLink.attr("data-post-project_id", "<?php echo $project_id; ?>");
                        $taskViewLink.attr("data-post-add_type", "multiple");
                    } else {
                        //save and show
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("projects/task_view"); ?>");
                        $taskViewLink.attr("data-title", taskShowText + "#" + result.id);
                        $taskViewLink.attr("data-post-id", result.id);
                    }

                    $taskViewLink.trigger("click");
                } else {
                    window.taskForm.closeModal();

                    if (window.refreshAfterAddTask) {
                        window.refreshAfterAddTask = false;
                        location.reload();
                    }
                }
            },
            onAjaxSuccess: function (result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#task-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });
        $("#task-form .select2").select2();
        $("#title").focus();

        setDatePicker("#start_date");

        setDatePicker("#deadline", {
            endDate: "<?php echo $project_deadline; ?>"
        });

        $('[data-toggle="tooltip"]').tooltip();

        //show/hide recurring fields
        $("#recurring").click(function () {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });

        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });
    });
</script>    

<?php $this->load->view("projects/tasks/get_related_data_of_project_script"); ?>