<?php

require("config.php");


// header('Content-Type: application/json; charset=utf-8');

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


$query = 'SELECT ca.id, ca.dt, m.mine_name, r.region_name
, a.area_name, ad.area_dir_name, p.place_name
, mf.mining_flag_name, ca.full_name, ca.age
, ag.age_group_name, pr.prof_name,d.desc_text
, di.diagnos_text, de.degree_name, ca.group_flag
, ca.pass_time, rf.relation_flag_name, ev.event_name, ps.process_name
FROM casualty.ca ca
LEFT JOIN casualty.mine m ON m.id = ca.mine_id
LEFT JOIN casualty.region r ON r.id = ca.region_id
LEFT JOIN casualty.area a ON a.id = ca.area_id
LEFT JOIN casualty.area_dir ad ON ad.id = ca.area_dir_id
LEFT JOIN casualty.place p ON p.id = ca.place_id
LEFT JOIN casualty.mining_flag mf ON mf.id = ca.mining_flag_id
LEFT JOIN casualty.age_group ag ON ag.id = ca.age_group_id
LEFT JOIN casualty.prof pr ON pr.id = ca.prof_id
LEFT JOIN casualty.description d ON d.id = ca.desc_id
LEFT JOIN casualty.diagnos di ON di.id = ca.diagnos_id
LEFT JOIN casualty.degree de ON de.id = ca.degree_id
LEFT JOIN casualty.event ev ON ev.id = ca.event_id
LEFT JOIN casualty.process ps ON ps.id = ca.process_id
LEFT JOIN casualty.relation_flag rf ON rf.id = ca.relation_flag_id
WHERE ca.deleted=0 AND ca.dt BETWEEN "' . $dt1 . '" AND "' . $dt2 . ' 23:59:59"';
if ($mine_id) $query = $query . ' AND ca.mine_id = ' . $mine_id;



$rez = mysqli_query($link, $query); 
$ca = [];
while($row = mysqli_fetch_assoc($rez)) { 
$ca[] = $row; 
}
// echo $query;
// echo '{"result": "OK", "dt1": "'.$dt1.'","dt2": "'.$dt2.'","casualties":'.json_encode($ca, JSON_UNESCAPED_UNICODE).'}'; 
// echo '{"result": "OK"}'; 
mysqli_close($link);

?>