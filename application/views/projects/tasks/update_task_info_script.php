<?php
$status_dropdown_for_update = array();
foreach ($statuses as $status) {
    $status_dropdown_for_update[] = array("value" => $status->id, "text" => $status->key_name ? lang($status->key_name) : $status->title);
}

$points_dropdown_for_update = array();
foreach ($points_dropdown as $key => $value) {
    $points_dropdown_for_update[] = array("id" => $key, "text" => $value);
}
?>


<script type="text/javascript">
    $(document).ready(function () {
        //select2 single
        $('body').on('click', '[data-act=update-task-info]', function (e) {
            var $instance = $(this),
                    type = $(this).attr('data-act-type'),
                    source = "",
                    select2Option = {},
                    showbuttons = false,
                    placement = "right",
                    editableType = "select2",
                    datepicker = {};

            if (type === "status_id") {
                source = <?php echo json_encode($status_dropdown_for_update); ?>
            } else if (type === "milestone_id") {
                source = <?php echo json_encode($milestones_dropdown); ?>
            } else if (type === "assigned_to") {
                source = <?php echo json_encode($assign_to_dropdown); ?>
            } else if (type === "points") {
                source = <?php echo json_encode($points_dropdown_for_update); ?>
            } else if (type === "collaborators") {
                e.stopPropagation();
                e.preventDefault();

                select2Option = {multiple: true};
                showbuttons = true;
                placement = "top";
                source = <?php echo json_encode($collaborators_dropdown); ?>
            } else if (type === "labels") {
                e.stopPropagation();
                e.preventDefault();

                source = <?php echo json_encode($label_suggestions); ?>;
                showbuttons = true;
                select2Option = {tags: source};
                placement = "bottom";
            } else if (type === "start_date" || type === "deadline") {
                editableType = "date";
                datepicker = {
                    weekStart: <?php echo get_setting('first_day_of_week'); ?>,
                    todayHighlight: true
                };

                if (type === "deadline") {
                    datepicker["endDate"] = "<?php echo $project_deadline; ?>";
                }
            }

            $(this).editable({
                type: editableType,
                pk: 1,
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                value: $(this).attr('data-value'),
                url: '<?php echo_uri("projects/update_task_info") ?>/' + $(this).attr('data-id') + '/' + $(this).attr('data-act-type'),
                showbuttons: showbuttons,
                datepicker: datepicker,
                clear: false, //clear button for datepicker
                source: source,
                select2: select2Option,
                placement: placement,
                autotext: "never",
                success: function (response, newValue) {
                    if (response.success) {
                        if (type === "assigned_to" && response.assigned_to_avatar) {
                            $("#task-assigned-to-avatar").attr("src", response.assigned_to_avatar);

                            if (response.assigned_to_id === "0") {
                                setTimeout(function () {
                                    $instance.html("<?php echo lang("add_assignee"); ?>");
                                }, 50);
                            }
                        }

                        if (type === "status_id" && response.status_color) {
                            $instance.closest("span").css("background-color", response.status_color);
                        }

                        if (type === "points" && response.points) {
                            setTimeout(function () {
                                $instance.html(response.points);
                            }, 50);
                        }

                        if (type === "labels" && response.labels) {
                            setTimeout(function () {
                                $instance.html(response.labels);
                            }, 50);
                        }

                        if (type === "collaborators" && response.collaborators) {
                            setTimeout(function () {
                                $instance.html(response.collaborators);
                            }, 50);
                        }

                        if ((type === "start_date" || type === "deadline") && response.date) {
                            setTimeout(function () {
                                $instance.html(response.date);
                            }, 50);
                        }

                        $("#task-table").appTable({newData: response.data, dataId: response.id});

                        appLoader.hide();
                    }
                }
            });

            $(this).editable("show");
        });
    });
</script>