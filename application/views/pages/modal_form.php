<?php echo form_open(get_uri("pages/save"), array("id" => "add-page-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="form-group">
        <label for="title" class=" col-md-2"><?php echo lang('title'); ?></label>
        <div class=" col-md-10">
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
        <label for="page_content" class=" col-md-2"><?php echo lang('content'); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_textarea(array(
                "id" => "page_content",
                "name" => "content",
                "value" => $model_info->content,
                "class" => "form-control"
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="slug" class=" col-md-2"><?php echo lang("slug"); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_input(array(
                "id" => "slug",
                "name" => "slug",
                "value" => $model_info->slug,
                "class" => "form-control",
                "placeholder" => get_uri("about") . "/[" . strtolower(lang("slug")) . "]",
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>            
    </div>
    <div class="form-group">
        <label for="status" class=" col-md-2"><?php echo lang('status'); ?></label>
        <div class="col-md-10">
            <?php
            $status_dropdown = array("active" => lang("active"), "inactive" => lang("inactive"));
            echo form_dropdown("status", $status_dropdown, $model_info->status, "class='select2'");
            ?>
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
        $("#add-page-form").appForm({
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "content") {
                        data[index]["value"] = encodeAjaxPostData(getWYSIWYGEditorHTML("#page_content"));
                    }
                });
            },
            onSuccess: function (result) {
                $("#pages-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        initWYSIWYGEditor("#page_content", {height: 150});
        $("#title").focus();
        $("#add-page-form .select2").select2();
    });
</script>    