<div class="panel panel-default  p15 no-border m0">
    <span class="mr10"><?php echo $invoice_status_label; ?></span>
    
    <?php
    $invoice_labels = "";
    if ($invoice_info->labels) {
        $labels = explode(",", $invoice_info->labels);
        foreach ($labels as $label) {
            $invoice_labels .= "<span class='mt0 label label-info large mr10'  title='$label'>" . $label . "</span>";
        }
    }
    echo "<span>" . $invoice_labels . " </span>";
    ?>

    <?php if ($invoice_info->project_id) { ?>
        <span class="ml15"><?php echo lang("project") . ": " . anchor(get_uri("projects/view/" . $invoice_info->project_id), $invoice_info->project_title); ?></span>
    <?php } ?>

    <span class="ml15"><?php
        echo lang("client") . ": ";
        echo (anchor(get_uri("clients/view/" . $invoice_info->client_id), $invoice_info->company_name));
        ?>
    </span> 

    <span class="ml15"><?php
        echo lang("last_email_sent") . ": ";
        echo (is_date_exists($invoice_info->last_email_sent_date)) ? format_to_date($invoice_info->last_email_sent_date, FALSE) : lang("never");
        ?>
    </span>
    <?php if ($invoice_info->recurring_invoice_id) { ?>
        <span class="ml15">
            <?php
            echo lang("created_from") . ": ";
            echo anchor(get_uri("invoices/view/" . $invoice_info->recurring_invoice_id), get_invoice_id($invoice_info->recurring_invoice_id));
            ?>
        </span>
    <?php } ?>

    <?php if ($invoice_info->cancelled_at) { ?>
        <span class="ml15"><?php echo lang("cancelled_at") . ": " . format_to_relative_time($invoice_info->cancelled_at); ?></span>
    <?php } ?>

    <?php if ($invoice_info->cancelled_by) { ?>
        <span class="ml15"><?php echo lang("cancelled_by") . ": " . get_team_member_profile_link($invoice_info->cancelled_by, $invoice_info->cancelled_by_user); ?></span>
    <?php } ?>

</div>