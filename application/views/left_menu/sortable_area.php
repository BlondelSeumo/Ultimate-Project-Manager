<div class="p15 bg-off-white clearfix" id="js-left-menu-customization-area">

    <div class="row">
        <div class="col-md-4">
            <div class="bg-white text-center">
                <div class="p15 strong text-center"><?php echo lang("available_menu_items"); ?></div>
                <div style="overflow-x: hidden" id="menu-item-list-1" class="js-left-menu-scrollbar available-items-container menu-item-list p15 pt0"><?php echo $available_items; ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-white">
                <div class="p15 strong text-center"><?php echo lang("left_menu"); ?></div>
                <div class="p15 pt0"><?php echo $sortable_items; ?></div>
                <div class="p15 pt0"><?php echo modal_anchor(get_uri("left_menus/add_menu_item_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_menu_item'), array("class" => "btn btn-default block custom-menu-item-add-button", "title" => lang('add_menu_item'))); ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-white">
                <div class="p15 text-center">
                    <strong><?php echo lang("preview"); ?></strong>
                    <div class="text-off"><?php echo lang("left_menu_preview_message"); ?></div>
                </div>
                <div id="left-menu-preview" class="js-left-menu-scrollbar p15 pt0"><?php echo $preview; ?></div>
            </div>
        </div>
    </div>

</div>