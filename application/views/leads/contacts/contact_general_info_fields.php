<input type="hidden" name="contact_id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="client_id" value="<?php echo $model_info->client_id; ?>" />
<div class="form-group">
    <?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
    ?>
    <label for="first_name" class="<?php echo $label_column; ?>"><?php echo lang('first_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "first_name",
            "name" => "first_name",
            "value" => $model_info->first_name,
            "class" => "form-control",
            "placeholder" => lang('first_name'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="last_name" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "last_name",
            "name" => "last_name",
            "value" => $model_info->last_name,
            "class" => "form-control",
            "placeholder" => lang('last_name'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="email" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "email",
            "name" => "email",
            "value" => $model_info->email,
            "class" => "form-control",
            "placeholder" => lang('email'),
            "data-rule-email" => true,
            "data-msg-email" => lang("enter_valid_email"),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off"
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
            "value" => $model_info->phone ? $model_info->phone : "",
            "class" => "form-control",
            "placeholder" => lang('phone')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="skype" class="<?php echo $label_column; ?>">Skype</label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "skype",
            "name" => "skype",
            "value" => $model_info->skype ? $model_info->skype : "",
            "class" => "form-control",
            "placeholder" => "Skype"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="job_title" class="<?php echo $label_column; ?>"><?php echo lang('job_title'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "job_title",
            "name" => "job_title",
            "value" => $model_info->job_title,
            "class" => "form-control",
            "placeholder" => lang('job_title')
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="gender" class="<?php echo $label_column; ?>"><?php echo lang('gender'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "gender_male",
            "name" => "gender",
            "data-msg-required" => lang("field_required"),
                ), "male", ($model_info->gender == "female") ? false : true);
        ?>
        <label for="gender_male" class="mr15"><?php echo lang('male'); ?></label> <?php
        echo form_radio(array(
            "id" => "gender_female",
            "name" => "gender",
            "data-msg-required" => lang("field_required"),
                ), "female", ($model_info->gender == "female") ? true : false);
        ?>
        <label for="gender_female" class=""><?php echo lang('female'); ?></label>
    </div>
</div>

<?php $this->load->view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<?php if ($this->login_user->is_admin && $model_info->id) { ?>
    <div class="form-group ">
        <label for="is_primary_contact"  class="<?php echo $label_column; ?>"><?php echo lang('primary_contact'); ?></label>

        <div class="<?php echo $field_column; ?>">
            <?php
            //is set primary contact, disable the checkbox
            $disable = "";
            if ($model_info->is_primary_contact) {
                $disable = "disabled='disabled'";
            }
            echo form_checkbox("is_primary_contact", "1", $model_info->is_primary_contact, "id='is_primary_contact' $disable");
            ?> 
        </div>
    </div>
<?php } ?> 