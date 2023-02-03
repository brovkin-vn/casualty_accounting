<?php

require("config.php");


if (isset($_GET['full_name']))
    $full_name = $_GET['full_name'];
else {
    echo '{"result": "Ошибка, нет данных для сохранения"}';
    exit(0);
}

if (isset($_GET['birth_year']))
    $birth_year = $_GET['birth_year'];
else {
    echo '{"result": "Ошибка, нет данных для сохранения"}';
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);


if (!$link) {
    echo '{"result": "Ошибка соединения c БД"}';
    exit(0);
}
mysqli_query($link, 'set charset utf8');
mysqli_query($link, 'set autocommit=1l');
mysqli_query($link, 'Start trancaction;');

try {
    $person_id = -1;

    $query = "SELECT id FROM person WHERE UPPER(full_name) = UPPER('$full_name') AND birth_year=$birth_year;";
    $rez = mysqli_query($link, $query);

    if ($row = mysqli_fetch_row($rez)) {
        $person_id = $row[0];
    } else  {
        $query = "INSERT INTO person(full_name, birth_year) VALUES('$full_name', $birth_year);";
        $rez = mysqli_query($link, $query);
        $rez = mysqli_query($link, 'SELECT last_insert_id();');
        if ($row = mysqli_fetch_row($rez)) {
            $person_id = $row[0];
        }
    }

    mysqli_query($link, 'commit;');
    echo '{"result": "OK", "person_id": "' . $person_id . '"}';
} catch (Exception $e) {
    mysqli_query($link, 'rollback;');
    echo '{"result": "ERROR", "exception": "' . $e->getMessage() . '"}';
}




mysqli_close($link);
