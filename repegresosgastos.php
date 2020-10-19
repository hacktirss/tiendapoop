<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Relacion de gastos del $FechaI al $FechaF";

$cSql = $selectEgresosGastos;

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
                $("#Status").val("<?= $Status ?>");
                $("#Rubro").val("<?= $Rubro ?>");
                $("#Forma").val("<?= $Forma ?>");
                

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
                            <th>Pago</th>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Rubro</th>
                            <th>Concepto</th>
                            <th>FormaPago</th>
                            <th>Status</th>
                            <th>Importe</th>
                            <th>Iva</th>
                            <th>Iva-Ret</th>
                            <th>Isr</th>
                            <th>Total</th>
                            <th>Banco</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $Importe = $Iva = $IvaRet = $Isr = $Total = 0;
                        $nImporte = $nIva = $nIvaRet = $nIsr = $nTotal = 0;
                        $sImporte = $sIva = $sIvaRet = $sIsr = $sTotal = 0;
                        $nRng = 1;
                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format($nRng++) ?></td>
                                <td><?= $rg["pago"] ?></td>
                                <td><?= $rg["folio"] ?></td>
                                <td><?= $rg["fecha"] ?></td>
                                <td><?= ucwords(strtolower($rg["proveedor"])) ?></td>
                                <td><?= $rg["rubro"] ?></td>
                                <td><?= ucwords(strtolower($rg["concepto"])) ?></td>
                                <td><?= ucwords(strtolower($rg["forma"])) ?></td>
                                <td><?= $rg["status"] ?></td>
                                <td class="numero"><?= number_format($rg["importe"], 2) ?></td>
                                <td class="numero"><?= number_format($rg["iva"], 2) ?></td>
                                <td class="numero"><?= number_format($rg["iva_ret"], 2) ?></td>
                                <td class="numero"><?= number_format($rg["isr"], 2) ?></td>
                                <td class="numero"><?= number_format($rg["total"], 2) ?></td>
                                <td><?= $rg["banco"] ?></td>
                            </tr>
                            <?php
                            $nImporte += $rg["importe"];
                            $nIva += $rg["iva"];
                            $nIvaRet += $rg["iva_ret"];
                            $nIsr += $rg["isr"];
                            $nTotal += $rg["total"];

                            $sImporte += $rg["importe"];
                            $sIva += $rg["iva"];
                            $sIvaRet += $rg["iva_ret"];
                            $sIsr += $rg["isr"];
                            $sTotal += $rg["total"];

                            if ($registros[$nRng - 1]["forma"] !== $rg["forma"] || $registros[$nRng - 1]["banco"] !== $rg["banco"]) :
                                ?>
                                <tr class="subtotal">
                                    <td colspan="9"><?= $rg["descripcion"] ?></td>
                                    <td><?= number_format($sImporte, 2) ?></td>
                                    <td><?= number_format($sIva, 2) ?></td>
                                    <td><?= number_format($sIvaRet, 2) ?></td>
                                    <td><?= number_format($sIsr, 2) ?></td>
                                    <td><?= number_format($sTotal, 2) ?></td>
                                    <td></td>
                                </tr>
                                <?php
                                $sImporte = $sIva = $sIvaRet = $sIsr = $sTotal = 0;
                            endif;

                            if ($registros[$nRng - 1]["banco"] !== $rg["banco"]) :
                                ?>
                                <tr class="subtotal2">
                                    <td colspan="9">Subtotal</td>
                                    <td><?= number_format($nImporte, 2) ?></td>
                                    <td><?= number_format($nIva, 2) ?></td>
                                    <td><?= number_format($nIvaRet, 2) ?></td>
                                    <td><?= number_format($nIsr, 2) ?></td>
                                    <td><?= number_format($nTotal, 2) ?></td>
                                    <td></td>
                                </tr>
                                <?php
                                $nImporte = $nIva = $nIvaRet = $nIsr = $nTotal = 0;
                            endif;

                            $Importe += $rg["importe"];
                            $Iva += $rg["iva"];
                            $IvaRet += $rg["iva_ret"];
                            $Isr += $rg["isr"];
                            $Total += $rg["total"];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">Total</td>
                            <td><?= number_format($Importe, 2) ?></td>
                            <td><?= number_format($Iva, 2) ?></td>
                            <td><?= number_format($IvaRet, 2) ?></td>
                            <td><?= number_format($Isr, 2) ?></td>
                            <td><?= number_format($Total, 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
                                    Forma de pago:
                                    <?php ListasCatalogo::getFormasDePago("Forma", "style='width: 200px;'", "Todos"); ?>
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
                                    <span class="exportar"><a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionEgresos&Detallado=No">Exportar</a></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
    </body>
</html>