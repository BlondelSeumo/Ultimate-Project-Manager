<?php echo form_open(get_uri("left_menus/prepare_custom_menu_item_data"), array("id" => "custom-menu-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="form-group">
        <input type="hidden" name="is_sub_menu" value="<?php echo $model_info->is_sub_menu; ?>" />
        <label for="title" class=" col-md-3"><?php echo lang('title'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "title",
                "name" => "title",
                "value" => $model_info->title,
                "class" => "form-control",
                "placeholder" => lang('title'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="url" class=" col-md-3"><?php echo lang('url'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "url",
                "name" => "url",
                "value" => $model_info->url,
                "class" => "form-control",
                "placeholder" => lang('url'),
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <?php $this->load->view("left_menu/icon_plate"); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#custom-menu-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    addOrUpdateCustomMenuItem(result.item_data);
                    saveItemsPosition();
                }
            }
        });

        $("#title").focus();
    });
</script>