<?php

require("config.php");

if (isset($_GET['process_id']))
    $process_id = $_GET['process_id'];
else
    $process_id = -1;


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}
mysqli_query($link, 'set charset utf8');

$query = 'SELECT id process_id, process_name FROM casualty.process ORDER BY process_name;';
$rez = mysqli_query($link, $query); 
while($row = mysqli_fetch_assoc($rez)) { 
$process[] = $row; 
}


if ($process_id>=0) {
$query = 'SELECT distinct coalesce(m.id, -1) measure_id, ca.process_id, ca.event_id, m.measure_text, ps.process_name, ev.event_name FROM ca
JOIN process ps ON ps.id = ca.process_id
JOIN event ev ON ev.id = ca.event_id
LEFT JOIN measure m ON (m.process_id = ca.process_id) AND (m.event_id = ca.event_id)
WHERE ca.process_id = ' . $process_id . ' ORDER BY 1,2';
} else {
$query = 'SELECT distinct coalesce(m.id, -1) measure_id, ca.process_id, ca.event_id, m.measure_text, ps.process_name, ev.event_name FROM ca
JOIN process ps ON ps.id = ca.process_id
JOIN event ev ON ev.id = ca.event_id
LEFT JOIN measure m ON (m.process_id = ca.process_id) AND (m.event_id = ca.event_id)
ORDER BY 1,2';
}


$rez = mysqli_query($link, $query); 
$measures = [];
while($row = mysqli_fetch_assoc($rez)) { 
$measures[] = $row; 
}

echo '{"result": "OK", "process":'.json_encode($process, JSON_UNESCAPED_UNICODE).' 
    ,"measures":'.json_encode($measures, JSON_UNESCAPED_UNICODE).'}'; 
    // echo '{"result": "OK"}'; 
mysqli_close($link);
    
?>