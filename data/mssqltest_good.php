<?php

$serverName = "Hq-server69"; //serverName\instanceName
$connectionInfo = array( "Database"=>"SURV", "UID"=>"SURVtoMPO", "PWD"=>"AuzmqS86kPAvS6lNSK0d");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Соединение установлено.";
     sqlsrv_close($conn);
}else{
     echo "Соединение не установлено.";
     die(print_r( sqlsrv_errors(), true));

}

?>


