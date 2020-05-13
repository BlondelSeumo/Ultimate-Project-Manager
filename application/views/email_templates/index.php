<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "email_templates";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">
            <div class="row">
                <div class="col-md-3">
                    <div id="template-list-box" class="panel panel-default">
                        <div class="page-title clearfix">
                            <h4> <?php echo lang('email_templates'); ?></h4>
                        </div>

                        <ul class="nav nav-tabs vertical settings p15" role="tablist">
                            <?php
                            foreach ($templates as $template => $value) {

                                //collapse the selected template tab panel
                                $collapse_in = "";
                                $collapsed_class = "collapsed";
                                ?>
                                <div class="clearfix settings-anchor <?php echo $collapsed_class; ?>" data-toggle="collapse" data-target="#settings-tab-<?php echo $template; ?>">
                                    <?php echo lang($template); ?> 
                                    <span class="pull-right template"><i class="fa fa-angle-right"></i></span>
                                </div>
                                <?php
                                echo "<div id='settings-tab-$template' class='collapse $collapse_in'>";
                                echo "<ul class='list-group help-catagory'>";

                                foreach ($value as $sub_template_name => $sub_template) {
                                    echo "<span class='template-row list-group-item clickable' data-name='$sub_template_name'>" . lang($sub_template_name) . "</span>";
                                }

                                echo "</ul>";
                                echo "</div>";
                            }
                            ?>
                        </ul>

                    </div>
                </div>
                <div class="col-md-9">
                    <div id="template-details-section"> 
                        <div id="empty-template" class="text-center p15 box panel panel-default ">
                            <div class="box-content" style="vertical-align: middle; height: 100%"> 
                                <div><?php echo lang("select_a_template"); ?></div>
                                <span class="fa fa-code" style="font-size: 1450%; color:#f6f8f8"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
load_css(array(
    "assets/js/summernote/summernote.css",
    "assets/js/summernote/summernote-bs3.css"
));
load_js(array(
    "assets/js/summernote/summernote.min.js",
    "assets/js/bootstrap-confirmation/bootstrap-confirmation.js",
));
?>


<script type="text/javascript">
    $(document).ready(function () {

        /*load a template details*/
        $(".template-row").click(function () {
            //don't load this message if already has selected.
            if (!$(this).hasClass("active")) {
                var template_name = $(this).attr("data-name");
                if (template_name) {
                    $(".template-row").removeClass("active")
                    $(this).addClass("active");
                    $.ajax({
                        url: "<?php echo get_uri("email_templates/form"); ?>/" + template_name,
                        success: function (result) {
                            $("#template-details-section").html(result);
                        }
                    });
                }
            }
        });
    });
</script>