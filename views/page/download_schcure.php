<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Services\ScheduleService;



// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'PressNo')
    ->setCellValue('C1', 'PressSide')
    ->setCellValue('D1', 'Curecode');
// ->setCellValue('E1', 'Total');

$get = new ScheduleService;
$getmachine = $get->getPressMaster();
$count = $get->CountMachine();

$row = 2;
foreach ($getmachine as $g) {
    for ($i = 0; $i < 2; $i++) {
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $row, $date_inter);

        $spreadsheet->setActiveSheetIndex(0)

            ->setCellValue('B' . $row, $g['ID']);

        // $spreadsheet->setActiveSheetIndex(0)
        //     ->setCellValue('C' . $row, $shift);

        $row = $row + 1;
    }
}

// style
$spreadsheet->getActiveSheet()->getStyle('B1:B299')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
//     ->getNumberFormat()
// ->setFormatCode("YYYY-MM-DD");



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Schedule_Cure');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="Schedule_Building.xlsx"');
header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
header('Content-Disposition: attachment;filename="Schedule_Cure.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
// header('Cache-Control: max-age=1');


$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
