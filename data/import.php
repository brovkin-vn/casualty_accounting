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


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');


$query = 'SELECT id, dt, en, age, mine_name, area_name, place_name, level_name, risk_name, process_name, desc_text
FROM casualty.accident 
WHERE deleted = 1
ORDER BY 2';


$rez = mysqli_query($link, $query); 
$casualties = [];
while($row = mysqli_fetch_assoc($rez)) { 
    //  print_r($row);
$casualties[] = $row; 
}
// echo $query;
echo '{"result": "OK", "dt1": "'.$dt1.'","dt2": "'.$dt2.'","casualties":'.json_encode($casualties, JSON_UNESCAPED_UNICODE).'}'; 

mysqli_close($link);

?>