<div id="page-content" class="clearfix">
    <?php
    load_css(array(
        "assets/css/invoice.css",
    ));
    ?>

    <div class="invoice-preview print-invoice">
        <div class="invoice-preview-container bg-white mt15">
            <div class="col-md-12">
                <div class="ribbon"><?php echo $invoice_status_label; ?></div>
            </div>

            <?php echo $invoice_preview; ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("html, body").addClass("dt-print-view");
    });
</script>