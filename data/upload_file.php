<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$id = $_GET['id'];
$dir = $_GET['dir'];
chdir('..');
$uploaddir = getcwd();
$uploaddir = $uploaddir."\\files\\$id\\";
if (!file_exists($uploaddir)) mkdir($uploaddir);
$uploaddir = $uploaddir."$dir\\";
if (!file_exists($uploaddir)) mkdir($uploaddir);

$file_name = $_FILES['file']['name'];
$file_name = str_replace(" ", "_", $file_name);
// $file_name_win = mb_convert_encoding($file_name,"Windows-1251","UTF-8");

$actual_name = pathinfo($file_name, PATHINFO_FILENAME);
$original_name = $actual_name;

$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$i = 1;
while (file_exists($uploaddir . $actual_name . "." . $extension)) {
    $actual_name = (string)$original_name . "(" . $i . ")";
    $file_name = $actual_name . "." . $extension;
    $i++;
}

$uploadfile = $uploaddir . basename($file_name);
// $uploadfile_win = $uploaddir.basename($file_name_win);
try {
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
        echo '{"result": "OK", "message": "Файл загружен под именем: ' . addslashes($uploadfile) . '"}';
    } else {
        echo '{"result": "ERROR", "message": "Ошибка файловой загрузки", "uploaddir": "' . addslashes($uploaddir) . '"}';
    }
} catch (Exception $e) {
    echo '{"result": "ERROR", "message": "' . $e->getMessage() . '"}';
}
