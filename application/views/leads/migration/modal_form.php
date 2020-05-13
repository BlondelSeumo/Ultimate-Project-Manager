<?php echo form_open(get_uri("leads/save_as_client"), array("id" => "make-client-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $model_info = get_array_value($lead_info, "model_info"); ?>
    <input type="hidden" name="main_client_id" value="<?php echo $model_info->id; ?>" />

    <?php if ($contacts) { ?>
        <div class="form-widget">
            <div class="widget-title clearfix">
                <div id="lead-info-label" class="col-sm-6"><i class="fa fa-circle-o"></i><strong> <?php echo lang('client_details'); ?></strong></div>
                <div id="lead-contacts-label" class="col-sm-6"><i class="fa fa-circle-o"></i><strong>  <?php echo lang('client_contacts'); ?></strong></div>
            </div>

            <div class="progress ml15 mr15">
                <div id="form-progress-bar" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="tab-content <?php echo $contacts ? "mt15" : ""; ?>">
        <div role="tabpanel" class="tab-pane active" id="lead-info-tab">
            <?php $this->load->view("clients/client_form_fields", $lead_info); ?>
            <?php $this->load->view("leads/custom_field_migration", $lead_info); ?>
        </div>

        <div role="tabpanel" class="tab-pane settings" id="lead-contacts-tab">            
            <?php foreach ($contacts as $key => $value) { ?>
                <?php
                //collapse the first contact
                $collapse_in = "";
                $collapsed_class = "collapsed";
                if ($key == 0) {
                    $collapse_in = "in";
                    $collapsed_class = "";
                }

                //add +1 to get the exact contact value
                $key = $key + 1;
                ?>
                <div class="lead-migration-contacts">
                    <div class="clearfix settings-anchor m15 font-14 strong <?php echo $collapsed_class; ?>" data-toggle="collapse" data-target="#contact-<?php echo $key; ?>">
                        <?php echo lang("contact") . " #$key"; ?>
                        <span class="pull-right"><i class="fa fa-plus-square-o"></i></span>
                    </div>

                    <div id='contact-<?php echo $key; ?>' class='pt15 b-t collapse <?php echo $collapse_in; ?>'>
                        <?php $this->load->view("leads/contacts/contact_general_info_fields_for_migration", array("model_info" => $value, "custom_fields" => $value->custom_fields)); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

</div>

<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>

    <?php if ($contacts) { ?>
        <button id="form-previous" type="button" class="btn btn-default hide"><span class="fa fa-arrow-circle-left"></span> <?php echo lang('previous'); ?></button>
        <button id="form-next" type="button" class="btn btn-info"><span class="fa  fa-arrow-circle-right"></span> <?php echo lang('next'); ?></button>
    <?php } ?>

    <button id="form-submit" type="button" class="btn btn-primary <?php echo $contacts ? "hide" : ""; ?>"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#make-client-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    window.location.href = result.redirect_to;
                }
            }
        });

        $("#make-client-form input").keydown(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                if ($('#form-submit').hasClass('hide')) {
                    $("#form-next").trigger('click');
                } else {
                    triggerSubmit();
                }
            }
        });

        function triggerSubmit() {
            //to check required fields under the collapse panel, we've to open those first
            $(".lead-migration-contacts div.collapse").collapse("show");

            $("#make-client-form").trigger('submit');
        }

        $("#form-previous").click(function () {
            var $infoTab = $("#lead-info-tab"),
                    $contactsTab = $("#lead-contacts-tab"),
                    $previousButton = $("#form-previous"),
                    $nextButton = $("#form-next"),
                    $submitButton = $("#form-submit");

            if ($contactsTab.hasClass("active")) {
                $contactsTab.removeClass("active");
                $infoTab.addClass("active");
                $previousButton.addClass("hide");
                $nextButton.removeClass("hide");
                $submitButton.addClass("hide");
            }
        });

        $("#form-next").click(function () {
            var $infoTab = $("#lead-info-tab"),
                    $contactsTab = $("#lead-contacts-tab"),
                    $previousButton = $("#form-previous"),
                    $nextButton = $("#form-next"),
                    $submitButton = $("#form-submit");
            if (!$("#make-client-form").valid()) {
                return false;
            }
            if ($infoTab.hasClass("active")) {
                $infoTab.removeClass("active");
                $contactsTab.addClass("active");
                $previousButton.removeClass("hide");
                $nextButton.addClass("hide");
                $submitButton.removeClass("hide");
                $("#form-progress-bar").width("50%");
                $("#lead-info-label").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
            }
        });

        $("#form-submit").click(function () {
            triggerSubmit();
        });

        //there should be only one primary contact
        var $isPrimaryContactCheckbox = $(".is_primary_contact_lead");
        $isPrimaryContactCheckbox.click(function () {
            $isPrimaryContactCheckbox.removeAttr("disabled");
            $isPrimaryContactCheckbox.prop("checked", false);
            $(this).prop("checked", true);
            $(this).prop("disabled", true);

            //store value
            $(".is_primary_contact_value").val("0");
            $(this).closest(".form-group").find("input.is_primary_contact_value").val("1");
        });

        //skip multi-selection for custom field migration
        $(".custom-field-migration-fields input").click(function () {
            $(this).closest(".custom-field-migration-fields").find("input").prop("checked", false);
            $(this).prop("checked", true);
        });

        //add user id with the custom fields to get the exact value
        $(".custom-fields-on-migration").each(function () {
            var userId = $(this).attr("data-user-id");

            $(this).find(".form-group").each(function () {
                var finalId = $(this).find("input").attr("name") + "_" + userId;

                if ($(this).attr("data-field-type") === "select") {
                    finalId = $(this).find("select").attr("name") + "_" + userId;
                    $(this).find("select").attr("name", finalId);

                } else if ($(this).attr("data-field-type") === "date") {
                    $(this).find("input").attr("name", finalId);

                } else if ($(this).attr("data-field-type") === "multi_select") {
                    finalId = $(this).find("label").attr("for") + "_" + userId;
                    $(this).find("input[data-custom-multi-select-input='1']").attr("name", finalId);

                } else {
                    $(this).find("label").attr("for", finalId);
                    $(this).find("input").attr("name", finalId);
                    $(this).find("input").attr("id", finalId);
                }
            });
        });
    });
</script>    