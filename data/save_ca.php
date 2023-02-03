<?php

require("config.php");


if (isset($_POST['ca']))
    $ca = json_decode($_POST['ca']);
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
    $casualty_id = $ca->id;
    if ($ca->id >= 0) {
        $query = 'UPDATE description  SET desc_text = "' . $ca->desc_text . '", last_editor = "'.$ca->last_editor.'" WHERE id  = ' . $ca->desc_id . ';';
        $rez = mysqli_query($link, $query);
        $query = 'UPDATE diagnos  SET diagnos_text = "' . $ca->diagnos_text . '",   body_part_id = '.$ca->body_part_id.', last_editor = "'.$ca->last_editor.'" WHERE id  = ' . $ca->diagnos_id . ';';
        $rez = mysqli_query($link, $query);
        
        $desc_id = $ca->desc_id;
        $diagnos_id = $ca->diagnos_id;

        $query = 'UPDATE ca  SET dt = "' . $ca->dt . '"'
        .',mine_id = '.$ca->mine_id;
        if (is_numeric($ca->region_id)) $query = $query.',region_id = '.$ca->region_id;
        if (is_numeric($ca->area_id)) $query = $query.',area_id = '.$ca->area_id;
        if (is_numeric($ca->area_dir_id)) $query = $query.',area_dir_id = '.$ca->area_dir_id;
        if (is_numeric($ca->place_id)) $query = $query.',place_id = '.$ca->place_id;
        if (is_numeric($ca->mining_flag_id)) $query = $query.',mining_flag_id = '.$ca->mining_flag_id;
        if ($ca->full_name) $query = $query.',full_name = "'.$ca->full_name . '"';
        if (is_numeric($ca->birth_year)) $query = $query.',birth_year = '.$ca->birth_year;
        if (is_numeric($ca->age)) $query = $query.',age = '.$ca->age;
        if (is_numeric($ca->age_group_id)) $query = $query.',age_group_id = '.$ca->age_group_id;
        if (is_numeric($ca->prof_id)) $query = $query.',prof_id = '.$ca->prof_id;
        if (is_numeric($ca->degree_id)) $query = $query.',degree_id = '.$ca->degree_id;
        if (is_numeric($ca->group_flag)) $query = $query.',group_flag = '.$ca->group_flag;
        if (is_numeric($ca->end_flag)) $query = $query.',end_flag = '.$ca->end_flag;
        if (is_numeric($ca->at_request_flag)) $query = $query.',at_request_flag = '.$ca->at_request_flag;
        if (is_numeric($ca->event_id)) $query = $query.',event_id = '.$ca->event_id;
        if (is_numeric($ca->process_id)) $query = $query.',process_id = '.$ca->process_id;
        if (is_numeric($ca->prod_flag)) $query = $query.',prod_flag = '.$ca->prod_flag;
        if (is_numeric($ca->relation_flag_id)) $query = $query.',relation_flag_id = '.$ca->relation_flag_id;
        if ($ca->last_editor) $query = $query.',last_editor = "'.$ca->last_editor . '"';
        $query = $query.' WHERE id  = ' . $casualty_id . ';';
        $rez = mysqli_query($link, $query);
        if ($rez) {
            $rez = mysqli_query($link, 'SELECT last_insert_id();');
            if ($row = mysqli_fetch_row($rez)){ $ca->id = $row[0];}
            mysqli_query($link, 'commit;');
            echo '{"result": "OK", "casualty_id": "'.$casualty_id.'", "desc_id": "'.$desc_id.'", "diagnos_id": "'.$diagnos_id.'"}';
        } else {
            mysqli_query($link, 'rollback;');
            echo '{"result": "ERROR", "exception": "'.mysqli_error($link).'"}';
        }
    } else {
        $query = "INSERT INTO description(desc_text,last_editor) VALUES('$ca->desc_text','$ca->last_editor');";
        $rez = mysqli_query($link, $query);
        $rez = mysqli_query($link, 'SELECT last_insert_id();');
        if ($row = mysqli_fetch_row($rez)) $desc_id = $row[0];

        $query = "INSERT INTO diagnos(diagnos_text, last_editor, body_part_id) VALUES('$ca->diagnos_text', '$ca->last_editor', $ca->body_part_id);";
        // echo $query;
        $rez = mysqli_query($link, $query);
        $rez = mysqli_query($link, 'SELECT last_insert_id();');
        if ($row = mysqli_fetch_row($rez)) $diagnos_id = $row[0];

        $query = "INSERT INTO ca(dt, mine_id, region_id, area_id, area_dir_id, place_id
        , mining_flag_id, full_name, birth_year, age, age_group_id, prof_id, degree_id
        , group_flag, end_flag, event_id, process_id, prod_flag, relation_flag_id
        , desc_id, diagnos_id, last_editor)
        VALUES('$ca->dt', $ca->mine_id, $ca->region_id, $ca->area_id, $ca->area_dir_id, $ca->place_id
        , $ca->mining_flag_id, '$ca->full_name', $ca->birth_year, $ca->age, $ca->age_group_id, $ca->prof_id, $ca->degree_id
        , $ca->group_flag, $ca->end_flag, $ca->event_id, $ca->process_id, $ca->prod_flag, $ca->relation_flag_id
        , $desc_id, $diagnos_id, '$ca->last_editor')";
        // echo "$query\n";
        $rez = mysqli_query($link, $query);
        if($rez) {
            $rez = mysqli_query($link, 'SELECT last_insert_id();');
            if ($row = mysqli_fetch_row($rez)){ $casualty_id = $row[0];}
            mysqli_query($link, 'commit;');
            echo '{"result": "OK", "casualty_id": "'.$ca->id.'", "desc_id": "'.$desc_id.'", "diagnos_id": "'.$diagnos_id.'"}';
        } else {
            mysqli_query($link, 'rollback;');
            echo '{"result": "ERROR", "exception": "'.mysqli_error($link).'"}';
        }
    }
    
    // mysqli_query($link, 'commit;');
    // echo '{"result": "OK", "casualty_id": "'.$casualty_id.'", "desc_id": "'.$desc_id.'", "diagnos_id": "'.$diagnos_id.'"}';
    
} catch (Exception $e) {
    mysqli_query($link, 'rollback;');
    echo '{"result": "ERROR", "exception": "'.$e->getMessage().'"}';
}




mysqli_close($link);
