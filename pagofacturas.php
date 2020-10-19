<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Relacion de pago de facturas del $FechaI al $FechaF";

$registros = utils\ConnectionUtils::getRowsFromQuery($selectPagoFacturas, conectarse());

$cSql = $selectPagoFacturas;
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
                $("#Formato").val("<?= $Formato ?>");
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

                    <?php if ($Formato == 1) { ?>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Id.pago</th>
                                <th>Factura</th>
                                <th>F.Pago</th>
                                <th>Fec/factura</th>
                                <th>Cliente</th>
                                <th>Importe</th>
                                <th>Iva</th>
                                <th>Total</th>
                                <th>Depositado</th>
                                <th>Diferencia</th>
                                <th>Folio Fiscal</th>
                                <th>Status</th>
                                <th>Banco</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $nRng = 1;
                            foreach ($registros as $rg) {
                                ?>
                                <tr>
                                    <td><?= number_format($nRng++) ?></td>
                                    <td><?= $rg[recibo] ?></td>
                                    <td><?= $rg[folioFc] ?></td>
                                    <td><?= $rg[fechaPago] ?></td>
                                    <td><?= $rg[fechaFac] ?></td>
                                    <td><?= ucwords(strtolower(substr($rg[nombre], 0, 40))) ?></td>
                                    <td class="numero"><?= number_format($rg[importe], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[iva], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[total], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[depositado], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[diferencia], 2, ".", "") ?></td>
                                    <td><?= $rg[uuid] ?></td>
                                    <td><?= $rg[status] ?></td>
                                    <td><?= $rg[banco] ?></td>
                                </tr>
                                <?php
                                if ($registros[$nRng - 1][banco] !== $rg[banco]) {
                                    ?>
                                    <tr class="subtotal">
                                        <td colspan="6">Subtotal</td>
                                        <td><?= number_format($nImporte, 2, ".", "") ?></td>
                                        <td><?= number_format($nIva, 2, ".", "") ?></td>
                                        <td><?= number_format($nTotal, 2, ".", "") ?></td>
                                        <td><?= number_format($nPagado, 2, ".", "") ?></td>
                                        <td><?= number_format($nDif, 2, ".", "") ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <?php
                                    $nImporte = $nIva = $nTotal = $nPagado = $nDif = 0;
                                }

                                $nImporte += $rg[importe];
                                $nIva += $rg[iva];
                                $nTotal += $rg[total];
                                $nPagado += $rg[depositado];
                                $nDif += $rg[diferencia];

                                $Importe += $rg[importe];
                                $Iva += $rg[iva];
                                $Total += $rg[total];
                                $Importe += $rg[depositado];
                                $Dif += $rg[diferencia];
                            }
                            ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="6">Total</td>
                                <td><?= number_format($Importe, 2, ".", "") ?></td>
                                <td><?= number_format($Iva, 2, ".", "") ?></td>
                                <td><?= number_format($Total, 2, ".", "") ?></td>
                                <td><?= number_format($Importe, 2, ".", "") ?></td>
                                <td><?= number_format($Dif, 2, ".", "") ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>

                        <?php
                    } else {
                        ?>
                        <thead>
                            <tr>
                                <th></th>
                                <th>F.Deposito</th>
                                <th>Cliente</th>
                                <th>Importe</th>
                                <th>Iva</th>
                                <th>Total</th>
                                <th>Depositado</th>
                                <th>Folio Fac.</th>
                                <th>Folio Fiscal</th>
                                <th>Status</th>
                                <th>Banco</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $ImporteR = $IvaR = $TotalR = $PagadoR = 0;
                            $nRng = 1;
                            foreach ($registros as $rg) {
                                $ImporteR += $rg[importe];
                                $IvaR += $rg[iva];
                                $TotalR += $rg[total];
                                $PagadoR += $rg[depositado];
                                ?>
                                <tr title="<?= "Correspondiente al recibo: $rg[recibo]" ?>">
                                    <td><?= number_format($nRng++) ?></td>
                                    <td><?= $rg[fechaPago] ?></td>
                                    <td><?= ucwords(strtolower(substr($rg[nombre], 0, 40))) ?></td>
                                    <td class="numero"><?= number_format($rg[importe], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[iva], 2, ".", "") ?></td>
                                    <td class="numero"><?= number_format($rg[total], 2, ".", "") ?></td>
                                    <td class="numero negrita">
                                        <?php if ($registros[$nRng - 1][recibo] !== $rg[recibo]) : ?>
                                            <?= number_format($PagadoR, 2, ".", "");
                                            $PagadoR = 0;
                                            ?>

        <?php endif; ?>
                                    </td>
                                    <td class="numero"><?= $rg[folio] ?></td>
                                    <td><?= $rg[uuid] ?></td>
                                    <td><?= $rg[status] ?></td>
                                    <td><?= $rg[banco] ?></td > 
                                </tr>

                                <?php
                                $Importe += $rg[importe];
                                $Iva += $rg[iva];
                                $Total += $rg[total];
                                $Pagado += $rg[depositado];

                                if ($registros[$nRng - 1][banco] !== $rg[banco]) {
                                    ?>
                                    <tr class="subtotal">
                                        <td colspan="3">Total</td>
                                        <td><?= number_format($Importe, 2, ".", "") ?></td>
                                        <td><?= number_format($Iva, 2, ".", "") ?></td>
                                        <td><?= number_format($Total, 2, ".", "") ?></td>
                                        <td><?= number_format($Pagado, 2, ".", "") ?></td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <?php
                                    $Importe = $Iva = $Total = $Pagado = $PagadoR = 0;
                                }

                                $gImporte += $rg[importe];
                                $gIva += $rg[iva];
                                $gTotal += $rg[total];
                                $gPagado += $rg[depositado];
                            }
                            ?>
                        </tbody>

                        <tfoot>                      
                            <tr>
                                <td colspan="3">Gran total</td>
                                <td><?= number_format($gImporte, 2, ".", "") ?></td> 
                                <td><?= number_format($gIva, 2, ".", "") ?></td>
                                <td><?= number_format($gTotal, 2, ".", "") ?></td>
                                <td><?= number_format($gPagado, 2, ".", "") ?></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
<?php } ?>
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
                                    Status: 
                                    <select name="Status" id="Status">
                                        <?php foreach ($StatusArray as $key => $value): ?>
                                            <option value="<?= $key ?>"><?= $value ?></option>
<?php endforeach; ?>
                                    </select> 
                                </td>
                                <td style="text-align: center;">
                                    Formato: 
                                    <select name="Formato" id="Formato">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select> 
                                </td>
                                <td style="text-align: center;">
                                    <span><input type="submit" name="Boton" value="Enviar"></span>
                                    <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="exportar">
                                        <?php if ($Formato == 1): ?>
                                            <a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionDePagos&Detallado=Si&Filtro=12&Textos=SubTotal">Exportar</a>
<?php else: ?>
                                            <!--<a href="report_excel.php?cSql=<?= urlencode($cSql) ?>&Nombre=RelacionDePagos&Detallado=Si&Filtro=0,10&Textos=SubTotal,Total Banco">Exportar</a>-->
                                            <span id="Exportar">Exportar</span>
<?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>

    </body>
</html>