<?php

require("config.php");



header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}';
    exit(0);
}
mysqli_query($link, 'set charset utf8');

$tables = ['mine', 'region', 'area', 'area_dir', 
    'place', 'mining_flag', 'age_group', 'prof',
    'degree', 'relation_flag', 'process', 'event', 'body_part'];
$db = [];

foreach ($tables as $table) {
    // print("$table\n");
    $query = 'SELECT id, ' . $table . '_name , ' . $table . '_name AS item_name FROM ' . $table . ' WHERE deleted = 0 ORDER BY ' . $table . '_name;';
    // print("$query\n");
    $rez = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($rez)) {
        $db[$table][] = $row;
    }
}

$query = 'SELECT id, full_name, birth_year FROM person ORDER BY full_name;';
$rez = mysqli_query($link, $query);
while ($row = mysqli_fetch_assoc($rez)) {
    $person[] = $row;
}

echo '{"result": "OK", "db": {';
foreach ($tables as $table) {
    echo '"' . $table . '":' . json_encode($db[$table], JSON_UNESCAPED_UNICODE) . ',';
}
echo '"person":' . json_encode($person, JSON_UNESCAPED_UNICODE);
echo '}}';

mysqli_close($link);
