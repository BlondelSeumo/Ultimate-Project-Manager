<?php

//This helpers provided only for developers
//Don't include this in production/live project
//
//read file
function read_file_by_curl($path) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_POST, 1);

    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

//preapre app.all.css
function write_css($files) {
    merge_file($files, "assets/css/app.all.css");
}

//preapre app.all.js
function write_js($files) {
    merge_file($files, "assets/js/app.all.js");
}

//merge all files into one
function merge_file($files, $file_name) {
    $txt ="";
    foreach ($files as $file) {
        $txt .= file_get_contents(base_url($file));
    }

    file_put_contents($file_name, $txt);
}
