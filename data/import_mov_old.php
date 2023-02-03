<?php

require("config.php");

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['ca']))
    $ca = json_decode($_POST['ca']);
else {
    echo '{"result": "Ошибка, нет данных для сохранения"}';
    exit(0);
}

$id = $ca->id;

die("$ca->feature\n");


$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}'; 
    exit(0);
}

mysqli_query($link, 'set charset utf8');
mysqli_query($link, 'set autocommit=1l');
mysqli_query($link, 'Start trancaction;');

$query = "INSERT INTO accident (id, dt, en, age, bdy, mine_name, area_name, place_name, level_name2, level_name, risk_name, process_name, desc_text, deleted)
VALUES ($ca->id, '$ca->dt', '$ca->en', $ca->age, $ca->bdy, '$ca->mine_name', '$ca->area_name', '$ca->place_name', '$ca->level_name2', '$ca->level_name', '$ca->risk_name', '$ca->process_name', '$ca->desc_text', 0)
ON DUPLICATE KEY UPDATE 
id=$ca->id, dt='$ca->dt', en='$ca->en', age=$ca->age, bdy=$ca->bdy, mine_name='$ca->mine_name', area_name='$ca->area_name', 
place_name='$ca->place_name', level_name2='$ca->level_name2', level_name='$ca->level_name', risk_name='$ca->risk_name', process_name='$ca->process_name', desc_text='$ca->desc_text', deleted=0
";

if (mysqli_query($link, $query) && mysqli_affected_rows($link)) {
    $place_id = $ca->id;
} else {
    mysqli_query($link, 'rollback;');
    die('{"result": "ERROR", "query": "'.$query.'"}');
}

$query = "INSERT INTO casualty.place(place_name) SELECT place_name FROM casualty.accident WHERE id = $id";
if (mysqli_query($link, $query)) {
  $place_id = mysqli_insert_id($link);
} else {
    mysqli_query($link, 'rollback;');
    die('{"result": "ERROR", "query": "'.$query.'"}');
}

$query = "INSERT INTO casualty.description(desc_text) SELECT desc_text FROM casualty.accident WHERE id = $id";
if (mysqli_query($link, $query)) {
  $desc_id = mysqli_insert_id($link);
} else {
    mysqli_query($link, 'rollback;');
    die('{"result": "ERROR", "query": "'.$query.'"}');
}

$query = "INSERT INTO diagnos(diagnos_text) VALUES('...')";
if (mysqli_query($link, $query)) {
  $diagnos_id = mysqli_insert_id($link);
} else {
    mysqli_query($link, 'rollback;');
    die('{"result": "ERROR", "query": "'.$query.'"}');
}

$query = "INSERT INTO casualty.ca(ida, dt, full_name, age, birth_year, age_group_id, mine_id, degree_id, area_id, desc_id, place_id, diagnos_id) 

SELECT 
    a.id ida
    , a.dt
    ,a.en
    ,a.age
    ,a.bdy
    ,
    CASE
        WHEN a.age >= 63 THEN 10
        WHEN a.age >= 58 THEN 9
        WHEN a.age >= 53 THEN 8
        WHEN a.age >= 48 THEN 7
        WHEN a.age >= 43 THEN 6
        WHEN a.age >= 38 THEN 5
        WHEN a.age >= 33 THEN 4
        WHEN a.age >= 28 THEN 3
        WHEN a.age >= 23 THEN 2
        WHEN a.age >= 18 THEN 1
        ELSE 0
    END age_group_id
    ,COALESCE(mine.id, 0) mine_id
    ,COALESCE(degree.id, 0) degree_id
    ,COALESCE(a2.area_id, 0) area_id
    ,$desc_id desc_id 
    ,$place_id place_id
    ,$diagnos_id diagnos_id
FROM casualty.accident a
LEFT JOIN casualty.mine ON mine.hse_mine_name = a.mine_name
LEFT JOIN casualty.degree ON degree.hse_degree_name = a.level_name
LEFT JOIN (
    SELECT DISTINCT casualty.accident.area_name, casualty.ca.area_id
    FROM casualty.accident 
    JOIN casualty.ca ON casualty.ca.ida = casualty.accident.id
    JOIN casualty.area ON casualty.area.id =  casualty.ca.area_id
    WHERE casualty.ca.area_id IS NOT NULL
) a2 ON a2.area_name = a.area_name
WHERE a.id =  $id

ON DUPLICATE KEY UPDATE
    dt = VALUES(dt), 
    mine_id = VALUES(mine_id),
    degree_id = VALUES(degree_id), 
    desc_id = VALUES(desc_id),
    place_id = VALUES(place_id),
    diagnos_id = VALUES(diagnos_id)";

$rez = mysqli_query($link, $query); 

if (mysqli_affected_rows($link) > 0) {
    $rez = mysqli_query($link, "UPDATE casualty.accident SET deleted = 1 WHERE id = $id"); 
    if (mysqli_affected_rows($link) > 0) {
        mysqli_query($link, 'commit;');
        echo '{"result": "OK", "move import message id": "'.$id.'"}'; 
    } else {
        mysqli_query($link, 'rollback;');
        echo '{"result": "ERROR", "move import message id": "'.$id.'"}'; 
    }
} else {
    mysqli_query($link, 'rollback;');
    echo '{"result": "ERROR", "move import message id": "'.$id.'"}'; 
}

mysqli_close($link);

// echo '{"result": "OK", "move import message id": "'.$id.'"}'; 
