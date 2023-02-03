<?php

require("config.php");


if (isset($_POST['data']))
    $data = json_decode($_POST['data']);
else {
    echo '{"result": "Ошибка, нет данных для сохранения"}';
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}

mysqli_query($link, 'set charset utf8');

if ($data->measure_id >= 0) {
    $query = 'UPDATE measure  SET measure_text = "' . addslashes($data->measure_text) . '", last_editor="'.$data->last_editor.'" WHERE id  = ' . $data->measure_id . ';';
    $rez = mysqli_query($link, $query);
    if($rez) {
        $rez = mysqli_query($link, 'SELECT last_insert_id()'); 
        if($row = mysqli_fetch_row($rez)) $measure_id = $row[0];
        echo '{"result": "OK", "measure_id": "'.$data->measure_id.'"}';
    } else {
        echo '{"result": "ERROR", "message": "'.mysqli_error($link).'", "query": "'.$query.'"}';
    }

} else {
    $query = 'INSERT INTO measure(process_id, event_id, measure_text, last_editor) VALUES('
        .$data->process_id.', '.$data->event_id.', "'.addslashes($data->measure_text).'", "'.$data->last_editor.'");';
    $rez = mysqli_query($link, $query); 
    if($rez) {
        $rez = mysqli_query($link, 'SELECT last_insert_id()'); 
        if($row = mysqli_fetch_row($rez)) $measure_id = $row[0]; 
        echo '{"result": "OK", "measure_id": "'.$measure_id.'"}';
    } else {
        echo '{"result": "ERROR", "message": "'.mysqli_error($link).'", "query": "'.$query.'"}';
    }
}


// echo '{"result": "OK", "measure_id": "'.$measure_id.'", "query": "' . $query . '" }';
// echo '{"result": "OK", "measure_id": "'.$measure_id.'"}';

mysqli_close($link);
