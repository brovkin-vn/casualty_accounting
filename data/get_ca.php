<?php

require("config.php");

if (isset($_GET['id']))
    $casualty_id = $_GET['id'];
else
    $casualty_id = 0;

if (isset($_GET['offset']))
    $offset = $_GET['offset'];
else
    $offset = 0;


header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}';
    exit(0);
}
mysqli_query($link, 'set charset utf8');

if ($offset != 0) {
    $delta = abs($offset);
    $query = "WITH ttt AS (
    SELECT id
    ,LAG(id, $delta) OVER (ORDER BY id) prev_id
    ,LEAD(id, $delta) OVER (ORDER BY id) next_id
    FROM casualty.ca where 1=1
    AND deleted = 0
    AND dt > '2005-01-01' )
    SELECT COALESCE(prev_id, id) prev_id, id, COALESCE(next_id, id) next_id FROM ttt WHERE id = $casualty_id";

    $rez = mysqli_query($link, $query);
    if ($rez) {
        while ($row = mysqli_fetch_assoc($rez)) {
            if ($offset > 0)
                $casualty_id = $row['next_id'];
            else
                $casualty_id = $row['prev_id'];
        }
    }
}

$query = "SELECT ca.id, ca.dt, mine_id, region_id, area_id, area_dir_id
, place_id, mining_flag_id, ca.age, full_name, birth_year, age_group_id
, prof_id, desc_id, diagnos_id, degree_id, group_flag
, end_flag, event_id, process_id, prod_flag
, relation_flag_id, measure_id, ca.deleted, ca.at_request_flag
, de.desc_text, di.diagnos_text, di.body_part_id
, coalesce(ac.mine_name, 'noflash') flash_mine_name, ac.area_name flash_area_name, ac.process_name flash_process_name, ac.risk_name flash_risk_name
FROM ca 
JOIN description de ON de.id = ca.desc_id
JOIN diagnos di ON di.id = ca.diagnos_id
LEFT JOIN accident ac ON ac.id = ca.ida
WHERE ca.id = $casualty_id;";
$rez = mysqli_query($link, $query);

$ca = [];
if ($rez) {
    while ($row = mysqli_fetch_assoc($rez)) {
        $ca[] = $row;
    }
}

if (count($ca) == 0) {
    while ($field = mysqli_fetch_field($rez)) {
        $fields[$field->name] = 0;
    }
    $ca  = [$fields];
}

$ca['file_act'] = [];
$ca['file_other'] = [];

chdir('..');
$uploaddir = getcwd();
$uploaddir = $uploaddir . "\\files\\$casualty_id\\";

if (file_exists($uploaddir . "act\\"))
    $ca['file_act'] = array_diff(scandir($uploaddir . "act\\"), array('.', '..'));
if (file_exists($uploaddir . "other\\"))
    $ca['file_other'] = array_diff(scandir($uploaddir . "other\\"), array('.', '..'));



// echo $query;
echo '{"result": "OK", "casualty_id": "' . $casualty_id . '","ca":' . json_encode($ca, JSON_UNESCAPED_UNICODE) . '}';

mysqli_close($link);
