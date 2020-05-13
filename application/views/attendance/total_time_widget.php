<div class="box <?php echo ($show_projects_count && ($show_total_hours_worked || $show_total_project_hours)) ? 'b-t' : ''; ?>">

    <?php if ($show_total_hours_worked) { ?>
        <div class="box-content widget-container <?php echo $show_total_project_hours ? 'b-r' : ''; ?>">
            <div class="panel-body ">
                <h1><?php echo $total_hours_worked; ?></h1>
                <span class="text-off uppercase"><?php echo lang("total_hours_worked"); ?></span>
            </div>
        </div>
    <?php } ?>

    <?php if ($show_total_project_hours) { ?>
        <div class="box-content widget-container">
            <div class="panel-body ">
                <h1 class=""><?php echo $total_project_hours; ?></h1>
                <span class="text-off uppercase"><?php echo lang("total_project_hours"); ?></span>
            </div>
        </div>
    <?php } ?>

</div>