<?php if (get_setting("enable_footer")) { ?>

    <div class="footer p15 hidden-xs">
        <?php
        $footer_copyright_text = get_setting("footer_copyright_text");
        if ($footer_copyright_text) {
            ?>

            <div class="pull-left">
                <?php echo $footer_copyright_text; ?>
            </div>

        <?php } ?>

        <div class="<?php echo $footer_copyright_text ? "pull-right" : ""; ?>">
            <?php
            $footer_menus = unserialize(get_setting("footer_menus"));
            if ($footer_menus && is_array($footer_menus)) {
                foreach ($footer_menus as $footer) {
                    echo anchor($footer->url, $footer->menu_name);
                }
            }
            ?>
        </div>
    </div>

<?php } ?>
