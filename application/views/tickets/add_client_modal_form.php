<?php echo form_open(get_uri("tickets/link_to_client"), array("id" => "ticket-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />

    <div class="form-group">
        <label for="client_id" class=" col-md-3"><?php echo lang('client'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_dropdown("client_id", $clients_dropdown, array(""), "class='select2 validate-hidden' id='client_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
        $("#ticket-form").appForm({
            onSuccess: function (result) {
                location.reload();
            }
        });

        $("#ticket-form .select2").select2();
    });
</script>