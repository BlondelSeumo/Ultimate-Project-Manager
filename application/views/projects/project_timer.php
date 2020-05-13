<?php

if ($disable_timer) {
    $start_timer = js_anchor("<i class='fa fa fa-clock-o'></i> " . lang('start_timer'), array('title' => lang('start_timer'), "class" => "btn btn-info", "disabled" => "true", "data-action-url" => get_uri("projects/timer/" . $project_info->id . "/start"), "data-reload-on-success" => "1"));
} else {
    $start_timer = ajax_anchor(get_uri("projects/timer/" . $project_info->id . "/start"), "<i class='fa fa fa-clock-o'></i> " . lang('start_timer'), array("class" => "btn btn-info", "id" => "start_timer", "title" => lang('start_timer'), "data-reload-on-success" => "1"));
}

$stop_timer = modal_anchor(get_uri("projects/stop_timer_modal_form/" . $project_info->id), "<i class='fa fa fa-clock-o'></i> " . lang('stop_timer'), array("class" => "btn btn-danger", "title" => lang('stop_timer')));

if ($timer_status === "open") {
    echo $stop_timer;
} else {
    echo $start_timer;
}
?>