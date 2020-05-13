<div id="sidebar" class="box-content ani-width">
    <div id="sidebar-scroll">
        <ul class="" id="sidebar-menu">
            <?php
            if (!$is_preview) {
                $sidebar_menu = get_active_menu($sidebar_menu);
            }

            foreach ($sidebar_menu as $main_menu) {
                if (isset($main_menu["name"])) {
                    $submenu = get_array_value($main_menu, "submenu");
                    $expend_class = $submenu ? " expand " : "";
                    $active_class = isset($main_menu["is_active_menu"]) ? "active" : "";

                    $submenu_open_class = "";
                    if ($expend_class && $active_class) {
                        $submenu_open_class = " open ";
                    }

                    $devider_class = ($show_devider && get_array_value($main_menu, "devider")) ? "devider" : "";
                    $badge = get_array_value($main_menu, "badge");
                    $badge_class = get_array_value($main_menu, "badge_class");
                    ?>
                    <li class="<?php echo $active_class . " " . $expend_class . " " . $submenu_open_class . " $devider_class"; ?> main">
                        <a href="<?php echo isset($main_menu['is_custom_menu_item']) ? $main_menu['url'] : get_uri($main_menu['url']); ?>">
                            <i class="fa <?php echo ($main_menu['class']); ?>"></i>
                            <span><?php echo isset($main_menu['is_custom_menu_item']) ? $main_menu['name'] : lang($main_menu['name']); ?></span>
                            <?php
                            if ($badge) {
                                echo "<span class='badge $badge_class'>$badge</span>";
                            }
                            ?>
                        </a>
                        <?php
                        if ($submenu) {
                            echo "<ul>";
                            foreach ($submenu as $s_menu) {
                                if (isset($s_menu['name'])) {
                                    ?>
                                <li>
                                    <a href="<?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['url'] : get_uri($s_menu['url']); ?>">
                                        <i class="dot fa fa-circle"></i>
                                        <span><?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['name'] : lang($s_menu['name']); ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        echo "</ul>";
                    }
                    ?>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div><!-- sidebar menu end -->