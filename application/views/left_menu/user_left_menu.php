<?php echo form_open(get_uri("left_menus/save"), array("id" => "left-menu-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

<input type="hidden" name="data" id="items-data" value=""/>
<input type="hidden" name="type" value="user"/>

<div class="panel panel-default">
    <div class="page-title clearfix">
        <h4> <?php echo lang('left_menu'); ?></h4>
        <div class="title-button-group">
            <?php
            if (get_setting("user_" . $this->login_user->id . "_left_menu")) {
                echo anchor(get_uri("left_menus/restore/user"), "<span class='fa fa-refresh'></span> " . lang("restore_to_default"), array("class" => "btn btn-danger"));
            }
            ?>
            <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>

    <div class="panel-body">
        <?php $this->load->view("left_menu/sortable_area"); ?>
    </div>
</div>

<?php echo form_close(); ?>

<?php $this->load->view("left_menu/helper_js"); ?>

