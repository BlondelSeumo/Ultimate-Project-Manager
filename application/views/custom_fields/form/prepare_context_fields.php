<?php
$label_column = isset($label_column) ? $label_column : "col-md-3";
$field_column = isset($field_column) ? $field_column : "col-md-9";

foreach ($custom_fields as $field) {
    ?>
    <div class="form-group " data-field-type="<?php echo $field->field_type; ?>">
        <label for="custom_field_<?php echo $field->id ?>" class="<?php echo $label_column; ?>"><?php echo $field->title; ?></label>

        <div class="<?php echo $field_column; ?>">
            <?php
            if ($this->login_user->user_type == "client" && $field->disable_editing_by_clients) {
                //for clients, if the 'Disable editing by clients' setting is enabled
                //show the output instead of input
                $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $field->value));
            } else {
                $this->load->view("custom_fields/input_" . $field->field_type, array("field_info" => $field));
            }
            ?> 
        </div>
    </div>
<?php } ?>