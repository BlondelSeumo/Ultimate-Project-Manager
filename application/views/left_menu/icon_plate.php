<div class="icon-palet">
    <?php
    $selected_icon = $model_info->icon ? $model_info->icon : "bookmark";
    $icons = array(
        "bookmark",
        "area-chart",
        "line-chart",
        "pie-chart",
        "bank",
        "cab",
        "cloud",
        "folder",
        "sticky-note",
        "book",
        "file",
        "file-text",
        "envelope",
        "fax",
        "rss",
        "shopping-cart",
        "tag",
        "upload",
        "bar-chart",
        "coffee",
        "life-ring",
        "paper-plane",
        "recycle",
        "street-view",
        "video-camera",
        "globe",
        "heart",
        "home",
        "shopping-basket",
        "suitcase",
        "thumbs-up",
        "gear",
        "check-square",
        "ambulance",
        "link",
        "adjust",
        "bars",
        "beer",
        "bomb",
        "camera",
        "cart-plus",
        "comment",
        "crosshairs",
        "dashboard",
        "edit",
        "external-link-square",
        "fire",
        "flag",
        "futbol-o",
        "hashtag",
        "hotel",
        "key",
        "lock",
        "map",
        "paint-brush",
        "print",
        "send",
        "battery-4",
        "bluetooth",
        "bullhorn",
        "calculator",
        "ship",
        "shopping-bag",
        "warning",
        "tint",
        "truck",
        "user-secret",
        "bicycle",
        "train",
        "credit-card",
        "cc-mastercard",
        "paypal"
    );

    foreach ($icons as $icon) {
        $active_class = "";
        if ($selected_icon === $icon) {
            $active_class = "active";
        }
        echo "<span class='icon-tag clickable inline-block " . $active_class . "' data-icon='" . $icon . "'><i class='fa fa-$icon'></i></span>";
    }
    ?> 
    <input id="icon" type="hidden" name="icon" value="<?php echo $selected_icon; ?>" />
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $(".icon-palet span").click(function () {
            $(".icon-palet").find(".active").removeClass("active");
            $(this).addClass("active");
            $("#icon").val($(this).attr("data-icon"));
        });

    });
</script>