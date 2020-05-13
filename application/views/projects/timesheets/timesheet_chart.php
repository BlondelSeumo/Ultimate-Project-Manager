<div class="panel">
    <div class="panel-heading clearfix">
        <strong><i class="fa fa-bar-chart pt10"></i>&nbsp; <?php echo lang("chart"); ?></strong>
        <div class="pull-right clearfix">
            <span id="monthly-chart-date-range-selector" class="pull-right"></span>
            <?php
            echo form_input(array(
                "id" => "members-dropdown",
                "name" => "members-dropdown",
                "class" => "select2 w200 reload-timesheet-chart pull-right",
                "placeholder" => lang('member')
            ));
            ?>
            <?php
            if (!$project_id) {
                echo form_input(array(
                    "id" => "projects-dropdown",
                    "name" => "projects-dropdown",
                    "class" => "select2 w200 reload-timesheet-chart pull-right mr15",
                    "placeholder" => lang('project')
                ));
            }
            ?>
        </div>
    </div>
    <div class="panel-body">
        <div id="timesheet-statistics-flotchart" style="width: 100%; height: 350px;"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date = {};

        //initialize data
        $("#members-dropdown").select2({data: <?php echo $members_dropdown; ?>});
        $("#projects-dropdown").select2({data: <?php echo $projects_dropdown; ?>});

        //initialize timesheet statistics flotchart
        initTimesheetStatisticsFlotchart = function (timesheets, ticks) {
            dataset = [
                {
                    data: timesheets,
                    color: "rgba(0, 179, 147, 1)",
                    lines: {
                        show: true,
                        fill: 0.2
                    },
                    points: {
                        show: false
                    },
                    shadowSize: 0
                },
                {
                    label: "<?php echo lang('timesheets'); ?>",
                    data: timesheets,
                    color: "rgba(0, 179, 147, 1)",
                    lines: {
                        show: false
                    },
                    points: {
                        show: true,
                        fill: true,
                        radius: 4,
                        fillColor: "#fff",
                        lineWidth: 1
                    },
                    shadowSize: 0,
                    curvedLines: {
                        apply: false
                    }
                }
            ];

            $.plot("#timesheet-statistics-flotchart", dataset, {
                series: {
                    curvedLines: {
                        apply: true,
                        active: true,
                        monotonicFit: true
                    }
                },
                legend: {
                    show: true
                },
                yaxis: {
                    min: 0
                },
                xaxis: {
                    ticks: ticks
                },
                grid: {
                    color: "#bbb",
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: '#FFF'
                },
                tooltip: true,
                tooltipOpts: {
                    content: function (x, y, z) {
                        if (x) {
                            return secondsToTimeFormat(z * 60);
                        } else {
                            return false;
                        }
                    },
                    defaultTheme: false
                }
            });

        };

        //prepare timesheet statistics flotchart
        prepareTimesheetStatisticsFlotchart = function () {
            appLoader.show();

            var user_id = $("#members-dropdown").val() || "0", project_id = $("#projects-dropdown").val() || "0";

            $.ajax({
                url: "<?php echo_uri("projects/timesheet_chart_data/$project_id") ?>",
                data: {start_date: date.start_date, end_date: date.end_date, user_id: user_id, project_id: project_id},
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function (response) {
                    appLoader.hide();
                    initTimesheetStatisticsFlotchart(response.timesheets, response.ticks);
                }
            });
        };

        $("#monthly-chart-date-range-selector").appDateRange({
            dateRangeType: "monthly",
            onChange: function (dateRange) {
                date = dateRange;
                prepareTimesheetStatisticsFlotchart();
            },
            onInit: function (dateRange) {
                date = dateRange;
                prepareTimesheetStatisticsFlotchart();
            }
        });

        $(".reload-timesheet-chart").change(function () {
            prepareTimesheetStatisticsFlotchart();
        });

    });
</script>    

