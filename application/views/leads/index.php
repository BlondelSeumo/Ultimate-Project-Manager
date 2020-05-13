<div id="page-content" class="p20 clearfix">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("leads"); ?></h4></li>

        <?php $this->load->view("leads/tabs", array("active_tab" => "leads_list")); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("leads/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_lead'), array("class" => "btn btn-default", "title" => lang('add_lead'))); ?>
            </div>
        </div>
    </ul>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table id="lead-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#lead-table").appTable({
            source: '<?php echo_uri("leads/list_data") ?>',
            columns: [
                {title: "<?php echo lang("company_name") ?>"},
                {title: "<?php echo lang("primary_contact") ?>"},
                {title: "<?php echo lang("owner") ?>"},
                {title: "<?php echo lang("status") ?>"}
<?php echo $custom_field_headers; ?>,
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            filterDropdown: [
                {name: "status", class: "w200", options: <?php $this->load->view("leads/lead_statuses"); ?>},
                {name: "source", class: "w200", options: <?php $this->load->view("leads/lead_sources"); ?>},
                {name: "owner_id", class: "w200", options: <?php echo json_encode($owners_dropdown); ?>}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2], '<?php echo $custom_field_headers; ?>')
        });
    });
</script>

<?php $this->load->view("leads/update_lead_status_script"); ?>