<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <i class="fa fa fa-file-text-o"></i>&nbsp; <?php echo lang("invoice_statistics"); ?>

        <?php if ($currencies && $this->login_user->user_type == "staff") { ?>
            <div class="pull-right">
                <span class="pull-right dropdown">
                    <div class="dropdown-toggle clickable font-14" type="button" data-toggle="dropdown" aria-expanded="true" >
                        <i class="fa fa-ellipsis-h ml10 mr10"></i>
                    </div>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <?php
                            $default_currency = get_setting("default_currency");
                            echo js_anchor($default_currency, array("class" => "load-currency-wise-data", "data-value" => $default_currency)); //default currency

                            foreach ($currencies as $currency) {
                                echo js_anchor($currency->currency, array("class" => "load-currency-wise-data", "data-value" => $currency->currency));
                            }
                            ?>
                        </li>
                    </ul>
                </span>
            </div>
        <?php } ?>
    </div>
    <div class="panel-body ">
        <div id="invoice-payment-statistics-flotchart" style="width: 100%; height: 300px;"></div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var invoiceStatisticsFlotchart = function () {
            var invoices =<?php echo $invoices; ?>,
                    payments = <?php echo $payments; ?>,
                    dataset = [
                        {
                            data: invoices,
                            color: "rgba(220,220,220, 1)",
                            lines: {
                                show: true,
                                fill: 0.2
                            },
                            points: {
                                show: false
                            },
                            shadowSize: 0,
                        },
                        {
                            label: "<?php echo lang('invoice'); ?>",
                            data: invoices,
                            color: "rgba(220,220,220, 1)",
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
                            curvedLines: {
                                apply: false
                            },
                            shadowSize: 0
                        },
                        {
                            data: payments,
                            color: "rgba(0, 179, 147, 1)",
                            lines: {
                                show: true,
                                fill: 0.2
                            },
                            points: {
                                show: false
                            },
                            shadowSize: 0,
                        },
                        {
                            label: "<?php echo lang('payment'); ?>",
                            data: payments,
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
                                apply: false,
                            }
                        }
                    ];

            $.plot($("#invoice-payment-statistics-flotchart"), dataset, {
                series: {
                    curvedLines: {
                        apply: true,
                        active: true,
                        monotonicFit: true
                    }
                },
                yaxis: {
                    min: 0
                },
                xaxis: {
                    ticks: [[1, "<?php echo lang('short_january'); ?>"], [2, "<?php echo lang('short_february'); ?>"], [3, "<?php echo lang('short_march'); ?>"], [4, "<?php echo lang('short_april'); ?>"], [5, "<?php echo lang('short_may'); ?>"], [6, "<?php echo lang('short_june'); ?>"], [7, "<?php echo lang('short_july'); ?>"], [8, "<?php echo lang('short_august'); ?>"], [9, "<?php echo lang('short_september'); ?>"], [10, "<?php echo lang('short_october'); ?>"], [11, "<?php echo lang('short_november'); ?>"], [12, "<?php echo lang('short_december'); ?>"]]
                },
                grid: {
                    color: "#bbb",
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: '#FFF'
                },
                tooltip: {
                    show: true,
                    content: function (x, y, z) {
                        if (x) {
                            return "%s: " + toCurrency(z, "<?php echo $currency_symbol; ?>");
                        } else {
                            return false;
                        }
                    },
                    defaultTheme: false
                }
            });
        };

        invoiceStatisticsFlotchart();
    });
</script>    

