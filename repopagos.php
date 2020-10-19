<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Relacion de ordenes de pago $FechaI al $FechaF";


$cSql = $selectOrdenesPago;

$registros = utils\ConnectionUtils::getRowsFromQuery($cSql, conectarse());
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
    </head>

    <body>

        <?php EncabezadoReportes() ?>

        <div id="container">

            <div id="Reportes">
                <table width="95%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Orden</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Rubro</th>
                            <th>Concepto</th>
                            <th>status</th>
                            <th>Importe</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 1;
                        $Porpagar = $Pagado = 0;
                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format($nRng++) ?></td>
                                <td><?= $rg["orden"] ?></td>
                                <td><?= $rg["fecha"] ?></td>
                                <td><?= ucwords(strtolower($rg["proveedor"])) ?></td>
                                <td><?= ucwords(strtolower($rg["rubro"])) ?></td>
                                <td><?= ucwords(strtolower($rg["concepto"])) ?></td>
                                <td><?= ucwords(strtolower($rg["status"])) ?></td>
                                <td class="numero"><?= number_format($rg["importe"], 2) ?></td>
                                <td class="centrar">
                                    <?php
                                    if ($rg["pagonumero"] == 0) {
                                        $Porpagar += $rg["importe"];
                                    } else {
                                        ?><i class="fa fa-check-square"></i><?php
                                        $Pagado += $rg["importe"];
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">Pagado </td>
                            <td><?= number_format($Pagado, 2) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7">Por pagar </td>
                            <td><?= number_format($Porpagar, 2) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7">Total</td>
                            <td><?= number_format($Pagado + $Porpagar, 2) ?></td>
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
                                Rubro: 
                                <?php ListasCatalogo::listaNombreCatalogo("Rubro", "RUBROS_PAGOS", "Todos"); ?>
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
                                <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionOrdenesPago&Detallado=No">Exportar</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </body>
</html>