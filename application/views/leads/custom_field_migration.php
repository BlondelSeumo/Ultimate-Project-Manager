<?php if (isset($custom_fields) && $custom_fields) { ?>
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('custom_field_migration'); ?></label>
        <div class="<?php echo $field_column; ?> custom-field-migration-fields">
            <div class="pb10">
                <?php echo form_checkbox("merge_custom_fields-$model_info->id", "1", true, "id='merge_custom_fields-$model_info->id'"); ?> 
                <label for="merge_custom_fields-<?php echo $model_info->id; ?>"><?php echo lang('merge_custom_fields'); ?></label>
                <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo sprintf(lang('merge_custom_fields_help_message'), lang($to_custom_field_type), lang($to_custom_field_type)); ?>"><i class="fa fa-question-circle"></i></span>
            </div>

            <div class="pb10">
                <?php echo form_checkbox("do_not_merge-$model_info->id", "1", false, "id='do_not_merge-$model_info->id'"); ?> 
                <label for="do_not_merge-<?php echo $model_info->id; ?>"><?php echo lang('do_not_merge'); ?></label>
            </div>
        </div>
    </div>
<?php } ?>