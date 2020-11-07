<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once "./service/ReportesService.php";

$Titulo = "Nota de venta";

$headerSql = $selectNotaSalida;
$cSql = $selectNotaSalidaDetalle;

$header = utils\ConnectionUtils::execSql($headerSql);
$registros = utils\ConnectionUtils::getRowsFromQuery($cSql);

$CiaSesion = getSessionCia();
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports_print.php"; ?>
        <title><?= $Gcia ?></title> 
        <script>
            $(document).ready(function () {
                $("#busca").val("<?= $busca ?>");
            });
        </script>
        <style>
            @page { 
                size: A4-Ticket; 
            }
            @media print {
                .noPrint {
                    display:none;
                }
            }
        </style>
    </head>

    <!-- Set "A5", "A4" or "A3" for class name -->
    <!-- Set also "landscape" if you need -->
    <body class="A4-Ticket">

        <!-- Each sheet element should have the class "sheet" -->
        <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->

        <div class="sheet padding-10mm">
            <form name="form1" method="get" action="" class="noPrint">
                <div style="text-align: center;position: relative;">
                    <input type="submit" name="Boton" class="nombre_cliente" value="Imprimir" onclick="print()">
                    <input type="hidden" name="busca" id="busca">
                </div>
            </form>
            <div align="center" class="text" style="align-items: flex-start">
                <table style="text-align: center" class="text" aria-hidden="true">                   
                    <tr><td align="center"><img src="lib/logoPoop.png" style="width: 200px; height: 90px;" alt=""></td></tr>
                    <tr><td align="center" class="TextosTitulos"><strong><?= $CiaSesion->getNombre() ?></strong></td></tr>                
                    <tr><td align="center"><?= $CiaSesion->getDireccion() . " " . $CiaSesion->getNumeroext() ?></td></tr>
                    <tr><td align="center"><?= $CiaSesion->getMunicipio() ?>, <?= $CiaSesion->getEstado() ?> Cp. <?= $CiaSesion->getCodigo() ?></td></tr>
                    <tr><td align="center">Telefono: <?= $CiaSesion->getTelefono() ?></td></tr>
                    <tr><td align="center">RFC: <?= $CiaSesion->getRfc() ?></td></tr>                             

                    <tr><td align="left"><strong>Folio: <?= $header["folio"] ?></strong></td></tr>
                    <tr><td align="left"><strong>Fecha venta <?= $header["fecha"] ?></strong></td></tr>
                    <tr><td align="left">Fecha impresion <?= date("Y-m-d H:i:s") ?></strong></td></tr>
                    <tr><td align="left"><strong><?= "Cliente: " . $header["nombre"] ?></strong></td></tr>

                </table><br/>

                <table style="text-align: center" class="text" aria-hidden="true">
                    <tr>
                        <td width="45%"><strong>Producto</td>
                        <td align="right" width="15%"><strong>Cnt</td>
                        <td align="right" width="20%"><strong>Precio</td>
                        <td align="right" width="20%"><strong>Importe</td>
                    </tr>

                    <?php 
                    $Total = 0;
                    foreach ($registros as $vt) : ?>
                        <tr>
                            <td><strong><?= $Vt["producto"] ?></strong> <?= $vt["descripcion"] ?></td>
                            <td align="right"><?= number_format($vt["cantidad"], 0) ?></td>
                            <td align="right"><?= number_format($vt["costo"], 2) ?></td>
                            <td align="right"><?= number_format($vt["total"], 2) ?></td>
                        </tr>    
                    <?php 
                    $Total += $vt["total"];
                    endforeach; ?>
                   
                    <tr>
                        <td></td>
                        <td></td>
                        <td align="right"> Total</td>
                        <td align="right"><?= number_format($Total, 2) ?></td>
                    </tr>

                </table>
            </div>
        </div>
    </body>
</html>
