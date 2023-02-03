<?php

require("config.php");

if (isset($_GET['id']))
    $id = $_GET['id'];
else
    $id = -1;


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');


$query = 'UPDATE casualty.accident SET deleted = 1 WHERE id = '.$id;
$rez = mysqli_query($link, $query); 

if (mysqli_affected_rows($link) > 0)
    echo '{"result": "OK", "delete import message id": "'.$id.'"}'; 
else
    echo '{"result": "ERROR", "delete import message id": "'.$id.'"}'; 


mysqli_close($link);

?>