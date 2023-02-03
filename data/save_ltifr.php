<?php

require("config.php");

header('Content-Type: application/json; charset=utf-8');


//print_r($_POST);

if (isset($_POST['data']))
    $data = json_decode($_POST['data']);
else {
    echo '{"result": "Ошибка, нет данных для сохранения"}';
    exit(0);
}

try {

    $link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

    if (!$link) {
        echo '{"result": "Ошибка соединения c БД"}';
        exit(0);
    }

    mysqli_query($link, 'set charset utf8');
    mysqli_query($link, 'set autocommit=1l');
    mysqli_query($link, 'Start trancaction;');
    
    $affected_rows = 0;

    foreach ($data as $r) {
        $query = "INSERT INTO casualty.ltifr(mine_id, dt, ФЧЧ, ЧО, СЧЧ, ДВПДР, ДННСП, ДНПЗ, ДНОЗ, ДННСБ) 
    VALUES($r->mine_id, '$r->dt', $r->f1, $r->f2, $r->f3, $r->f4, $r->f5, $r->f6, $r->f7, $r->f8) 
    ON DUPLICATE KEY UPDATE
    ФЧЧ = $r->f1, ЧО = $r->f2, СЧЧ = $r->f3, ДВПДР = $r->f4, ДННСП = $r->f5, ДНПЗ = $r->f6, ДНОЗ = $r->f7, ДННСБ = $r->f8;\n";
        $rez = mysqli_query($link, $query);
        $affected_rows = $affected_rows + mysqli_affected_rows($link);
        // print_r($query);
    }

    mysqli_query($link, 'commit;');

    echo '{"result": "OK", "affected_rows":' . $affected_rows . '}';
    
    mysqli_close($link);
} catch (Exception $e) {
    mysqli_query($link, 'rollback;');
    echo '{"result": "ERROR", "exception": "' . $e->getMessage() . '"}';
}
