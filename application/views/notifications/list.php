<div class="panel panel-default mb0">
    <div class="page-title clearfix notificatio-plate-title-area">
        <span class="pull-left"><strong><?php echo lang('notifications'); ?></strong></span>
        <span class="pull-right"><?php echo get_team_member_profile_link($this->login_user->id . '/my_preferences', lang('settings')); ?></span>
        <span class="pull-right dot">&CenterDot;</span>
        <span class="pull-right"><?php echo js_anchor(lang("mark_all_as_read"), array("class" => "mark-all-as-read-button")); ?></span>
    </div>

    <div class="list-group" id="notificaion-popup-list" style="">
        <?php
        $view_data["notifications"] = $notifications;
        $this->load->view("notifications/list_data", $view_data);
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //don't apply scrollbar for mobile devices
        if ($(window).width() > 640) {
            if ($('#notificaion-popup-list').height() >= 400) {
                initScrollbar('#notificaion-popup-list', {
                    setHeight: 400
                });
            } else {
                $('#notificaion-popup-list').css({"overflow-y": "auto"});
            }

        }

        //mark all notification as read
        $('body').on('click', '.mark-all-as-read-button', function (e) {
            appLoader.show();

            //stop default dropdown operation
            e.stopPropagation();
            e.preventDefault();

            $.ajax({
                url: "<?php echo get_uri('notifications/set_notification_status_as_read') ?>",
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        $(".unread-notification").removeClass("unread-notification");
                        appAlert.success(result.message, {duration: 10000});
                        appLoader.hide();
                    }
                }
            });
        });
    });
</script>
