<?php
if (!$project_id) {
    load_css(array(
        "assets/js/gantt-chart/gantt.css",
    ));
    load_js(array(
        "assets/js/gantt-chart/gantt.js",
    ));
    echo "<div id='page-content' class='p20 clearfix'>";
}
?>
<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('gantt'); ?></h4>
        <div class="pull-right p10 mr10">
            <?php
            if ($show_project_members_dropdown) {
                echo lang("group_by") . " : ";
                $milestones_and_members_group_by = array("milestones" => lang("milestones"), "members" => lang("team_members"));

                $project_group_by = array();
                if (!$project_id) {
                    $project_group_by = array("projects" => lang("projects"));
                }

                $gantt_group_by = array_merge($milestones_and_members_group_by, $project_group_by);

                echo form_dropdown("gantt-group-by", $gantt_group_by, array(), "class='select2 w200 mr10 reload-gantt' id='gantt-group-by'");
            }
            ?>
            <?php
            if (!$project_id) {
                echo form_input(array(
                    "id" => "gantt-projects-dropdown",
                    "name" => "gantt-projects-dropdown",
                    "class" => "select2 w200 reload-gantt",
                    "placeholder" => lang('project')
                ));
            }
            ?>
            <?php
            if ($show_project_members_dropdown) {
                echo form_input(array(
                    "id" => "gantt-members-dropdown",
                    "name" => "gantt-members-dropdown",
                    "class" => "select2 w200 reload-gantt hide",
                    "placeholder" => lang('team_member')
                ));
            }
            ?>
            <?php
            echo form_input(array(
                "id" => "gantt-milestone-dropdown",
                "name" => "gantt-milestone-dropdown",
                "class" => "select2 w200 reload-gantt",
                "placeholder" => lang('milestone')
            ));
            ?>
            <?php
            echo "<span class='dropdown-apploader-section'>";
            echo form_input(array(
                "id" => "gantt-status-dropdown",
                "name" => "gantt-status-dropdown",
                "class" => "select2 w200 reload-gantt ml10",
                "placeholder" => lang('status')
            ));
            echo "</span>";
            ?>
        </div>
    </div>
    <div class="w100p pt10">
        <div id="gantt-chart" style="width: 100%;"></div>
    </div>

</div>
<?php
if (!$project_id) {
    echo "</div>";
}
?>

<script type="text/javascript">
    var loadGantt = function (group_by, id, status, projectId) {
        group_by = group_by || "milestones";
        id = id || "0";
        status = status || "";
        projectId = projectId || "<?php echo $project_id; ?>";

        $("#gantt-chart").html("<div style='height:100px;'></div>");
        appLoader.show({container: "#gantt-chart", css: "right:50%;"});

        $("#gantt-chart").ganttView({
            dataUrl: "<?php echo get_uri("projects/gantt_data/"); ?>" + projectId + "/" + group_by + "/" + id + "/" + status,
            monthNames: AppLanugage.months,
            dayText: "<?php echo lang('day'); ?>",
            daysText: "<?php echo lang('days'); ?>"
        });
    };

    $(document).ready(function () {
        var $ganttGroupBy = $("#gantt-group-by"),
                $ganttProjects = $("#gantt-projects-dropdown"),
                $ganttMilestone = $("#gantt-milestone-dropdown"),
                $ganttMembers = $("#gantt-members-dropdown"),
                $ganttStatus = $("#gantt-status-dropdown");

        $ganttGroupBy.select2();

        $ganttMilestone.select2({
            data: <?php echo $milestone_dropdown; ?>
        });

        if ($ganttMembers.length) {
            $ganttMembers.select2({
                data: <?php echo $project_members_dropdown; ?>
            });
        }

        $ganttStatus.select2({
            data: <?php echo $status_dropdown; ?>
        });

<?php if (!$project_id) { ?>
            $ganttProjects.select2({
                data: <?php echo $projects_dropdown; ?>
            });
<?php } ?>

        $(".reload-gantt").change(function () {
            var group_by = $ganttGroupBy.val() || "milestones" || "projects",
                    id = 0,
                    status = $ganttStatus.val();

            if (group_by === "milestones") {
                $ganttMilestone.removeClass("hide");
                id = $ganttMilestone.val();
                $ganttMembers.addClass("hide");
            } else {
                $ganttMembers.removeClass("hide");
                id = $("#gantt-members-dropdown").val();
                $ganttMilestone.addClass("hide");
            }

            var projectId = $ganttProjects.val() || "<?php echo $project_id; ?>";

            loadGantt(group_by, id, status, projectId);
        });

        //refresh milestones on changing of project
        $ganttProjects.on("change", function () {
            var projectId = $(this).val();
            if ($(this).val()) {
                $ganttMilestone.select2("destroy");
                $ganttMilestone.hide();
                appLoader.show({container: "#dropdown-apploader-section"});
                $.ajax({
                    url: "<?php echo get_uri('projects/get_all_related_data_of_selected_project') ?>" + "/" + projectId,
                    dataType: "json",
                    success: function (result) {
                        $ganttMilestone.show().val("");
                        $ganttMilestone.select2({data: result.milestones_dropdown});
                        appLoader.hide();
                    }
                });
            }
        });

        loadGantt();
    });
</script>