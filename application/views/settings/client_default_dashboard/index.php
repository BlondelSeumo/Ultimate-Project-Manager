<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "dashboard";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">

            <div class="panel panel-default">
                <div class="page-title clearfix">
                    <h4><?php echo lang("dashboard"); ?></h4>
                    <div class="title-button-group">
                        <button id="restore_to_default" data-toggle="popover" data-placement="bottom" type="button" class="btn btn-danger"><span class="fa fa-refresh"></span> <?php echo lang('restore_to_default'); ?></button>
                        <?php echo anchor(get_uri("dashboard/edit_client_default_dashboard"), "<i class='fa fa-columns'></i> " . lang('edit_dashboard'), array("class" => "btn btn-default", "title" => lang('edit_dashboard'))); ?>
                    </div>
                </div>

                <div class="bg-off-white">
                    <div class="client-dashboard-help-message"><?php echo lang("client_dashboard_help_message"); ?></div>

                    <?php echo $dashboard_view; ?>

                </div>

            </div>

        </div>
    </div>
</div>
<?php
load_js(array(
    "assets/js/bootstrap-confirmation/bootstrap-confirmation.js",
));
?>

<script type="text/javascript">
    $(document).ready(function () {



        $('#restore_to_default').confirmation({
            btnOkLabel: "<?php echo lang('yes'); ?>",
            btnCancelLabel: "<?php echo lang('no'); ?>",
            onConfirm: function () {
                $.ajax({
                    url: "<?php echo get_uri('dashboard/restore_to_default_client_dashboard') ?>",
                    type: 'POST',
                    success: function () {
                       location.reload();
                    }
                });

            }
        });
    });
</script>  