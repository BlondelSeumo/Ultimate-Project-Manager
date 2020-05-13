<div id="page-content" class="p20 pb0 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("leads"); ?></h4></li>

        <?php $this->load->view("leads/tabs", array("active_tab" => "leads_kanban")); ?>      

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("leads/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_lead'), array("class" => "btn btn-default", "title" => lang('add_lead'))); ?>
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

<?php $this->load->view("leads/kanban/all_leads_kanban_helper_js"); ?>