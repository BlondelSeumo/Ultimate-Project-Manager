<?php
if (count($notifications)) {

    foreach ($notifications as $notification) {
        //get url attributes
        $url_attributes_array = get_notification_url_attributes($notification);
        $url_attributes = get_array_value($url_attributes_array, "url_attributes");
        $url = get_array_value($url_attributes_array, "url");

        //check read/unread class
        $notification_class = "";
        if (!$notification->is_read) {
            $notification_class = "unread-notification";
        }

        if (!$url || $url == "#") {
            $notification_class .= " not-clickable";
        }

        //for custom field changes, we've to check if the field has any restrictions 
        //like 'visible to admins only' or 'hide from clients'
        $changes_array = array();
        if ($notification->activity_log_changes !== "") {
            $changes_array = get_change_logs_array($notification->activity_log_changes, $notification->activity_log_type, "all");
        }

        if ($notification->activity_log_changes == "" || ($notification->activity_log_changes !== "" && count($changes_array))) {
            ?>

            <a class="list-group-item <?php echo $notification_class; ?>" data-notification-id="<?php echo $notification->id; ?>" <?php echo $url_attributes; ?> >
                <div class="media-left">
                    <span class="avatar avatar-xs">
                        <img src="<?php echo get_avatar($notification->user_id ? $notification->user_image : "system_bot"); ?>" alt="..." />
                        <!--  if user name is not present then -->
                    </span>
                </div>
                <div class="media-body w100p">
                    <div class="media-heading">
                        <strong><?php echo $notification->user_id ? $notification->user_name : get_setting("app_title"); ?></strong>
                        <span class="text-off pull-right"><small><?php echo format_to_relative_time($notification->created_at); ?></small></span>
                    </div>
                    <div class="media m0">
                        <?php
                        echo sprintf(lang("notification_" . $notification->event), "<strong>" . $notification->to_user_name . "</strong>");

                        $this->load->view("notifications/notification_description", array("notification" => $notification, "changes_array" => $changes_array));
                        ?>
                    </div>
                </div>
            </a>
            <?php
        }
    }

    if ($result_remaining) {
        $next_container_id = "load" . $next_page_offset;
        ?>
        <div id="<?php echo $next_container_id; ?>">

        </div>

        <div id="loader-<?php echo $next_container_id; ?>" >
            <div class="text-center p20 clearfix mt-5">
                <?php
                echo ajax_anchor(get_uri("notifications/load_more/" . $next_page_offset), lang("load_more"), array("class" => "btn btn-default load-more mt15 p10", "data-remove-on-success" => "#loader-" . $next_container_id, "title" => lang("load_more"), "data-inline-loader" => "1", "data-real-target" => "#" . $next_container_id));
                ?>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <span class="list-group-item"><?php echo lang("no_new_notifications"); ?></span>               
<?php } ?>


<script type="text/javascript">
    $(document).ready(function () {
        $(".unread-notification").click(function (e) {
            $.ajax({
                url: '<?php echo get_uri("notifications/set_notification_status_as_read") ?>/' + $(this).attr("data-notification-id")
            });
            $(this).removeClass("unread-notification");
        });
    });
</script>