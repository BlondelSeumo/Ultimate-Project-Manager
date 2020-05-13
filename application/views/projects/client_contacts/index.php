<div class="panel no-border">
    <div class="tab-title clearfix">
        <h4><?php echo lang('client_contacts'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($can_add_remove_project_members && $can_access_clients) {
                echo modal_anchor(get_uri("projects/project_member_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_contact'), array("class" => "btn btn-default", "title" => lang('add_contact'), "data-post-project_id" => $project_id, "data-post-add_user_type" => "client_contacts"));
            }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="project-client-contacts-table" class="b-b-only no-thead" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#project-client-contacts-table").appTable({
            source: '<?php echo_uri("projects/project_member_list_data/" . $project_id . "/client_contacts") ?>',
            hideTools: true,
            displayLength: 500,
            columns: [
                {title: ''},
                {title: '', "class": "text-center option w100"}
            ]
        });
    });
</script>