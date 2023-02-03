<?php

require("config.php");

if (isset($_GET['id']))
    $casualty_id = $_GET['id'];
else
    $casualty_id = 0;

if (isset($_GET['last_editor']))
    $last_editor = $_GET['last_editor'];
else
    $last_editor ='none';


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');

$query = "UPDATE ca SET deleted = 1, last_editor='$last_editor' WHERE id = $casualty_id";
$rez = mysqli_query($link, $query); 


echo '{"result": "OK", "caualty_id": "'.$casualty_id.'","query":"'.$query.'"}'; 

mysqli_close($link);

?>