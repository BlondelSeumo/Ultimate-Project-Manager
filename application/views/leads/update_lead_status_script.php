<?php
$statuses = array();
foreach ($lead_statuses as $status) {
    $statuses[] = array("id" => $status->id, "text" => $status->title);
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '[data-act=update-lead-status]', function () {
            $(this).editable({
                type: "select2",
                pk: 1,
                name: 'status',
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                value: $(this).attr('data-value'),
                url: '<?php echo_uri("leads/save_lead_status") ?>/' + $(this).attr('data-id'),
                showbuttons: false,
                source: <?php echo json_encode($statuses) ?>,
                success: function (response, newValue) {
                    if (response.success) {
                        $("#lead-table").appTable({newData: response.data, dataId: response.id});
                    }
                }
            });
            $(this).editable("show");
        });
    });
</script>