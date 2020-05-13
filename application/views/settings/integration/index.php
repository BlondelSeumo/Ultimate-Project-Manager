<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "integration";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">

            <div class="panel panel-default no-border clearfix ">

                <ul id="integration-tab" data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("integration"); ?></h4></li>
                    <li><a role="presentation" class="active" href="<?php echo_uri("settings/re_captcha/"); ?>" data-target="#integration-re-captcha">reCAPTCHA</a></li>
                    <li><a id="google_drive" role="presentation" href="<?php echo_uri("settings/google_drive/"); ?>" data-target="#integration-google-drive">Google Drive</a></li>
                    <li><a role="presentation" class="" href="<?php echo_uri("settings/push_notification/"); ?>" data-target="#integration-push-notification"><?php echo lang("push_notification"); ?></a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="integration-re-captcha"></div>
                    <div role="tabpanel" class="tab-pane fade" id="integration-google-drive"></div>
                    <div role="tabpanel" class="tab-pane fade" id="integration-push-notification"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "google_drive") {
                $("[data-target=#integration-google-drive]").trigger("click");
            } else if (tab === "push_notification") {
                $("[data-target=#integration-push-notification]").trigger("click");
            }
        }, 210);
    });

</script>