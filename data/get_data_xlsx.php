<?php

require '../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if (isset($_GET['dt1']))
    $dt1 = $_GET['dt1'];
else
    $dt1 = date("Y-m-d", strtotime("first day of this month"));

    if (isset($_GET['dt2']))
    $dt2 = $_GET['dt2'];
else
    $dt2 = date("Y-m-d", strtotime("0 day"));

if (isset($_GET['mine_id']))
    $mine_id = $_GET['mine_id'];
else
    $mine_id = 0;


$sheet->setCellValue('A1', "Выгрузка данных о НС за период с $dt1 по $dt2");
$sheet->setCellValue('A2', "Нажмите Ctrl-T для создания таблицы");

$sheet->setCellValue('A4', '#');
$sheet->setCellValue('B4', 'Дата'); 
$sheet->setCellValue("C4", 'Предприятие');
$sheet->setCellValue("D4", 'Площадка');
$sheet->setCellValue("E4", 'Участок');
$sheet->setCellValue("F4", 'Направление участка');
$sheet->setCellValue("G4", 'Место НС');
$sheet->setCellValue("H4", 'Горные выработки');
$sheet->setCellValue("I4", 'ФИО пострадавшего');
$sheet->setCellValue("J4", 'Возраст');
$sheet->setCellValue("K4", 'Возрастная группа');
$sheet->setCellValue("L4", 'Профессия');
$sheet->setCellValue("M4", 'Описание НС');
$sheet->setCellValue("N4", 'Диагноз');
$sheet->setCellValue("O4", 'Степень тяжести');
$sheet->setCellValue("P4", 'Групповой');
$sheet->setCellValue("Q4", 'Прошло с момента травмы');
$sheet->setCellValue("R4", 'Отношение');
$sheet->setCellValue("S4", 'Событие');
$sheet->setCellValue("T4", 'Процесс');

include "get_data_sql.php";

$n = 0;
foreach ($ca as $r) {
    $N = ++$n + 4;
    $sheet->setCellValue("A$N", $r['id']);
    $sheet->setCellValue("B$N", $r['dt']);
    $sheet->setCellValue("C$N", $r['mine_name']);
    $sheet->setCellValue("D$N", $r['region_name']);
    $sheet->setCellValue("E$N", $r['area_name']);
    $sheet->setCellValue("F$N", $r['area_dir_name']);
    $sheet->setCellValue("G$N", $r['place_name']);
    $sheet->setCellValue("H$N", $r['mining_flag_name']);
    $sheet->setCellValue("I$N", $r['full_name']);
    $sheet->setCellValue("J$N", $r['age']);
    $sheet->setCellValue("K$N", $r['age_group_name']);
    $sheet->setCellValue("L$N", $r['prof_name']);
    $sheet->setCellValue("M$N", $r['desc_text']);
    $sheet->setCellValue("N$N", $r['diagnos_text']);
    $sheet->setCellValue("O$N", $r['degree_name']);
    $sheet->setCellValue("P$N", $r['group_flag']);
    $sheet->setCellValue("Q$N", $r['pass_time']);
    $sheet->setCellValue("R$N", $r['relation_flag_name']);
    $sheet->setCellValue("S$N", $r['event_name']);
    $sheet->setCellValue("T$N", $r['process_name']);
}

$sheet->getStyle("A4");
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment;filename="data.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
