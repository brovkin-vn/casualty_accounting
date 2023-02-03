<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
chdir('..');
$dir = getcwd();
$file_name = $dir."/".$_GET['file_name'];
if (file_exists($file_name)) {
    if (unlink($file_name))
        echo '{"result": "OK", "message": "Удален файл: ' . addslashes($file_name) . '"}';
    else
        echo '{"result": "ERROR", "message": "Ошибка уделеняи файла: ' . addslashes($file_name) . '"}';
 } else {
    echo '{"result": "ERROR", "message": "Ошибка уделеняи файла: ' . addslashes($file_name) . '"}';
 }

