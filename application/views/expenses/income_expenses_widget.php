<div class="panel panel-default <?php echo $custom_class; ?>">
    <div class="panel-heading clearfix">
        <i class="fa fa-bar-chart"></i>&nbsp;<?php echo lang("income_vs_expenses"); ?>

        <span class="help pull-right" data-toggle="tooltip" title="<?php echo lang('income_expenses_widget_help_message') ?>"><i class="fa fa-question-circle"></i></span>
    </div>
    <div class="panel-body ">
        <div id="income-expense" style="width: 100%; height: 250px;"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var data = [
            {label: " <?php echo lang("income"); ?>", data: "<?php echo $income ?>" * 1, color: "#00B393"},
            {label: " <?php echo lang("expenses"); ?>", data: "<?php echo $expenses ?>" * 1, color: "#F06C71"}
        ];

        $.plot('#income-expense', data, {
            series: {
                pie: {
                    show: true,
                    innerRadius: 0.5
                }
            },
            legend: {
                show: true
            },
            grid: {
                hoverable: true
            },
            tooltip: {
                show: true,
                content: "%s: %p.0%", //%n show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>    