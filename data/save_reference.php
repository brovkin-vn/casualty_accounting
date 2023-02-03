<?php

require("config.php");


if (isset($_GET['ref_mode']))
    $ref_mode = $_GET['ref_mode'];
else {
    echo '{"result": "Ошибка, нет данных ref_mode для сохранения"}';
    exit(0);
}

if (isset($_GET['ref_name']))
    $ref_name = $_GET['ref_name'];
else {
    echo '{"result": "Ошибка, нет данных ref_name для сохранения"}';
    exit(0);
}

if (isset($_GET['ref_item_text']))
    $ref_item_text = $_GET['ref_item_text'];
else {
    echo '{"result": "Ошибка, нет данных ref_item_text для сохранения"}';
    exit(0);
}

if ($ref_mode >= 1) {
    if (isset($_GET['ref_item_id']))
        $ref_item_id = $_GET['ref_item_id'];
    else {
        echo '{"result": "Ошибка, нет данных ref_item_id для сохранения"}';
        exit(0);
    }
}
if ($ref_mode == 2) {
    if (isset($_GET['ref_item_text2']))
        $ref_item_text2 = $_GET['ref_item_text'];
    else {
        echo '{"result": "Ошибка, нет данных ref_item_text2 для сохранения"}';
        exit(0);
    }
    if (isset($_GET['ref_item_id2']))
        $ref_item_id2 = $_GET['ref_item_id2'];
    else {
        echo '{"result": "Ошибка, нет данных ref_item_id2 для сохранения"}';
        exit(0);
    }
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
    $ref_id = -1;
    $table = $ref_name;
    $field = $ref_name . '_name';
    $field_id_name = $ref_name . '_id';

    if ($ref_mode == 0) {
        $query = "SELECT id FROM $table WHERE UPPER($field) = UPPER('$ref_item_text')";
        $rez = mysqli_query($link, $query);

        if ($row = mysqli_fetch_row($rez)) {
            $ref_id = $row[0];
        } else {
            $query = "INSERT INTO $table($field) VALUES('$ref_item_text');";
            $rez = mysqli_query($link, $query);
            $rez = mysqli_query($link, 'SELECT last_insert_id();');
            if ($row = mysqli_fetch_row($rez)) {
                $ref_id = $row[0];
            }
        }
    }

    if ($ref_mode == 1) {

        $query = "UPDATE casualty.$table SET $field='$ref_item_text' WHERE id = $ref_item_id";
        $rez = mysqli_query($link, $query);

        if (mysqli_affected_rows($link) >= 0) {
            $ref_id = $ref_item_id;
        } else {
            mysqli_query($link, 'rollback;');
            echo '{"result": "ERROR", "query", "' . $query . '"}';
            exit(0);
        }
    }


    if ($ref_mode == 2) {
        
        $query = "UPDATE casualty.ca SET $field_id_name =$ref_item_id WHERE $field_id_name = $ref_item_id2";
        $rez = mysqli_query($link, $query);
        if (mysqli_affected_rows($link) >= 0) {
            $ref_id = $ref_item_id2;
        } else {
            mysqli_query($link, 'rollback;');
            echo '{"result": "ERROR", "query", "' . $query . '"}';
            exit(0);
        }

        $query = "UPDATE casualty.$table SET deleted=1 WHERE id = $ref_item_id2";
        $rez = mysqli_query($link, $query);
        if (mysqli_affected_rows($link) >= 0) {
            $ref_id = $ref_item_id2;
        } else {
            mysqli_query($link, 'rollback;');
            echo '{"result": "ERROR", "query", "' . $query . '"}';
            exit(0);
        }
    }



    mysqli_query($link, 'commit;');
    echo '{"result": "OK", "ref_id": "' . $ref_id . '"}';
} catch (Exception $e) {
    mysqli_query($link, 'rollback;');
    echo '{"result": "ERROR", "exception": "' . $e->getMessage() . '"}';
}




mysqli_close($link);
