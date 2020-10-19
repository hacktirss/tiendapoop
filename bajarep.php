<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once './excel/PHPExcel.php';

$link = conectarse();

$name = "Reporte".date("ymdHis");


//$cSql = urldecode($_REQUEST[cSql]);

$cSql = str_replace("¡", "+", $_REQUEST[cSql]);

error_log("*****************\n" . $cSql);

$result = mysqli_query($link, $cSql);

$count = mysqli_num_fields($result);

$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->setTitle($name);

$objPHPExcel->getProperties()->setTitle("Reporte");

$font = new PHPExcel_Style_Font();
$font->setName("Helvetica");
$font->setSize(10);

$sheet = 0;
$row = 1;
$column = 0X41;

$info_campo = mysqli_fetch_fields($result);
error_log("Encabezados [$count]...");
foreach ($info_campo as $valor) {

    $ident = chr($column) . "" . $row;
    //$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($ident, ucwords(strtolower(mysql_field_name($result, $i))));
    $objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($ident, ucwords($valor->name));
    $objPHPExcel->setActiveSheetIndex($sheet)->getCell($ident)->getStyle()->setFont($font);
    $objPHPExcel->setActiveSheetIndex($sheet)->getStyle($ident)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex($sheet)->getStyle($ident)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //error_log(chr($column) . "" . $row . " = " . mysql_field_name($result, $i) . "\n");
    $column = $column + 1;
}

error_log("Datos...");

while ($datos = mysqli_fetch_array($result)) {

    $column = 0X41;
    $row++;

    for ($i = 0; $i < $count; $i++) {
        $ident = chr($column) . "" . $row;
        $objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($ident, $datos[$i]);
        $objPHPExcel->setActiveSheetIndex($sheet)->getCell($ident)->getStyle()->setFont($font);
        $objPHPExcel->setActiveSheetIndex($sheet)->getStyle($ident)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $column = $column + 1;
    }
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex($sheet);

// Auto size columns for each worksheet

$sheet = $objPHPExcel->getActiveSheet();
$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
$cellIterator->setIterateOnlyExistingCells(true);
/** @var PHPExcel_Cell $cell */
foreach ($cellIterator as $cell) {
    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
}

//// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="' . $name . '.xls"');
//header('Cache-Control: max-age=0');
//// If you're serving to IE 9, then the following may be needed
//header('Cache-Control: max-age=1');
//
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' .$name . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$objWriter->save('php://output');

mysqli_close($link);
?>