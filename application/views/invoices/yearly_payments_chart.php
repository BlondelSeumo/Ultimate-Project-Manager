<style type="text/css">
    .flot-y1-axis {
        left: -35px !important;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <i class="fa fa-bar-chart pt10"></i>&nbsp; <?php echo lang("chart"); ?>
        <div class="pull-right">
            <?php
            if ($currencies_dropdown) {
                echo form_input(array(
                    "id" => "payment-chart-currency-dropdown",
                    "name" => "payment-chart-currency-dropdown",
                    "class" => "select2 w200 font-normal",
                    "placeholder" => lang('currency')
                ));
            }
            ?>

            <div id="payment-chart-date-range-selector" class="inline-block"></div>

        </div>
    </div>
    <div class="panel-body ">
        <div style="padding-left:35px;">
            <div id="yearly-payment-chart" style="width:100%; height: 350px;"></div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var preparePaymentsFlotChart = function (data, currency) {
        data["currency"] = currency;

        appLoader.show();
        $.ajax({
            url: "<?php echo_uri("invoice_payments/yearly_chart_data") ?>",
            data: data,
            cache: false,
            type: 'POST',
            dataType: "json",
            success: function (response) {
                appLoader.hide();
                initPaymentsFlotChart(response.data, response.currency_symbol);
            }
        });

    };

    var initPaymentsFlotChart = function (data, currency_symbol) {
        // var data = [["January", 1500], ["February", 100], ["March", 16000], ["April", 0], ["May", 17000], ["June", 10009]];

        $.plot("#yearly-payment-chart", [data], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
                    align: "center"
                }
            },
            colors: ['#009688', 'rgba(0, 150, 136, 0.72)'],
            xaxis: {
                mode: "categories",
                tickLength: 0,
                fillColor: "#ccc",
                background: "#ccc"
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
                        return "%s: " + toCurrency(z, currency_symbol);
                    } else {
                        return  toCurrency(z, currency_symbol);
                    }
                },
                defaultTheme: false
            }
        });
    };

    $(document).ready(function () {
        var date = {}, currency = "";

        $("#payment-chart-date-range-selector").appDateRange({
            dateRangeType: "yearly",
            onChange: function (dateRange) {
                date = dateRange;
                preparePaymentsFlotChart(dateRange, currency);
            },
            onInit: function (dateRange) {
                date = dateRange;
                preparePaymentsFlotChart(dateRange, currency);
            }
        });

        var $currenciesDropdown = $("#payment-chart-currency-dropdown");

<?php if ($currencies_dropdown) { ?>
            $currenciesDropdown.select2({data: <?php echo $currencies_dropdown; ?>});
<?php } ?>

        $currenciesDropdown.change(function () {
            currency = $currenciesDropdown.val();
            preparePaymentsFlotChart(date, currency);
        });
    });
</script>