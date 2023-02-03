<?php

const COLOR_RED =    'FFFF3333';
const COLOR_ORANGE = 'FFFF9933';
const COLOR_YELLOW = 'FFFFFF33';
const COLOR_GREEN =  'FF99FF33';

require '../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if (isset($_GET['dt'])) $dt = $_GET['dt'];
else $dt = 'Н/Д';
if (isset($_GET['prof'])) $prof = $_GET['prof'];
else $prof = 'Н/Д';
if (isset($_GET['mine_name'])) $mine_name = $_GET['mine_name'];
else $mine_name = 'Н/Д';


$sheet->setCellValue('D1', 'Карта оценки рисков №');
$sheet->getStyle('D1:D1')->getFont()->setBold(true);
$sheet->getStyle("D1:D1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('B2', 'Работодатель:');
$sheet->setCellValue('B3', 'Должность (профессия):');
$sheet->setCellValue('B4', 'Дата оценки рисков:');

$sheet->setCellValue('C2', $mine_name);
$sheet->setCellValue('C3', $prof);
$sheet->setCellValue('C4', $dt);

$sheet->setCellValue('B6', 'Руководитель рабочей группы:');
$sheet->setCellValue('B7', 'Члены рабочей группы:');
$sheet->setCellValue('B8', 'Члены рабочей группы:');
$sheet->setCellValue('B9', 'Члены рабочей группы:');
$sheet->setCellValue('B10', 'Члены рабочей группы:');

$sheet->getStyle("C6:C11")->applyFromArray(
    [
        'borders' => [
            'horizontal' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ]
);



$sheet->getColumnDimension('A')->setWidth(3);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(24);
$sheet->getColumnDimension('D')->setWidth(49);
$sheet->getColumnDimension('E')->setWidth(12);
$sheet->getColumnDimension('F')->setWidth(13);
$sheet->getColumnDimension('G')->setWidth(17);
$sheet->getColumnDimension('H')->setWidth(16);
$sheet->getColumnDimension('I')->setWidth(8);
$sheet->getColumnDimension('J')->setWidth(17);

$sheet->setCellValue('A12', '#');
$sheet->setCellValue('B12', 'Процесс'); //<th class="col-md-2">Процесс</th>
$sheet->setCellValue('C12', 'Опасность'); //<th class="col-md-2">Событие</th>
$sheet->setCellValue('D12', 'Меры по управлению опасностью'); //<th class="col-md-3">Меры</th>
$sheet->setCellValue('E12', 'Пострадало'); //<th>Пострадало</th>
$sheet->setCellValue('F12', 'Дата последнего просшествия'); //<th class="col-md-1">Дата</th>
$sheet->setCellValue('G12', 'Прошло с момента последнего просшествия, лет'); //<th>Лет</th>
$sheet->setCellValue('H12', 'Наиболее тяжкие зарегестрированные последствия'); //<th>Тяжесть</th>
$sheet->mergeCells("I12:J12");
$sheet->setCellValue('I12', 'Оценка риска'); //<th colspan="2">Оценка</th>

include "get_card_sql.php";
$n = 0;
foreach ($card as $r) {
    $N = ++$n + 12;
    $sheet->setCellValue("A$N", strval($n));
    $sheet->setCellValue("B$N", $r['risk']); // <td>{{r.risk}}</td>
    $sheet->setCellValue("C$N", $r['risk2']); // <td>{{r.risk2}}</td>
    $sheet->setCellValue("D$N", $r['measure_text']); // <td><pre wrap>{{r.measure_text}}</pre></td>
    $sheet->setCellValue("E$N", $r['cnt']); // <td>{{r.cnt}}</td>
    $sheet->setCellValue("F$N", $r['last_dt']); // <td>{{r.last_dt}}</td>
    $sheet->setCellValue("G$N", $r['full_years']); // <td>{{r.full_years}}</td>
    $sheet->setCellValue("H$N", $r['degree_name']); // <td>{{r.degree_name}}</td>
    $sheet->setCellValue("I$N", $r['score']); // <td :style="{ background: r.score_color}">{{r.score}}</td>
    $sheet->setCellValue("J$N", $r['score_text']); // <td>{{r.score_text}}</td>

    if (in_array($r['score'], ['1B', '1A', '2A'])) {
        $sheet->getStyle("J$N")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("J$N")->getFill()->getStartColor()->setARGB(COLOR_GREEN);
    } else if (in_array($r['score'], ['2E', '3E', '3D', '4C', '4B'])){
        $sheet->getStyle("J$N")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("J$N")->getFill()->getStartColor()->setARGB(COLOR_ORANGE);
    } else if (in_array($r['score'], ['4E', '4D', '5E', '5D', '5C', '5B', '5A'])){
        $sheet->getStyle("J$N")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("J$N")->getFill()->getStartColor()->setARGB(COLOR_RED);
    } else {
        $sheet->getStyle("J$N")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("J$N")->getFill()->getStartColor()->setARGB(COLOR_YELLOW);
    }
}

$NL = $N + 3;

$sheet->setCellValue("B$NL", 'Лист ознакомления');
$sheet->getStyle("B$NL")->getFont()->setSize(12);


for ($i = 0; $i < 3; $i++) {
    $NL = $NL + 3;
    $sheet->setCellValue("B$NL", 'ФИО');
    $sheet->setCellValue("E$NL", 'Дата ознакомления');
    $sheet->setCellValue("H$NL", 'Подпись');
    $sheet->getStyle("B$NL:H$NL")->applyFromArray(
        [
            'font' => [
                'bold' => true,
                'size' => 7,
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_TOP,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]
    );
}


$sheet->getStyle("A12:J12")->getFont()->setBold(true);
$sheet->getStyle("A12:J12")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A12:A$N")->getFont()->setBold(true);
$sheet->getStyle("A12:J$N")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle("A12:J$N")->getAlignment()->setWrapText(true);

$sheet->getStyle("A12:J$N")->applyFromArray(
    [
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ]
);
$sheet->getStyle("B13:D$N")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);




//$sheet->getStyle('A1:I1')->getFont()->setSize(14);


$sheet->getStyle("A1");

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment;filename="Крата_рисков_' . $dt . '_' . str_replace(' ', '_', $prof) . '.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
