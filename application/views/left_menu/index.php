<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = $setting_active_tab;
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("left_menus/save"), array("id" => "left-menu-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

            <input type="hidden" name="data" id="items-data" value=""/>
            <input type="hidden" name="type" value="<?php echo $type; ?>"/>

            <div class="panel panel-default">
                <div class="page-title clearfix">
                    <h4>
                        <?php
                        if ($type == "client_default") {
                            echo lang('left_menu_for_client');
                        } else {
                            echo lang('left_menu');
                        }
                        ?>
                    </h4>
                    <div class="title-button-group">
                        <?php
                        if ($type == "client_default" && get_setting("default_client_left_menu")) {
                            echo anchor(get_uri("left_menus/restore/client_default"), "<span class='fa fa-refresh'></span> " . lang("restore_to_default"), array("class" => "btn btn-danger"));
                        } else if (get_setting("default_left_menu")) {
                            echo anchor(get_uri("left_menus/restore"), "<span class='fa fa-refresh'></span> " . lang("restore_to_default"), array("class" => "btn btn-danger"));
                        }
                        ?>
                        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="text-off pb15">
                        <?php
                        if ($type == "client_default") {
                            echo lang('left_menu_setting_help_message_for_client');
                        } else {
                            echo lang('left_menu_setting_help_message');
                        }
                        ?>
                    </div>

                    <?php $this->load->view("left_menu/sortable_area"); ?>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php $this->load->view("left_menu/helper_js"); ?>
