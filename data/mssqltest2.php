<?php
// phpinfo();

// header('Content-Type: application/json; charset=win-1251');

// $serverName = "evraz-sql-hse\hse"; //serverName\instanceName
// $connectionInfo = array( "Database"=>"HSE-Inspection", "UID"=>"uku\svc_ReportTravmatizm", "PWD"=>"4UOk3LPbkP21dsXXqgoK");
// $conn = sqlsrv_connect( $serverName, $connectionInfo);

// if( $conn ) {
//      echo "Соединение установлено.";
// }else{
//      echo "Соединение не установлено.";
//      die( print_r( sqlsrv_errors(), true));
// }

// sqlsrv_close($conn);

$serverName = "evraz-sql-hse.sib.evraz.com\\hse"; //serverName\instanceName

// $usr = "UKU\\svc_ReportTravmatizm";
// $pwd = "4UOk3LPbkP21dsXXqgoK";

// $usr = "Viktor.Brovkin@evraz.com";

$usr = "UKU\\Brovkin_VN";
$pwd = "2063qwerty$";


$connectionInfo = array("Database"=>"HSE-Inspection",
                    //     "CharacterSet" => "UTF-8",
                        "Encrypt" => 0,
                        "UID"=>$usr,
                        "PWD"=> $pwd);//,
                        //"Authentication"=>'ActiveDirectoryPassword');
$conn2 = sqlsrv_connect( $serverName, $connectionInfo);                        

if( $conn2 ) {


     
     echo "$usr/$pwd Соединение 2 установлено.<br />";
}else{
     echo "$usr/$pwd Соединение 2 не установлено.<br />";
     die( print_r( sqlsrv_errors(), true));
}

sqlsrv_close($conn2);

?>


// $q="SELECT 
//  a.id
//  ,a.AccidentDate dt
//  ,em.EmployeeName en
//  ,DATEDIFF(year, em.EmployeeDateBirth, CONVERT(DATE, a.AccidentDate)) age
//  ,YEAR(em.EmployeeDateBirth) bdy
//  ,ow.ShortName wn
//  ,oa.ShortName an
//  ,a.Scene place
//  ,dh.Name ln2
//  ,dh.AlternativeName ln
//  ,r.Name r
//  ,a.Process p 
//  ,a.AccidentDescription d
//  ,0 dd
// FROM OperAccident.Accident a 
// join MasterData.OrganizationUnits ow on ow.id = a.Enterprise_ID
// join MasterData.OrganizationUnits oa on oa.id = a.OrganizationUnit_ID
// join MasterDataAccident.DamageToHealth dh on dh.id = a.DamageToHealth_ID
// join MasterDataRiskHunting.RiskCategorys r on r.id = RiskCategoryId
// join OperAccident.EmployeesMembersAccident em on em.Accident_ID = a.ID
// where a.AccidentDate between '2022-07-01T00:00:00.000' and '2022-12-30T00:00:00.000'
// and a.Division_ID = 2
// and a.DamageToHealth_ID > 1
// -- and a.id > 76706
// order by 1
// ";
