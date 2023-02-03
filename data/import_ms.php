<?php



function compare_by_id($a, $b)
{
    $id_a = intval($a['id']);
    $id_b = intval($b['id']);
    $en_a = strval($a['en']);
    $en_b = strval($b['en']);
    // print("$en_a:$en_b\n");

    if ($id_a < $id_b) {
        return -1;
    } elseif ($id_a > $id_b) {
        return 1;
    } else {
        return strcmp($en_a, $en_b);
    }
}

function compare_by_data($a, $b)
{
    $id_a = intval($a['id']);
    $id_b = intval($b['id']);
    $en_a = strval($a['en']);
    $en_b = strval($b['en']);

    if ($id_a < $id_b) {
        return -1;
    } elseif ($id_a > $id_b) {
        return 1;
    } else {
        $r = strcmp($en_a, $en_b);
        
        if ($r == 0) {
            foreach (['dt', 'age', 'bdy', 'mine_name', 'area_name', 'place_name', 'level_name', 'level_name2', 'risk_name', 'process_name', 'desc_text'] as $field_name) {
                // $r = strcmp($a[$field_name], $b[$field_name]);
                // костыль на обратный слеш
                $r = strcmp(str_replace('\\', '', $a[$field_name]), $b[$field_name]);
                if ($r <> 0) {
                    print("a. $id_a $field_name = $a[$field_name]\n");
                    print("b. $id_b $field_name = $b[$field_name]\n");
                    return 0;
                }
            }
            return 1;
        } else {
            return $r;
        }
    }
}


require("config.php");

if (isset($_GET['dt1']))
    $dt1 = $_GET['dt1'];
else
    $dt1 = date("Y-m-d", strtotime("first day of this month"));

if (isset($_GET['dt2']))
    $dt2 = $_GET['dt2'];
else
    $dt2 = date("Y-m-d", strtotime("0 day"));


// header('HTTP/1.1 200 OK');
header('Content-Type: application/json; charset=utf-8');
// header('Access-Control-Allow-Origin: *');    

//$serverName = "evraz-sql-hse\\hse";
$serverName = "EVRAZ-SQAG-HSE\HSE_AG";
$connectionInfo = array("Database" => "HSE-Inspection", "CharacterSet" => "UTF-8" ,"TrustServerCertificate"=>"true");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    $query = 'SELECT id, dt, en, age, mine_name, area_name, place_name, level_name, risk_name, process_name, desc_text
    FROM casualty.accident
    WHERE deleted = 0
    ORDER BY 2';

    $query = "SELECT
    a.id
    ,CONVERT(VARCHAR(20),a.AccidentDate,120) dt
    ,COALESCE(em.EmployeeName, '-') en
    ,COALESCE(DATEDIFF(year, em.EmployeeDateBirth, CONVERT(DATE, a.AccidentDate)), 0) age
    ,COALESCE(YEAR(em.EmployeeDateBirth), 0) bdy
    ,ow.ShortName mine_name
    ,oa.ShortName area_name
    ,COALESCE(a.Scene, '-') place_name
    ,dh.Name level_name2
    ,COALESCE(dh.AlternativeName, '-') level_name
    ,COALESCE(r.Name, '-') risk_name
    ,COALESCE(a.Process, '-') process_name
    ,a.AccidentDescription desc_text

    FROM OperAccident.Accident a
    join MasterData.OrganizationUnits ow on ow.id = a.Enterprise_ID
    join MasterData.OrganizationUnits oa on oa.id = a.OrganizationUnit_ID
    join MasterDataAccident.DamageToHealth dh on dh.id = a.DamageToHealth_ID
    left join MasterDataRiskHunting.RiskCategorys r on r.id = RiskCategoryId
    left join OperAccident.EmployeesMembersAccident em on em.Accident_ID = a.ID
    -- where a.AccidentDate between '2022-07-01T00:00:00.000' and '2022-12-30T00:00:00.000'
    where a.AccidentDate between '" . $dt1 . "' and '" . $dt2 . "T23:59:59.999'
    and a.Division_ID = 2
    and a.DamageToHealth_ID > 1
    order by 1
    ";


    $rez = sqlsrv_query($conn, $query);
    $casualties_out = [];
    while ($row = sqlsrv_fetch_array($rez, SQLSRV_FETCH_ASSOC)) {
        // print_r($row);
        $casualties_out[] = $row;
    }

    sqlsrv_close($conn);

    require("config.php");
    $link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);
    if (!$link) {
        echo '{"result": "Ошибка соединения c БД"}';
        exit(0);
    }
    mysqli_query($link, 'set charset utf8');

    $query = 'SELECT id, dt, en, age, bdy, mine_name, area_name, place_name, level_name2, level_name, risk_name, process_name, desc_text
    FROM casualty.accident
    WHERE 1=1
    -- AND deleted = 1
    AND dt between "' . $dt1 . '" and "' . $dt2 . ' 23:59:59"
    ORDER BY 2';
    $rez = mysqli_query($link, $query);
    $casualties_local = [];
    while ($row = mysqli_fetch_assoc($rez)) {
        $casualties_local[] = $row;
    }

    mysqli_close($link);

    $result_new = array_udiff($casualties_out, $casualties_local, 'compare_by_id');
    foreach ($result_new as $row) {
        $row['feature'] = 0;
        $result_out[] = $row;
    }


    $result_old = array_uintersect($casualties_out, $casualties_local, 'compare_by_data');
    foreach ($result_old as $row) {
        $row['feature'] = 1;
        $result_out[] = $row;
    }

    print("\n");

    if (isset($result_out))
        echo '{"result": "OK", "dt1": "' . $dt1 . '","dt2": "' . $dt2 . '","casualties":' . json_encode($result_out, JSON_UNESCAPED_UNICODE) . '}';
    else
        echo '{"result": "OK", "dt1": "' . $dt1 . '","dt2": "' . $dt2 . '","casualties":""}';
} else {
    echo date('d-m-y h:i:s.ms') . " Соединение не установлено.";
    die(print_r(sqlsrv_errors(), true));
}
