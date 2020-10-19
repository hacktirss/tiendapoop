<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");
include_once ('cfdi33/validator/CFDIValidator.php');

use com\softcoatl\cfdi\v33\validation\CFDIValidator;
use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Relacion de facturas canceladas del $FechaI al $FechaF";

$registros = utils\ConnectionUtils::getRowsFromQuery($selectCancelacionFacturas, conectarse());

$cSql = $selectRelacionFacturas;
//error_log($cSql);
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

        <script>
            $(document).ready(function () {
                $("#FechaI").val("<?= $FechaI ?>").attr("size", "10");
                $("#FechaF").val("<?= $FechaF ?>").attr("size", "10");
                $("#Status").val("<?= $Status ?>");
                $("#cFechaI").css("cursor", "hand").click(function () {
                    displayCalendar($("#FechaI")[0], "yyyy-mm-dd", $(this)[0]);
                });
                $("#cFechaF").css("cursor", "hand").click(function () {
                    displayCalendar($("#FechaF")[0], "yyyy-mm-dd", $(this)[0]);
                });

                $("#Exportar").click(function () {
                    var instance = new TableExport($("#Reportes"), {
                        headers: true,
                        footers: true,
                        formats: ["xlsx"],
                        ignoreCSS: ".tableexport-ignore",
                        trimWhitespace: true,
                        fileName: "Relacion de facturas",
                        RTL: false,
                        bootstrap: true,
                        position: "well",
                        exportButtons: false
                    });
                    var exportData = instance.getExportData()["Reportes"]["xlsx"];
                    instance.export2file(exportData.data, exportData.mimeType, exportData.filename, exportData.fileExtension);
                });
            });
        </script>
    </head>

    <body>

        <?php EncabezadoReportes() ?>

        <div id="container">

            <div id="Reportes">
                <table width="95%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Cta</>
                            <th>Cliente</th>
                            <th>Concepto</th>
                            <th>Folio Fiscal</th>
                            <th>Status</th>
                            <th>Cancelable</th>
                            <th>Status Cancelacion</th>
                            <th>Status Detisa</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 1;
                        foreach ($registros as $rg) {
                            $statusCFDI = CFDIValidator::CallAPI($rg['emisor'], $rg['receptor'], number_format($rg['cfditotal'], 2, '.', ''), $rg['uuid']);
                            ?>
                            <tr>
                                <td><?= number_format($nRng++) ?></td>
                                <td><?= $rg['folio'] ?></td>
                                <td><?= $rg['fecha'] ?></td>
                                <td><?= $rg['cliente'] ?></td>                               
                                <td><?= ucwords(strtolower(substr($rg['nombre'], 0, 30))) ?></td>
                                <td><?= $rg['concepto'] ?></td>                         
                                <td><?= $rg['uuid'] ?></td>
                                <td><?= $statusCFDI->Estado ?></td>
                                <td><?= strpos(strtoupper($statusCFDI->Estado), "NO ENCONTRADO") !== false ? "Documento no encontrado" : $statusCFDI->EsCancelable ?></td>
                                <td><?=
                                    strpos(strtoupper($statusCFDI->Estado), "VIGENTE") === false ? (
                                            strpos(strtoupper($statusCFDI->Estado), "NO ENCONTRADO") !== false ?
                                                    "Documento no encontrado" : $statusCFDI->EstatusCancelacion ) : "No cancelado"
                                    ?></td>
                                <td><?= $rg['status'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="Footer">
            <form name="form1" class="oculto" method="post" action="">
                <table width="95%"  align="center" class="form">
                    <tbody>
                        <tr>
                            <td style="width: 30px;">
                                <table>
                                    <tr>
                                        <td>F.inicial:</td>
                                        <td>
                                            <input type="text" id="FechaI" name="FechaI">
                                        </td>
                                        <td>
                                            <img id="cFechaI" src="lib/calendar.png">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>F.final:</td>
                                        <td>
                                            <input type="text" id="FechaF" name="FechaF">
                                        </td>
                                        <td>
                                            <img id="cFechaF" src="lib/calendar.png">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center;">
                                <span><input type="submit" name="Boton" value="Enviar"></span>
                                <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionFacturas&Detallado=No">Exportar</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </body>
</html>