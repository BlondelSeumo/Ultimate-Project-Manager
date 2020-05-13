<div id="page-content" class="p20 clearfix">
    <div class="row">

        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "general";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_general_settings"), array("id" => "general-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="panel">
                <div class="panel-default panel-heading">
                    <h4><?php echo lang("general_settings"); ?></h4>
                </div>
                <div class="panel-body post-dropzone">
                    <div class="form-group">
                        <label for="logo" class=" col-md-2"><?php echo lang('site_logo'); ?></label>
                        <div class=" col-md-10">
                            <div class="pull-left mr15">
                                <img id="site-logo-preview" src="<?php echo get_logo_url(); ?>" alt="..." />
                            </div>
                            <div class="pull-left file-upload btn btn-default btn-xs">
                                <span>...</span>
                                <input id="site_logo_file" class="cropbox-upload upload" name="site_logo_file" type="file" data-height="40" data-width="175" data-preview-container="#site-logo-preview" data-input-field="#site_logo" />
                            </div>
                            <input type="hidden" id="site_logo" name="site_logo" value=""  />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="favicon" class="col-md-2"><?php echo lang('favicon'); ?></label>
                        <div class="col-lg-10">
                            <div class="pull-left mr15">
                                <img id="favicon-preview" src="<?php echo get_favicon_url(); ?>" alt="..." />
                            </div>
                            <div class="pull-left file-upload btn btn-default btn-xs">
                                <span>...</span>
                                <input id="favicon_file" class="cropbox-upload upload" name="favicon_file" type="file" data-height="32" data-width="32" data-preview-container="#favicon-preview" data-input-field="#favicon" />
                            </div>
                            <input type="hidden" id="favicon" name="favicon" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="show_logo_in_signin_page" class=" col-md-2"><?php echo lang('show_logo_in_signin_page'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "show_logo_in_signin_page", array(
                                "no" => lang("no"),
                                "yes" => lang("yes")
                                    ), get_setting('show_logo_in_signin_page'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="show_background_image_in_signin_page" class=" col-md-2"><?php echo lang('show_background_image_in_signin_page'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "show_background_image_in_signin_page", array(
                                "no" => lang("no"),
                                "yes" => lang("yes")
                                    ), get_setting('show_background_image_in_signin_page'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class=" col-md-2"><?php echo lang('signin_page_background'); ?></label>
                        <div class=" col-md-10">
                            <div class="pull-left mr15">
                                <img id="signin-background-preview" style="max-width: 100px; max-height: 80px;" src="<?php echo get_file_from_setting("signin_page_background"); ?>" alt="..." />
                            </div>
                            <div class="pull-left mr15">
                                <?php $this->load->view("includes/dropzone_preview"); ?>    
                            </div>
                            <div class="pull-left upload-file-button btn btn-default btn-xs">
                                <span>...</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="app_title" class=" col-md-2"><?php echo lang('app_title'); ?></label>
                        <div class=" col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "app_title",
                                "name" => "app_title",
                                "value" => get_setting('app_title'),
                                "class" => "form-control",
                                "placeholder" => lang('app_title'),
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="language" class=" col-md-2"><?php echo lang('language'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "language", $language_dropdown, get_setting('language'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timezone" class=" col-md-2"><?php echo lang('timezone'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "timezone", $timezone_dropdown, get_setting('timezone'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date_format" class=" col-md-2"><?php echo lang('date_format'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "date_format", array(
                                "d-m-Y" => "d-m-Y",
                                "m-d-Y" => "m-d-Y",
                                "Y-m-d" => "Y-m-d",
                                "d/m/Y" => "d/m/Y",
                                "m/d/Y" => "m/d/Y",
                                "Y/m/d" => "Y/m/d",
                                "d.m.Y" => "d.m.Y",
                                "m.d.Y" => "m.d.Y",
                                "Y.m.d" => "Y.m.d",
                                    ), get_setting('date_format'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="time_format" class=" col-md-2"><?php echo lang('time_format'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "time_format", array(
                                "capital" => "12 AM",
                                "small" => "12 am",
                                "24_hours" => "24 hours"
                                    ), get_setting('time_format'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="first_day_of_week" class=" col-md-2"><?php echo lang('first_day_of_week'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "first_day_of_week", array(
                                "0" => "Sunday",
                                "1" => "Monday",
                                "2" => "Tuesday",
                                "3" => "Wednesday",
                                "4" => "Thursday",
                                "5" => "Friday",
                                "6" => "Saturday"
                                    ), get_setting('first_day_of_week'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="default_currency" class=" col-md-2"><?php echo lang('currency'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "default_currency", $currency_dropdown, get_setting('default_currency'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="currency_symbol" class=" col-md-2"><?php echo lang('currency_symbol'); ?></label>
                        <div class=" col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "currency_symbol",
                                "name" => "currency_symbol",
                                "value" => get_setting('currency_symbol'),
                                "class" => "form-control",
                                "placeholder" => lang('currency_symbol'),
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="currency_position" class=" col-md-2"><?php echo lang('currency_position'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "currency_position", array(
                                "left" => lang("left"),
                                "right" => lang("right")
                                    ), get_setting('currency_position'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="decimal_separator" class=" col-md-2"><?php echo lang('decimal_separator'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "decimal_separator", array("." => "Dot (.)", "," => "Comma (,)"), get_setting('decimal_separator'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="no_of_decimals" class=" col-md-2"><?php echo lang('no_of_decimals'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "no_of_decimals", array(
                                "0" => "0",
                                "2" => "2"
                                    ), get_setting('no_of_decimals') == "0" ? "0" : "2", "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="accepted_file_formats" class=" col-md-2"><?php echo lang('accepted_file_format'); ?></label>
                        <div class=" col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "accepted_file_formats",
                                "name" => "accepted_file_formats",
                                "value" => get_setting('accepted_file_formats'),
                                "class" => "form-control",
                                "placeholder" => lang('comma_separated'),
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rows_per_page" class=" col-md-2"><?php echo lang('rows_per_page'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "rows_per_page", array(
                                "10" => "10",
                                "25" => "25",
                                "50" => "50",
                                "100" => "100",
                                    ), get_setting('rows_per_page'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="scrollbar" class=" col-md-2"><?php echo lang('scrollbar'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "scrollbar", array(
                                "jquery" => "jQuery",
                                "native" => "Native"
                                    ), get_setting('scrollbar'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>

                    <?php if (get_setting("disable_html_input")) { ?>
                        <!--flag the enable_rich_text_editor as disabled, when the disable_html_input is enabled-->
                        <input type="hidden" name="enable_rich_text_editor" value="no" />
                    <?php } else { ?>
                        <div class="form-group">
                            <label for="enable_rich_text_editor" class=" col-md-2"><?php echo lang('enable_rich_text_editor'); ?></label>
                            <div class="col-md-10">
                                <?php
                                echo form_dropdown(
                                        "enable_rich_text_editor", array(
                                    "0" => lang("no"),
                                    "1" => lang("yes")
                                        ), get_setting('enable_rich_text_editor'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="rtl" class=" col-md-2"><?php echo lang('rtl'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "rtl", array(
                                "0" => lang("no"),
                                "1" => lang("yes")
                                    ), get_setting('rtl'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="item_purchase_code" class=" col-md-2"><?php echo lang('item_purchase_code'); ?></label>
                        <div class=" col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "item_purchase_code",
                                "name" => "item_purchase_code",
                                "value" => get_setting('item_purchase_code') ? "******" : "",
                                "class" => "form-control",
                                "placeholder" => "Envato Purchase Code",
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="show_theme_color_changer" class=" col-md-2"><?php echo lang('show_theme_color_changer'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "show_theme_color_changer", array(
                                "no" => lang("no"),
                                "yes" => lang("yes")
                                    ), get_setting('show_theme_color_changer'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>

                    <div class="form-group color-plate" id="settings-color-plate">
                        <label for="default_theme_color" class="col-md-2"><?php echo lang('default_theme_color'); ?></label>
                        <div class="col-md-10">
                            <?php echo get_custom_theme_color_list(); ?>
                            <input id="default-theme-color" type="hidden" name="default_theme_color" value="<?php echo get_setting("default_theme_color"); ?>" />
                        </div>
                    </div>

                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php $this->load->view("includes/cropbox"); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#general-settings-form .select2").select2();

        $("#general-settings-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "invoice_logo" || obj.name === "site_logo" || obj.name === "favicon") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
                });
            },
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val() || $("#favicon").val() || result.reload_page) {
                    location.reload();
                }
            }
        });

        var uploadUrl = "<?php echo get_uri("settings/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("settings/validate_file"); ?>";

        var dropzone = attachDropzoneWithForm("#general-settings-form", uploadUrl, validationUrl, {maxFiles: 1});


        $(".cropbox-upload").change(function () {
            showCropBox(this);
        });

        var existingColor = "<?php echo get_setting("default_theme_color"); ?>";
        if (existingColor === "1d2632") {
            $("#settings-color-plate span:first-child").addClass("active");
        } else {
            $("#settings-color-plate").find("[data-color='" + existingColor + "']").addClass("active");
        }

        $("#settings-color-plate span").click(function () {
            $("#settings-color-plate span").removeClass("active");
            $(this).addClass("active");

            var color = $(this).attr("data-color");
            if (color) {
                $("#default-theme-color").val($(this).attr("data-color"));
            } else {
                $("#default-theme-color").val("1d2632");
            }
        });

    });
</script>