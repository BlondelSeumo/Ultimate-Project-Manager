<div id="page-content" class="p20 pb0 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("tasks"); ?></h4></li>

        <?php $this->load->view("projects/tasks/tabs", array("active_tab" => "tasks_kanban")); ?>       

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($this->login_user->user_type == "staff") {
                    echo modal_anchor("", "<i class='fa fa-pencil-square'></i> " . lang('batch_update'), array("class" => "btn btn-info hide batch-update-btn", "title" => lang('batch_update')));
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
    <div class="bg-white kanban-filters-container">
        <div class="row">
            <div class="col-md-1 col-xs-2">
                <button class="btn btn-default" id="reload-kanban-button"><i class="fa fa-refresh"></i></button>
            </div>
            <div id="kanban-filters" class="col-md-11 col-xs-10"></div>
        </div>
    </div>

    <div id="load-kanban"></div>
</div>

<script>
    $(document).ready(function () {
        window.scrollToKanbanContent = true;
    });
</script>

<?php $this->load->view("projects/tasks/batch_update/batch_update_script"); ?>
<?php $this->load->view("projects/tasks/kanban/all_tasks_kanban_helper_js"); ?>