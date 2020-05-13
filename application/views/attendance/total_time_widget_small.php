<?php
$panel = "";
$total_count = "";
if ($widget_type == "total_hours_worked") {
    $panel = "info";
    $total_count = $total_hours_worked;
} else if ($widget_type == "total_project_hours") {
    $panel = "primary";
    $total_count = $total_project_hours;
}
?>

<div class="panel panel-<?php echo $panel; ?>">
    <div class="panel-body text-white">
        <div class="widget-icon">
            <i class="fa fa-clock-o"></i>
        </div>
        <div class="widget-details">
            <h1><?php echo $total_count; ?></h1>
            <?php echo lang($widget_type); ?>
        </div>
    </div>
</div>