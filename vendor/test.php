<?php
require 'autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Hello World !');

    $writer = new Xlsx($spreadsheet);

    // Clean any previous output
    if (ob_get_contents()) ob_end_clean();

    // Set correct headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="hello_world.xlsx"');
    header('Cache-Control: max-age=0');

    // Save the file to output
    $writer->save('php://output');
    exit();
} catch (Exception $e) {
    echo 'Error creating Excel file: ',  $e->getMessage();
}
?>
