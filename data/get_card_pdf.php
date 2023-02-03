<?php

require '../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if (isset($_GET['dt'])) $dt = $_GET['dt'];
else $dt = 'Н/Д';
if (isset($_GET['prof'])) $prof = $_GET['prof'];
else $prof = 'Н/Д';


$sheet->setCellValue('A1', $dt);
$sheet->setCellValue('C1', $prof);

$sheet->setCellValue('A4', '#');
$sheet->setCellValue('B4', 'Процесс'); //<th class="col-md-2">Процесс</th>
$sheet->setCellValue('C4', 'Событие'); //<th class="col-md-2">Событие</th>
$sheet->setCellValue('D4', 'Меры'); //<th class="col-md-3">Меры</th>
$sheet->setCellValue('E4', 'Пострадало'); //<th>Пострадало</th>
$sheet->setCellValue('F4', 'Дата'); //<th class="col-md-1">Дата</th>
$sheet->setCellValue('G4', 'Лет'); //<th>Лет</th>
$sheet->setCellValue('H4', 'Тяжесть'); //<th>Тяжесть</th>
$sheet->setCellValue('I4', 'Код'); //<th colspan="2">Оценка</th>

include "get_card_sql.php";
$n = 0;
foreach ($card as $r) {
    $N = ++$n + 4;
    $sheet->setCellValue("A$N", strval($n));
    $sheet->setCellValue("B$N", $r['risk']); // <td>{{r.risk}}</td>
    $sheet->setCellValue("C$N", $r['risk2']); // <td>{{r.risk2}}</td>
    $sheet->setCellValue("D$N", $r['measure_text']); // <td><pre wrap>{{r.measure_text}}</pre></td>
    $sheet->setCellValue("E$N", $r['cnt']); // <td>{{r.cnt}}</td>
    $sheet->setCellValue("F$N", $r['last_dt']); // <td>{{r.last_dt}}</td>
    $sheet->setCellValue("G$N", $r['full_years']); // <td>{{r.full_years}}</td>
    $sheet->setCellValue("H$N", $r['degree_name']); // <td>{{r.degree_name}}</td>
    $sheet->setCellValue("I$N", $r['score']); // <td :style="{ background: r.score_color}">{{r.score}}</td>
    // $sheet->setCellValue("J$N", $r['score_text']);// <td>{{r.score_text}}</td>

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


$sheet->getStyle('A1:I1')->getFont()->setSize(14);
// $sheet->getStyle('A1:I1')->getFont()->setBold(true);
$sheet->getStyle("A4:I4")->getFont()->setBold(true);
$sheet->getStyle("A4:I4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A4:A$N")->getFont()->setBold(true);

$sheet->getStyle("A4:I$N")->getAlignment()->setWrapText(true);

$sheet->getStyle("A4:I$N")->applyFromArray(
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

$sheet->getStyle("E5:I$N")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getColumnDimension('A')->setWidth(3);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(12);
$sheet->getColumnDimension('F')->setWidth(12);
$sheet->getColumnDimension('G')->setWidth(5);
$sheet->getColumnDimension('H')->setWidth(13);
$sheet->getColumnDimension('I')->setWidth(5);

$sheet->getStyle("A1");

// header('Content-Type: application/vnd.ms-excel; charset=utf-8');
// header('Content-Disposition: attachment;filename="Крата_рисков_' . $dt . '_' . str_replace(' ', '_', $prof) . '.xlsx"');
// header('Cache-Control: max-age=0');


// $writer = new Xlsx($spreadsheet);
// $writer->save('php://output');



IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);

// Redirect output to a client’s web browser (PDF)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="01simple.pdf"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Pdf');
$writer->save('php://output');

