<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Inventario de equipos al " . date("Y-m-d H:i:s");

$cSql = $selectInventarioE;

$registros = utils\ConnectionUtils::getRowsFromQuery($cSql, conectarse());
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

        <script>
            $(document).ready(function () {
                $("#Detallado").val("<?= $Detallado ?>");

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

            <div id="Reportes">
                <table width="95%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Grupo</th>
                            <th>Descripcion</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>No.entrada</th>
                            <th>No.serie</th>
                            <th>Cantidad</th>
                            <th>Costo</th>
                            <th>Precio</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 0;
                        $totCantidad = $totCosto = $totPrecio = 0;
                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format( ++$nRng) ?></td>
                                <td><?= $rg["grupo"] ?></td>
                                <td><?= $rg["descripcion"] ?></td>
                                <td><?= $rg["marca"] ?></td>
                                <td><?= $rg["modelo"] ?></td>
                                <td><?= $rg["numero_entrada"] ?></td>
                                <td><?= $rg["numero_serie"] ?></td>
                                <td class="numero"><?= number_format($rg["cantidad"]) ?></td>
                                <td class="numero"><?= number_format($rg["costo"], 2, ".", "") ?></td>
                                <td class="numero"><?= number_format($rg["precio"], 2, ".", "") ?></td>
                            </tr>
                            <?php
                            $totCantidad += $rg["cantidad"] ;
                            $totCosto += $rg["cantidad"] * $rg["costo"];
                            $totPrecio += $rg["cantidad"] * $rg["precio"];
                        }
                        ?>
                    </tbody>  
                    <tfoot>
                        <tr>
                            <td colspan="7">Total</td>
                            <td><?= number_format($totCantidad) ?></td>
                            <td><?= number_format($totCosto, 2, ".", "") ?></td>
                            <td><?= number_format($totPrecio, 2, ".", "") ?></td>
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
                            <td style="text-align: center;">
                                Detallado: 
                                <select name="Detallado" id="Detallado">
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select> 
                            </td>                     
                            <td style="text-align: center;">
                                <span><input type="submit" name="Boton" value="Enviar"></span>
                                <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=Inventario&Detallado=No">Exportar</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </body>
</html>
