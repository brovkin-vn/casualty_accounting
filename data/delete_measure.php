<?php

require("config.php");


if (isset($_GET['id']))
    $id = $_GET['id'];
else {
    echo '{"result": "ERROR", "message": "Ошибка, нет данных id"}';
    exit(0);
}

if (isset($_GET['last_editor']))
    $last_editor = $_GET['last_editor'];
else {
    echo '{"result": "ERROR", "message": "Ошибка, нет данных last_editor"}';
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
    echo '{"result": "ERROR", "message": "Ошибка соединения c БД"}'; 
    exit(0);
}

mysqli_query($link, 'set charset utf8');

$query = 'UPDATE measure SET last_editor="'.$last_editor.'" WHERE id = ' . $id . ';';
$rez = mysqli_query($link, $query);
if(!$rez) {
    echo '{"result": "ERROR", "message": "'.mysqli_error($link).'", "query": "'.$query.'"}';
    exit(0);
}

$query = 'DELETE FROM measure WHERE id = ' . $id . ';';
$rez = mysqli_query($link, $query);
if($rez) {
    echo '{"result": "OK", "message": "delete measure id: '.$id.'"}';
} else {
    echo '{"result": "ERROR", "message": "'.mysqli_error($link).'", "query": "'.$query.'"}';
}

