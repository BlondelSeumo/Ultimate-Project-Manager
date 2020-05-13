
<div id="page-content" class="clearfix">
    <div style="max-width: 1000px; margin: auto;">
        <div class="page-title clearfix mt15">
            <h1><?php echo get_estimate_id($estimate_info->id); ?></h1>
            <div class="title-button-group">
                <span class="dropdown inline-block">
                    <button class="btn btn-info dropdown-toggle  mt0 mb0" type="button" data-toggle="dropdown" aria-expanded="true">
                        <i class='fa fa-cogs'></i> <?php echo lang('actions'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li role="presentation"><?php echo anchor(get_uri("estimates/download_pdf/" . $estimate_info->id), "<i class='fa fa-download'></i> " . lang('download_pdf'), array("title" => lang('download_pdf'),)); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("estimates/download_pdf/" . $estimate_info->id . "/view"), "<i class='fa fa-file-pdf-o'></i> " . lang('view_pdf'), array("title" => lang('view_pdf'), "target" => "_blank")); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("estimates/preview/" . $estimate_info->id . "/1"), "<i class='fa fa-search'></i> " . lang('estimate_preview'), array("title" => lang('estimate_preview')), array("target" => "_blank")); ?> </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><?php echo modal_anchor(get_uri("estimates/modal_form"), "<i class='fa fa-edit'></i> " . lang('edit_estimate'), array("title" => lang('edit_estimate'), "data-post-id" => $estimate_info->id, "role" => "menuitem", "tabindex" => "-1")); ?> </li>
                        <li role="presentation"><?php echo modal_anchor(get_uri("estimates/modal_form"), "<i class='fa fa-copy'></i> " . lang('clone_estimate'), array("data-post-is_clone" => true, "data-post-id" => $estimate_info->id, "title" => lang('clone_estimate'))); ?></li>

                        <!--don't show status changing option for leads-->
                        <?php
                        if (!$client_info->is_lead) {
                            if ($estimate_status == "draft") {
                                ?>
                                <li role="presentation"><?php echo modal_anchor(get_uri("estimates/send_estimate_modal_form/" . $estimate_info->id), "<i class='fa fa-send'></i> " . lang('send_to_client'), array("title" => lang('send_to_client'), "data-post-id" => $estimate_info->id, "role" => "menuitem", "tabindex" => "-1")); ?> </li>
                            <?php } else if ($estimate_status == "sent") { ?>
                                <li role="presentation"><?php echo modal_anchor(get_uri("estimates/send_estimate_modal_form/" . $estimate_info->id), "<i class='fa fa-send'></i> " . lang('send_to_client'), array("title" => lang('send_to_client'), "data-post-id" => $estimate_info->id, "role" => "menuitem", "tabindex" => "-1")); ?> </li>
                                <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/accepted"), "<i class='fa fa-check-circle'></i> " . lang('mark_as_accepted'), array("data-reload-on-success" => "1")); ?> </li>
                                <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/declined"), "<i class='fa fa-times-circle-o'></i> " . lang('mark_as_declined'), array("data-reload-on-success" => "1")); ?> </li>
                            <?php } else if ($estimate_status == "accepted") { ?>
                                <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/declined"), "<i class='fa fa-times-circle-o'></i> " . lang('mark_as_declined'), array("data-reload-on-success" => "1")); ?> </li>
                            <?php } else if ($estimate_status == "declined") { ?>
                                <li role="presentation"><?php echo ajax_anchor(get_uri("estimates/update_estimate_status/" . $estimate_info->id . "/accepted"), "<i class='fa fa-check-circle'></i> " . lang('mark_as_accepted'), array("data-reload-on-success" => "1")); ?> </li>
                                <?php
                            }
                        }
                        ?>

                        <?php if ($client_info->is_lead) { ?>
                            <li role="presentation"><?php echo modal_anchor(get_uri("estimates/send_estimate_modal_form/" . $estimate_info->id), "<i class='fa fa-send'></i> " . lang('send_to_lead'), array("title" => lang('send_to_lead'), "data-post-id" => $estimate_info->id, "data-post-is_lead" => true, "role" => "menuitem", "tabindex" => "-1")); ?> </li>
                        <?php } ?>

                        <?php if ($estimate_status == "accepted") { ?>
                            <li role="presentation" class="divider"></li>
                            <?php if ($can_create_projects && !$estimate_info->project_id) { ?>
                                <li role="presentation"><?php echo modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-plus'></i> " . lang('create_project'), array("data-post-estimate_id" => $estimate_info->id, "title" => lang('create_project'), "data-post-client_id" => $estimate_info->client_id)); ?> </li>
                            <?php } ?>
                            <?php if ($show_invoice_option) { ?>
                                <li role="presentation"><?php echo modal_anchor(get_uri("invoices/modal_form/"), "<i class='fa fa-refresh'></i> " . lang('create_invoice'), array("title" => lang("create_invoice"), "data-post-estimate_id" => $estimate_info->id)); ?> </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </span>
                <?php echo modal_anchor(get_uri("estimates/item_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_item'), array("class" => "btn btn-default", "title" => lang('add_item'), "data-post-estimate_id" => $estimate_info->id)); ?>
            </div>
        </div>
        <div id="estimate-status-bar">
            <?php $this->load->view("estimates/estimate_status_bar"); ?>
        </div>
        <div class="mt15">
            <div class="panel panel-default p15 b-t">
                <div class="clearfix p20">
                    <!-- small font size is required to generate the pdf, overwrite that for screen -->
                    <style type="text/css"> .invoice-meta {font-size: 100% !important;}</style>

                    <?php
                    $color = get_setting("estimate_color");
                    if (!$color) {
                        $color = get_setting("invoice_color");
                    }
                    $style = get_setting("invoice_style");
                    ?>
                    <?php
                    $data = array(
                        "client_info" => $client_info,
                        "color" => $color ? $color : "#2AA384",
                        "estimate_info" => $estimate_info
                    );
                    if ($style === "style_2") {
                        $this->load->view('estimates/estimate_parts/header_style_2.php', $data);
                    } else {
                        $this->load->view('estimates/estimate_parts/header_style_1.php', $data);
                    }
                    ?>

                </div>

                <div class="table-responsive mt15 pl15 pr15">
                    <table id="estimate-item-table" class="display" width="100%">            
                    </table>
                </div>

                <div class="clearfix">
                    <div class="col-sm-8">

                    </div>
                    <div class="pull-right pr15" id="estimate-total-section">
                        <?php $this->load->view("estimates/estimate_total_section"); ?>
                    </div>
                </div>

                <p class="b-t b-info pt10 m15"><?php echo nl2br($estimate_info->note); ?></p>

            </div>
        </div>

    </div>
</div>



<script type="text/javascript">
    //RELOAD_VIEW_AFTER_UPDATE = true;
    $(document).ready(function () {
        $("#estimate-item-table").appTable({
            source: '<?php echo_uri("estimates/item_list_data/" . $estimate_info->id . "/") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo lang("item") ?> ", "bSortable": false},
                {title: "<?php echo lang("quantity") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<?php echo lang("rate") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<?php echo lang("total") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<i class='fa fa-bars'></i>", "class": "text-center option w100", "bSortable": false}
            ],

            onInitComplete: function () {
                //apply sortable
                $("#estimate-item-table").find("tbody").attr("id", "estimate-item-table-sortable");
                var $selector = $("#estimate-item-table-sortable");

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes 
                        var data = "";
                        $.each($selector.find(".item-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }

                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("estimates/update_item_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });

            },

            onDeleteSuccess: function (result) {
                $("#estimate-total-section").html(result.estimate_total_view);
                if (typeof updateInvoiceStatusBar == 'function') {
                    updateInvoiceStatusBar(result.estimate_id);
                }
            },
            onUndoSuccess: function (result) {
                $("#estimate-total-section").html(result.estimate_total_view);
                if (typeof updateInvoiceStatusBar == 'function') {
                    updateInvoiceStatusBar(result.estimate_id);
                }
            }
        });
    });

    updateInvoiceStatusBar = function (estimateId) {
        $.ajax({
            url: "<?php echo get_uri("estimates/get_estimate_status_bar"); ?>/" + estimateId,
            success: function (result) {
                if (result) {
                    $("#estimate-status-bar").html(result);
                }
            }
        });
    };

</script>

<?php
//required to send email 

load_css(array(
    "assets/js/summernote/summernote.css",
));
load_js(array(
    "assets/js/summernote/summernote.min.js",
));
?>
