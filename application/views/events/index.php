<?php
load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/lang-all.js",
    "assets/js/bootstrap-confirmation/bootstrap-confirmation.js"
));

$client = "";
if (isset($client_id)) {
    $client = $client_id;
}
?>

<div id="page-content<?php echo $client; ?>" class="p20<?php echo $client; ?> clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <?php if ($client) { ?>
                <h4><?php echo lang('events'); ?></h4>
            <?php } else { ?>
                <h1><?php echo lang('event_calendar'); ?></h1>
            <?php } ?>
            <div class="title-button-group">


                <?php if ($calendar_filter_dropdown) { ?>
                    <div id="calendar-filter-dropdown" class="pull-left <?php echo (count($calendar_filter_dropdown) == 1) ? "hide" : ""; ?>"></div>
                <?php } ?>


                <?php
                if (get_setting("enable_google_calendar_api")) {
                    echo modal_anchor(get_uri("events/google_calendar_settings_modal_form"), "<i class='fa fa-cog'></i> " . lang('google_calendar_settings'), array("class" => "btn btn-default", "title" => lang('google_calendar_settings')));
                }
                ?>

                <?php echo modal_anchor(get_uri("events/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_event'), array("class" => "btn btn-default", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/modal_form"), "", array("class" => "hide", "id" => "add_event_hidden", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/view"), "", array("class" => "hide", "id" => "show_event_hidden", "data-post-client_id" => $client, "data-post-cycle" => "0", "data-post-editable" => "1", "title" => lang('event_details'))); ?>
                <?php echo modal_anchor(get_uri("leaves/application_details"), "", array("class" => "hide", "data-post-id" => "", "id" => "show_leave_hidden")); ?>
                <?php echo modal_anchor(get_uri("projects/task_view"), "", array("class" => "hide", "data-post-id" => "", "id" => "show_task_hidden")); ?>
            </div>
        </div>
        <div class="panel-body">
            <div id="event-calendar"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var loadCalendar = function (values) {
        var filter_values = values || "events",
                $eventCalendar = $("#event-calendar");

        appLoader.show();

        //destroy existing data
        $eventCalendar.fullCalendar("destroy");

        $eventCalendar.fullCalendar({
            lang: AppLanugage.locale,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: "<?php echo_uri("events/calendar_events/"); ?>" + filter_values + "<?php echo "/$client"; ?>",
            dayClick: function (date, jsEvent, view) {
                $("#add_event_hidden").attr("data-post-start_date", date.format("YYYY-MM-DD"));
                var startTime = date.format("HH:mm:ss");
                if (startTime === "00:00:00") {
                    startTime = "";
                }
                $("#add_event_hidden").attr("data-post-start_time", startTime);
                var endDate = date.add(1, 'hours');

                $("#add_event_hidden").attr("data-post-end_date", endDate.format("YYYY-MM-DD"));
                var endTime = "";
                if (startTime != "") {
                    endTime = endDate.format("HH:mm:ss");
                }

                $("#add_event_hidden").attr("data-post-end_time", endTime);
                $("#add_event_hidden").trigger("click");
            },
            eventClick: function (calEvent, jsEvent, view) {
                if (calEvent.event_type === "event") {
                    $("#show_event_hidden").attr("data-post-id", calEvent.encrypted_event_id);
                    $("#show_event_hidden").attr("data-post-cycle", calEvent.cycle);
                    $("#show_event_hidden").trigger("click");

                } else if (calEvent.event_type === "leave") {
                    $("#show_leave_hidden").attr("data-post-id", calEvent.leave_id);
                    $("#show_leave_hidden").trigger("click");

                } else if (calEvent.event_type === "project_deadline") {
                    window.location = "<?php echo site_url('projects/view'); ?>/" + calEvent.project_id;
                } else if (calEvent.event_type === "task_deadline") {

                    $("#show_task_hidden").attr("data-post-id", calEvent.task_id);
                    $("#show_task_hidden").trigger("click");
                }
            },
            eventRender: function (event, element) {
                if (event.icon) {
                    element.find(".fc-title").prepend("<i class='fa " + event.icon + "'></i> ");
                }
            },
            eventAfterAllRender: function () {
                appLoader.hide();
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });
    };

    $(document).ready(function () {
        $("#calendar-filter-dropdown").appMultiSelect({
            text: "<?php echo lang('event_type'); ?>",
            options: <?php echo json_encode($calendar_filter_dropdown); ?>,
            onChange: function (values) {
                loadCalendar(values.join('-'));
                setCookie("calendar_filters_of_user_<?php echo $this->login_user->id; ?>", values.join('-')); //save filters on browser cookie
            },
            onInit: function (values) {
                loadCalendar(values.join('-'));
            }
        });

        var client = "<?php echo $client; ?>";
        if (client) {
            setTimeout(function () {
                $('#event-calendar').fullCalendar('today');
            });
        }

        //autoload the event popover
        var encrypted_event_id = "<?php echo isset($encrypted_event_id) ? $encrypted_event_id : ''; ?>";
        if (encrypted_event_id) {
            $("#show_event_hidden").attr("data-post-id", encrypted_event_id);
            $("#show_event_hidden").trigger("click");
        }
    });
</script>