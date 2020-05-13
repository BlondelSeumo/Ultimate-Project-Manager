<div class="panel panel-orange">
    <a href="<?php echo get_uri('invoices/index'); ?>" class="white-link">
        <div class="panel-body">
            <div class="widget-icon">
                <i class="fa fa-file-text"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $draft_invoices; ?></h1>
                <?php echo lang("draft_invoices"); ?>
            </div>
        </div>
    </a>
</div>