<?php
$user_id = $this->login_user->id;
echo form_open(get_uri("tickets/save_settings"), array("id" => "ticket-settings-form", "class" => "general-form", "role" => "form"));
?>
<div class="modal-body clearfix">
    <div class="form-group">
        <label for="signature" class=" col-md-3"><?php echo lang('signature'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "signature",
                "name" => "signature",
                "class" => "form-control",
                "value" => get_setting('user_' . $user_id . '_signature'),
                "placeholder" => lang('signature'),
                "data-rich-text-editor" => true
            ));
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
        $("#ticket-settings-form").appForm({
        });
    });
</script>    