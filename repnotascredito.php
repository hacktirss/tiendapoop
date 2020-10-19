<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Relacion de notas de crédito emitidas del $FechaI al $FechaF";

$cSql = $selectRelacionNotasCredito;

$registros = utils\ConnectionUtils::getRowsFromQuery($selectRelacionNotasCredito, conectarse());
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
                            <th>Factura</th>
                            <th>Concepto</th>
                            <th>Folio Fiscal</th>
                            <th>Importe</th>
                            <th>Iva</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 1;
                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format($nRng++) ?></td>
                                <td><?= $rg["folio"] ?></td>
                                <td><?= $rg["fecha"] ?></td>
                                <td><?= $rg["cliente"] ?></td>                               
                                <td><?= ucwords(strtolower(substr($rg["nombre"], 0, 30))) ?></td>
                                <td><?= $rg["factura"] ?></td>
                                <td><?= $rg["concepto"] ?></td>                         
                                <td><?= $rg["uuid"] ?></td>
                                <td class="numero"><?= number_format($rg["importe"], 2, ".", "") ?></td>
                                <td class="numero"><?= number_format($rg["iva"], 2, ".", "") ?></td>
                                <td class="numero"><?= number_format($rg["total"], 2, ".", "") ?></td>
                                <td><?= $rg[status] ?></td>
                            </tr>
                            <?php
                            $Importe += $rg[importe];
                            $Iva += $rg[iva];
                            $Total += $rg[total];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8">Total</td>
                            <td><?= number_format($Importe, 2, ".", "") ?></td>
                            <td><?= number_format($Iva, 2, ".", "") ?></td>
                            <td><?= number_format($Total, 2, ".", "") ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
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
                                            <input type="date" id="FechaI" name="FechaI">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>F.final:</td>
                                        <td>
                                            <input type="date" id="FechaF" name="FechaF">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center;">
                                Status: 
                                <select name="Status" id="Status">
                                    <?php foreach ($StatusArray as $key => $value): ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select> 
                            </td>
                            <td style="text-align: center;">
                                <span><input type="submit" name="Boton" value="Enviar"></span>
                                <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionNotasCredito&Detallado=No">Exportar</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>    