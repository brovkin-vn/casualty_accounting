<?php

require("config.php");

if (isset($_GET['dt1']))
    $dt1 = $_GET['dt1'];
else
    $dt1 = date("Y-m-d", strtotime("first day of this month"));

if (isset($_GET['dt2']))
    $dt2 = $_GET['dt2'];
else
    $dt2 = date("Y-m-d", strtotime("0 day"));

if (isset($_GET['mine_id']))
    $mine_id = $_GET['mine_id'];
else
    $mine_id = 0;

if (isset($_GET['find_flag']))
    $find_flag = $_GET['find_flag'];
else
    $find_flag = 0;

if (isset($_GET['find_text']))
    $find_text = $_GET['find_text'];
else
    $find_text = "*";


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');

$query = 'SELECT id mine_id, mine_name FROM casualty.mine ORDER BY mine_name;';
$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$mines[] = $row; 
}



$query = 'SELECT ca.id, dt, mine_name mine, area_name area, prof_name prof, full_name FROM ca
LEFT JOIN mine m ON m.id = ca.mine_id
LEFT JOIN area a ON a.id = ca.area_id
LEFT JOIN prof p ON p.id = ca.prof_id ';
if ($find_flag) $query = $query . '  JOIN description d ON d.id = ca.desc_id ';
$query = $query . ' WHERE ca.deleted=0 AND ca.dt BETWEEN "' . $dt1 . '" AND "' . $dt2 . ' 23:59:59" ';
if ($mine_id) $query = $query . ' AND ca.mine_id = ' . $mine_id . ' ';
if ($find_flag) $query = $query . ' AND MATCH(d.desc_text) AGAINST("' . $find_text .'" IN BOOLEAN MODE) ';

$query = $query . ' ORDER BY dt DESC';
//echo $query;

$rez = mysqli_query($link, $query); 
$checks = [];
while($row = mysqli_fetch_assoc($rez)) { 
$checks[] = $row; 
}
// echo $query;
echo '{"result": "OK", "dt1": "'.$dt1.'","dt2": "'.$dt2.'","mines":'.json_encode($mines, JSON_UNESCAPED_UNICODE).' 
,"casualties":'.json_encode($checks, JSON_UNESCAPED_UNICODE).'}'; 
// echo '{"result": "OK"}'; 
mysqli_close($link);

?>