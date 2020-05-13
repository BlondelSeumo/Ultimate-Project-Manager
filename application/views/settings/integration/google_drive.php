<div class="panel panel-default no-border clearfix mb0">
    <?php echo form_open(get_uri("settings/save_google_drive_settings"), array("id" => "google-drive-form", "class" => "general-form dashed-row", "role" => "form")); ?>

    <div class="panel-body">

        <div class="form-group">
            <label for="enable_google_drive_api_to_upload_file" class="col-md-2 col-xs-8 col-sm-4"><?php echo lang('enable_google_drive_api_to_upload_file'); ?></label>
            <div class="col-md-10 col-xs-4 col-sm-8">
                <?php
                echo form_checkbox("enable_google_drive_api_to_upload_file", "1", get_setting("enable_google_drive_api_to_upload_file") ? true : false, "id='enable_google_drive_api_to_upload_file' class='ml15'");
                ?> 
                <span class="google-drive-show-hide-area ml10 <?php echo get_setting("enable_google_drive_api_to_upload_file") ? "" : "hide" ?>"><i class="fa fa-warning text-warning"></i> <?php echo lang("drive_activation_help_message"); ?></span>
            </div>
        </div>

        <div class="google-drive-show-hide-area <?php echo get_setting("enable_google_drive_api_to_upload_file") ? "" : "hide" ?>">


            <div class="form-group">
                <label class=" col-md-12">
                    <?php echo lang("get_your_app_credentials_from_here") . " " . anchor("https://console.developers.google.com", "Google API Console", array("target" => "_blank")); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="google_drive_client_id" class=" col-md-2"><?php echo lang('google_drive_client_id'); ?></label>
                <div class=" col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "google_drive_client_id",
                        "name" => "google_drive_client_id",
                        "value" => get_setting('google_drive_client_id'),
                        "class" => "form-control",
                        "placeholder" => lang('google_drive_client_id'),
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="google_drive_client_secret" class=" col-md-2"><?php echo lang('google_drive_client_secret'); ?></label>
                <div class=" col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "google_drive_client_secret",
                        "name" => "google_drive_client_secret",
                        "value" => get_setting('google_drive_client_secret'),
                        "class" => "form-control",
                        "placeholder" => lang('google_drive_client_secret'),
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="redirect_uri" class=" col-md-2"><i class="fa fa-warning text-warning"></i> <?php echo lang('remember_to_add_this_url_in_authorized_redirect_uri'); ?></label>
                <div class=" col-md-10">
                    <?php
                    echo "<pre class='mt5'>" . get_uri("google_api/save_access_token") . "</pre>"
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class=" col-md-2"><?php echo lang('status'); ?></label>
                <div class=" col-md-10">
                    <?php if (get_setting("google_drive_authorized")) { ?>
                        <span class="ml5 label label-success"><?php echo lang("authorized"); ?></span>
                    <?php } else { ?>
                        <span class="ml5 label" style="background:#F9A52D;"><?php echo lang("unauthorized"); ?></span>
                    <?php } ?>
                </div>
            </div>

        </div>

    </div>

    <div class="panel-footer">
        <button id="save-button" type="submit" class="btn btn-primary <?php echo get_setting("enable_google_drive_api_to_upload_file") ? "hide" : "" ?>"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        <button id="save-and-authorize-button" type="submit" class="btn btn-primary ml5 <?php echo get_setting("enable_google_drive_api_to_upload_file") ? "" : "hide" ?>"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_authorize'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var $saveAndAuthorizeBtn = $("#save-and-authorize-button"),
                $saveBtn = $("#save-button"),
                $driveDetailsArea = $(".google-drive-show-hide-area");

        $("#google-drive-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});

                //if google drive is enabled, redirect to authorization system
                if ($saveBtn.hasClass("hide")) {
                    window.location.href = "<?php echo_uri('google_api'); ?>";
                }
            }
        });

        //show/hide google drive details area
        $("#enable_google_drive_api_to_upload_file").click(function () {
            if ($(this).is(":checked")) {
                $saveAndAuthorizeBtn.removeClass("hide");
                $driveDetailsArea.removeClass("hide");
                $saveBtn.addClass("hide");
            } else {
                $saveAndAuthorizeBtn.addClass("hide");
                $driveDetailsArea.addClass("hide");
                $saveBtn.removeClass("hide");
            }
        });

    });
</script>