<?php

$sources = array(array("id" => "", "text" => "- " . lang("source") . " -"));
foreach ($lead_sources as $source) {
    $sources[] = array("id" => $source->id, "text" => $source->title);
}

echo json_encode($sources);
?>