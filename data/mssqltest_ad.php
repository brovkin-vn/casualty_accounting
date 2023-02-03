<?php

header('Content-Type: application/json; charset=utf-8');

$serverName = "evraz-sql-hse\\hse"; //serverName\instanceName
$connectionInfo = array( "Database"=>"HSE-Inspection"
//,"Authentication"=>"ActiveDirectoryPassword"
// ,"UID"=>"brovkin_vn"
// ,"Encrypt" => 1
// ,"PWD"=>"2063qwerty$"
);
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo date('d-m-y h:i:s.ms') . " Соединение установлено.";
     sqlsrv_close($conn);
}else{
     echo date('d-m-y h:i:s.ms') . " Соединение не установлено.";
     die(print_r( sqlsrv_errors(), true));

}

?>


