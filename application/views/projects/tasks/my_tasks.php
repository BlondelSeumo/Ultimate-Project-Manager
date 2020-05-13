<div id="page-content" class="p20 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("tasks"); ?></h4></li>

        <?php $this->load->view("projects/tasks/tabs", array("active_tab" => "tasks_list")); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($this->login_user->user_type == "staff") {
                    echo modal_anchor("", "<i class='fa fa-pencil-square'></i> " . lang('batch_update'), array("class" => "btn btn-info hide batch-update-btn", "title" => lang('batch_update')));
                    echo js_anchor("<i class='fa fa-check-square'></i> " . lang("batch_update"), array("class" => "btn btn-default hide batch-active-btn"));
                    echo js_anchor("<i class='fa fa-close'></i> " . lang("cancel_selection"), array("class" => "hide btn btn-default batch-cancel-btn"));
                }
                if ($can_create_tasks) {
                    echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_multiple_tasks'), array("class" => "btn btn-default", "title" => lang('add_multiple_tasks'), "data-post-add_type" => "multiple"));
                    echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task'), array("class" => "btn btn-default", "title" => lang('add_task')));
                }
                ?>
            </div>
        </div>

    </ul>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table id="task-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<?php
//if we get any task parameter, we'll show the task details modal automatically
$preview_task_id = get_array_value($_GET, 'task');
if ($preview_task_id) {
    echo modal_anchor(get_uri("projects/task_view"), "", array("id" => "preview_task_link", "title" => lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id));
}

$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = false;
    if ($status->key_name != "done") {
        $is_selected = true;
    }

    $statuses[] = array("text" => ($status->key_name ? lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}
?>

<script type="text/javascript">
    $(document).ready(function () {

        var showOption = true,
                idColumnClass = "w10p",
                titleColumnClass = "";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w75p";
        }

        $("#task-table").appTable({
            source: '<?php echo_uri("projects/my_tasks_list_data") ?>',
            order: [[1, "desc"]],
            responsive: showOption, //hide responsive (+) icon for mobile devices
            filterDropdown: [
                {name: "specific_user_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
                {name: "milestone_id", class: "w200", options: [{id: "", text: "- <?php echo lang('milestone'); ?> -"}], dependency: ["project_id"], dataSource: '<?php echo_uri("projects/get_milestones_for_filter") ?>'}, //milestone is dependent on project
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>, dependent: ["milestone_id"]} //reset milestone on changing of project               
            ],
            singleDatepicker: [{name: "deadline", defaultText: "<?php echo lang('deadline') ?>",
                    options: [
                        {value: "expired", text: "<?php echo lang('expired') ?>"},
                        {value: moment().format("YYYY-MM-DD"), text: "<?php echo lang('today') ?>"},
                        {value: moment().add(1, 'days').format("YYYY-MM-DD"), text: "<?php echo lang('tomorrow') ?>"},
                        {value: moment().add(7, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 7); ?>"},
                        {value: moment().add(15, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 15); ?>"}
                    ]}],
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo lang("id") ?>', "class": idColumnClass},
                {title: '<?php echo lang("title") ?>', "class": titleColumnClass},
                {visible: false, searchable: false},
                {title: '<?php echo lang("start_date") ?>', "iDataSort": 3, visible: showOption},
                {visible: false, searchable: false},
                {title: '<?php echo lang("deadline") ?>', "iDataSort": 5, visible: showOption},
                {title: '<?php echo lang("project") ?>', visible: showOption},
                {title: '<?php echo lang("assigned_to") ?>', "class": "min-w150", visible: showOption},
                {title: '<?php echo lang("collaborators") ?>', visible: showOption},
                {title: '<?php echo lang("status") ?>', visible: showOption}
<?php echo $custom_field_headers; ?>,
                {visible: false, searchable: false}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 4, 6, 7, 8, 9, 10], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 4, 6, 7, 8, 9, 10], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            },
            onRelaodCallback: function () {
                hideBatchTasksBtn(true);
            },
            onInitComplete: function () {
                if (!showOption) {
                    window.scrollTo(0, 210); //scroll to the content for mobile devices
                }
            }
        });


        //open task details modal automatically 

        if ($("#preview_task_link").length) {
            $("#preview_task_link").trigger("click");
        }


    });
</script>

<?php $this->load->view("projects/tasks/batch_update/batch_update_script"); ?>
<?php $this->load->view("projects/tasks/update_task_script"); ?>
<?php $this->load->view("projects/tasks/update_task_read_comments_status_script"); ?>