<?php

require("config.php");

if (isset($_GET['dt']))
    $dt = $_GET['dt'];
else{
    echo '{"result": "Ошибка, нет данных dt для сохранения"}';
    exit(0);
}
 



header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}';
    exit(0);
}
mysqli_query($link, 'set charset utf8');

$query = "SELECT COALESCE(t.id, -1) id, m.id mine_id, m.mine_name mine_name, COALESCE(dt, '$dt') dt, 
COALESCE(ФЧЧ, 0) f1, COALESCE(ЧО, 0) f2, COALESCE(СЧЧ, 0) f3, COALESCE(ДВПДР, 0) f4, 
COALESCE(ДННСП, 0) f5, COALESCE(ДНПЗ, 0) f6, COALESCE(ДНОЗ, 0) f7, COALESCE(ДННСБ, 0) f8 
FROM casualty.mine m
LEFT JOIN casualty.ltifr t on t.mine_id = m.id
WHERE m.actual = 1 and (dt is null or dt='$dt')
ORDER BY mine_name";

$query="WITH 
m AS (SELECT * FROM casualty.mine WHERE actual = 1),
t AS (SELECT * FROM casualty.ltifr WHERE dt = '$dt')
SELECT  
COALESCE(t.id, -1) id, m.id mine_id, m.mine_name mine_name, COALESCE(dt, '$dt') dt, 
COALESCE(ФЧЧ, 0) f1, COALESCE(ЧО, 0) f2, COALESCE(СЧЧ, 0) f3, COALESCE(ДВПДР, 0) f4, 
COALESCE(ДННСП, 0) f5, COALESCE(ДНПЗ, 0) f6, COALESCE(ДНОЗ, 0) f7, COALESCE(ДННСБ, 0) f8 
FROM m
LEFT JOIN t on t.mine_id = m.id";


 
 

// print_r($query) ;

$rez = mysqli_query($link, $query);
while ($row = mysqli_fetch_assoc($rez)) {
    $ltifr[] = $row;
}

echo '{"result": "OK", "ltifr":' . json_encode($ltifr, JSON_UNESCAPED_UNICODE) . '}';

mysqli_close($link);
