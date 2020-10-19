<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Pagos por cliente del $FechaI al $FechaF";

$cSql = $selectPagosCliente;
$cSqlT = $selectPagosClienteCnt;

$registros = utils\ConnectionUtils::getRowsFromQuery($cSql, conectarse());
$registrosT = utils\ConnectionUtils::getRowsFromQuery($cSqlT, conectarse());
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

    <body>

        <?php EncabezadoReportes() ?>

        <div id="container">

            <div id="Reportes" style="min-height: 150px;">
                <table width="95%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Pago</th>
                            <th>Fecha</th>
                            <th>Cuenta</>
                            <th>Nombre</th>
                            <th>Importe</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 0;
                        $Imp = 0;

                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format(++$nRng) ?></td>
                                <td><?= $rg["id"] ?></td>
                                <td><?= $rg["fecha"] ?></td>
                                <td><?= $rg["cuenta"] ?></td>                               
                                <td><?= $rg["nombre"] ?></td>
                                <td class="numero"><?= number_format($rg["importe"], 2, ".", "") ?></td>
                            </tr>
                            <?php
                            $Imp += $rg["importe"];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">Total</td>
                            <td><?= number_format($Imp, 2, ".", "") ?></td>
                        </tr>
                    </tfoot>
                </table>

            </div>
            
            <div id="Reportes" style="min-height: 150px; width: 60%;">
                
                <table width="95%">
                    <thead>
                        <tr>
                            <th>Cuenta</>
                            <th>Nombre</th>
                            <th># Pagos</th>
                            <th>Importe</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ImpT = 0;

                        foreach ($registrosT as $rg) {
                            ?>
                            <tr>
                                <td><?= $rg["cuenta"] ?></td>                               
                                <td><?= $rg["nombre"] ?></td>
                                <td class="numero"><?= $rg["pagos"] ?></td>
                                <td class="numero"><?= number_format($rg["importe"], 2, ".", "") ?></td>
                            </tr>
                            <?php
                            $ImpT += $rg["importe"];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td><?= number_format($ImpT, 2, ".", "") ?></td>
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
                                <span><input type="submit" name="Boton" value="Enviar"></span>
                                <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=SaldosCleintes&Detallado=No">Exportar</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </body>
</html>