<?php

require("config.php");

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['mine_check'])) $mine_check = $_GET['mine_check']; else $mine_check = 0;
if (isset($_GET['dt1_check']))  $dt1_check  = $_GET['dt1_check'];  else $dt1_check = 0;
if (isset($_GET['mine_id']))    $mine_id    = $_GET['mine_id'];    else $mine_id = 0;
if (isset($_GET['dt1']))        $dt1        = $_GET['dt1'];        else $dt1 = 0;



$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');

// $query = 'SELECT id prof_id, prof_name FROM casualty.prof WHERE id > 0 AND NOT prof_name like "Все%" ORDER BY prof_name;';
$query = 'SELECT DISTINCT p.id prof_id, prof_name FROM casualty.prof p
JOIN ca ON ca.prof_id = p.id
WHERE p.id > 0 
AND NOT prof_name like "Все%"';
if ($mine_check)
    $query = $query.' AND ca.mine_id = '.$mine_id;
if ($dt1_check)
    $query = $query.' AND ca.dt > "'.$dt1.'"';
$query = $query.' ORDER BY prof_name';

$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$profs[] = $row; 
}

$query = 'SELECT id prof_id, prof_name FROM casualty.prof WHERE id > 0 AND prof_name like "Все%" ORDER BY prof_name;';


$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$profs_all[] = $row; 
}

$query = 'SELECT id, prof_sap_name FROM casualty.prof_sap ORDER BY prof_sap_name;';
$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$profs_sap[] = $row; 
}

$query = 'SELECT id, mine_name FROM casualty.mine ORDER BY mine_name;';
$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$mines[] = $row; 
}

// echo $query;
echo '{"result": "OK", "profs":'.json_encode($profs, JSON_UNESCAPED_UNICODE)
.', "profs_all":'.json_encode($profs_all, JSON_UNESCAPED_UNICODE)
.', "mines":'.json_encode($mines, JSON_UNESCAPED_UNICODE)
.', "profs_sap":'.json_encode($profs_sap, JSON_UNESCAPED_UNICODE).'}'; 

mysqli_close($link);
