<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="company_name" class="<?php echo $label_column; ?>"><?php echo lang('company_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_name",
            "name" => "company_name",
            "value" => $model_info->company_name,
            "class" => "form-control",
            "placeholder" => lang('company_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="lead_status_id" class="<?php echo $label_column; ?>"><?php echo lang('status'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        foreach ($statuses as $status) {
            $lead_status[$status->id] = $status->title;
        }

        echo form_dropdown("lead_status_id", $lead_status, array($model_info->lead_status_id), "class='select2'");
        ?>
    </div>
</div>
<div class="form-group">
    <label for="owner_id" class="<?php echo $label_column; ?>"><?php echo lang('owner'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "owner_id",
            "name" => "owner_id",
            "value" => $model_info->owner_id ? $model_info->owner_id : $this->login_user->id,
            "class" => "form-control",
            "placeholder" => lang('owner')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="lead_source_id" class="<?php echo $label_column; ?>"><?php echo lang('source'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        $lead_source = array();

        foreach ($sources as $source) {
            $lead_source[$source->id] = $source->title;
        }

        echo form_dropdown("lead_source_id", $lead_source, array($model_info->lead_source_id), "class='select2'");
        ?>
    </div>
</div>
<div class="form-group">
    <label for="address" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "address",
            "name" => "address",
            "value" => $model_info->address ? $model_info->address : "",
            "class" => "form-control",
            "placeholder" => lang('address')
        ));
        ?>

    </div>
</div>
<div class="form-group">
    <label for="city" class="<?php echo $label_column; ?>"><?php echo lang('city'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "city",
            "name" => "city",
            "value" => $model_info->city,
            "class" => "form-control",
            "placeholder" => lang('city')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="state" class="<?php echo $label_column; ?>"><?php echo lang('state'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "state",
            "name" => "state",
            "value" => $model_info->state,
            "class" => "form-control",
            "placeholder" => lang('state')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="zip" class="<?php echo $label_column; ?>"><?php echo lang('zip'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "zip",
            "name" => "zip",
            "value" => $model_info->zip,
            "class" => "form-control",
            "placeholder" => lang('zip')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="country" class="<?php echo $label_column; ?>"><?php echo lang('country'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "country",
            "name" => "country",
            "value" => $model_info->country,
            "class" => "form-control",
            "placeholder" => lang('country')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="phone" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "phone",
            "name" => "phone",
            "value" => $model_info->phone,
            "class" => "form-control",
            "placeholder" => lang('phone')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="website" class="<?php echo $label_column; ?>"><?php echo lang('website'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "website",
            "name" => "website",
            "value" => $model_info->website,
            "class" => "form-control",
            "placeholder" => lang('website')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="vat_number" class="<?php echo $label_column; ?>"><?php echo lang('vat_number'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "vat_number",
            "name" => "vat_number",
            "value" => $model_info->vat_number,
            "class" => "form-control",
            "placeholder" => lang('vat_number')
        ));
        ?>
    </div>
</div>

<?php if ($this->login_user->is_admin && get_setting("module_invoice")) { ?>
    <div class="form-group">
        <label for="currency" class="<?php echo $label_column; ?>"><?php echo lang('currency'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "currency",
                "name" => "currency",
                "value" => $model_info->currency,
                "class" => "form-control",
                "placeholder" => lang('keep_it_blank_to_use_default') . " (" . get_setting("default_currency") . ")"
            ));
            ?>
        </div>
    </div>    
    <div class="form-group">
        <label for="currency_symbol" class="<?php echo $label_column; ?>"><?php echo lang('currency_symbol'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "currency_symbol",
                "name" => "currency_symbol",
                "value" => $model_info->currency_symbol,
                "class" => "form-control",
                "placeholder" => lang('keep_it_blank_to_use_default') . " (" . get_setting("currency_symbol") . ")"
            ));
            ?>
        </div>
    </div>
<?php } ?>

<?php $this->load->view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $(".select2").select2();

<?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({data: <?php echo json_encode($currency_dropdown); ?>});
            }
<?php } ?>

        $('#owner_id').select2({data: <?php echo json_encode($owners_dropdown); ?>});

    });
</script>