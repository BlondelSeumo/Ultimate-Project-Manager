<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('tasks'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($this->login_user->user_type == "staff" && $can_edit_tasks) {
                echo modal_anchor("", "<i class='fa fa-pencil-square'></i> " . lang('batch_update'), array("class" => "btn btn-info hide batch-update-btn", "title" => lang('batch_update'), "data-post-project_id" => $project_id));
                echo js_anchor("<i class='fa fa-check-square'></i> " . lang("batch_update"), array("class" => "btn btn-default batch-active-btn"));
                echo js_anchor("<i class='fa fa-close'></i> " . lang("cancel_selection"), array("class" => "hide btn btn-default batch-cancel-btn"));
            }
            if ($can_create_tasks) {
                echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_multiple_tasks'), array("class" => "btn btn-default", "title" => lang('add_multiple_tasks'), "data-post-project_id" => $project_id, "data-post-add_type" => "multiple"));
                echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task'), array("class" => "btn btn-default", "title" => lang('add_task'), "data-post-project_id" => $project_id));
            }
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="task-table" class="display" width="100%">            
        </table>
    </div>    
</div>

<?php
//prepare status dropdown list
//select the non completed tasks for team members by default
//show all tasks for client by default.
$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = false;
    if ($this->login_user->user_type == "staff") {
        if ($status->key_name != "done") {
            $is_selected = true;
        }
    }

    $statuses[] = array("text" => ($status->key_name ? lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}
?>

<script type="text/javascript">
    $(document).ready(function () {

    var userType = "<?php echo $this->login_user->user_type; ?>";
    var optionVisibility = false;
    if ("<?php echo ($can_edit_tasks || $can_delete_tasks); ?>") {
    optionVisibility = true;
    }

        var showResponsiveOption = true,
                idColumnClass = "w10p",
                titleColumnClass = "",
                optionColumnClass = "w100";
        
        if (isMobile()) {
            showResponsiveOption = false;
            idColumnClass = "w20p";
            titleColumnClass = "w60p";
            optionColumnClass = "w20p";
        }

    if (userType === "client") {
    //don't show assignee and options to clients
    $("#task-table").appTable({
    source: '<?php echo_uri("projects/tasks_list_data/" . $project_id) ?>',
            order: [[1, "desc"]],
            filterDropdown: [{name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>}],
                responsive: showResponsiveOption, //hide responsive (+) icon for mobile devices
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
                    {title: '<?php echo lang("start_date") ?>', "iDataSort": 3, visible: showResponsiveOption},
            {visible: false, searchable: false},
                    {title: '<?php echo lang("deadline") ?>', "iDataSort": 5, visible: showResponsiveOption},
            {visible: false, searchable: false},
            {visible: false, searchable: false},
            {visible: false, searchable: false},
                    {title: '<?php echo lang("status") ?>', visible: showResponsiveOption}
<?php echo $custom_field_headers; ?>,
                    {title: '<i class="fa fa-bars"></i>', visible: optionVisibility, "class": "text-center option " + optionColumnClass}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 4, 7], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 4, 7], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            }
    });
    } else {
    $("#task-table").appTable({
    source: '<?php echo_uri("projects/tasks_list_data/" . $project_id) ?>',
            order: [[1, "desc"]],
                responsive: showResponsiveOption, //hide responsive (+) icon for mobile devices
            filterDropdown: [
            {name: "milestone_id", class: "w150", options: <?php echo $milestone_dropdown; ?>},
            {name: "assigned_to", class: "w150", options: <?php echo $assigned_to_dropdown; ?>}],
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
                    {title: '<?php echo lang("start_date") ?>', "iDataSort": 3, visible: showResponsiveOption},
            {visible: false, searchable: false},
                    {title: '<?php echo lang("deadline") ?>', "iDataSort": 5, visible: showResponsiveOption},
            {visible: false, searchable: false},
                    {title: '<?php echo lang("assigned_to") ?>', "class": "min-w150", visible: showResponsiveOption},
                    {title: '<?php echo lang("collaborators") ?>', visible: showResponsiveOption},
                    {title: '<?php echo lang("status") ?>', visible: showResponsiveOption}
<?php echo $custom_field_headers; ?>,
                    {title: '<i class="fa fa-bars"></i>', visible: optionVisibility, "class": "text-center option " + optionColumnClass}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 4, 6, 8, 9, 10], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 4, 6, 8, 9, 10], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            },
            onRelaodCallback: function () {
            hideBatchTasksBtn();
            }
    });
    }
    });
</script>

<?php $this->load->view("projects/tasks/update_task_script"); ?>
<?php $this->load->view("projects/tasks/update_task_read_comments_status_script"); ?>
