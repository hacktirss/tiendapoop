<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Nota de entrada de productos";

$headerSql = $selectNotaEntrada;
$cSql = $selectNotaEntradaDetalle;

$header = utils\ConnectionUtils::execSql($headerSql);
$registros = utils\ConnectionUtils::getRowsFromQuery($cSql);

//error_log(print_r($registros, TRUE));

$registrosLandscape = 25;
$registrosVertical = 40;
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports_print.php"; ?>
        <title><?= $Gcia ?></title> 
        <style>
            @page { 
                size: A4 /*landscape*/; 
            }
        </style>        
    </head>

    <!-- Set "A5", "A4" or "A3" for class name -->
    <!-- Set also "landscape" if you need -->
    <body class="A4">
        <div class="iconos">
            <table aria-hidden="true">
                <tr>
                    <td style="text-align: left"><?= $Titulo ?></td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">

                    </td>
                    <td style="text-align: center;"><i onclick="print();" title="Imprimir" class="icon fa fa-lg fa-print" aria-hidden="true"></i></td>
                </tr>
            </table>
        </div>
        <div id="TablaExcel">
            <!-- Each sheet element should have the class "sheet" -->
            <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
            <?php
            $nRng = 1;
            $close = false;
            $sheet = 0;
            $total = 0;
            if (count($registros) > 0) {
                foreach ($registros as $registro) {
                    if (($nRng - 1) % $registrosVertical == 0) {
                        $close = false;
                        $sheet++;

                        $ignore = "";
                        if ($sheet > 1) {
                            $ignore = "tableexport-ignore";
                        }
                        ?>
                        <div class="sheet padding-10mm"> <!-- Abre hoja-->
                            <?php EncabezadoReportes() ?>

                            <div id="TablaDatosHeader">
                                <table>
                                    <thead>
                                        <tr>
                                            <td>Factura: <?= $header["factura"] ?></td>
                                            <td></td>
                                            <td>Fecha de impresion: <?= date("Y-m-d H:i:s") ?></td>
                                        </tr>
                                        <tr>
                                            <td>Folio: <?= $header["folio"] ?></td>
                                            <td>Fecha factura: <?= $header["fechafac"] ?></td>
                                            <td>Responsable: <?= $header["responsable"] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Proveedor: <?= $header["nombre"] ?></td>
                                            <td>RFC: <?= $header["rfc"] ?></td> 
                                            <td></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div id="TablaDatosReporte"> <!-- Abre div estilos-->
                                <div style="padding-top: 10px;">
                                    <div style="padding-bottom: 10px;"></div>
                                    <table aria-hidden="true"> <!-- Abre tabla 1-->
                                        <thead>
                                            <tr class="<?= $ignore ?>">
                                                <td>#</td>
                                                <td>Clave</td>
                                                <td>Producto</td>
                                                <td>Cantidad</td>
                                                <td>Precio</td>
                                                <td>Total</td>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $nRng ?></td>
                                            <td class="numero tableexport-number"><?= $registro["producto"] ?></td>
                                            <td class="texto tableexport-string"><?= $registro["descripcion"] ?></td>
                                            <td class="texto tableexport-string"><?= $registro["cantidad"] ?></td>
                                            <td class="numero tableexport-number"><?= $registro["precio"] ?></td>
                                            <td class="numero tableexport-number"><?= $registro["total"] ?></td>
                                        </tr>

                                        <?php
                                        //error_log("Modulo $nRng: " . ($nRng % $registrosVertical));
                                        if ($nRng % $registrosVertical == 0) {
                                            if (($nRng - 1) == count($registros)) {
                                                
                                            } else {
                                                echo ''
                                                . '</tbody>'
                                                . '</table> <!-- Cierra tabla 1 si hay mas de 25 registros-->'
                                                . '</div>'
                                                . '</div> <!-- Cierra div estilos-->'
                                                . '</div> <!-- Cierra hoja si hay mas de 25 registros-->';
                                                $close = true;
                                            }
                                        }
                                        $nRng++;
                                        $total += $registro["total"];
                                    }
                                } else {
                                    echo '<div class="sheet padding-10mm"> <!-- Abre hoja-->';
                                    EncabezadoReportes();

                                    echo '<div id="TablaDatosHeader">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <td>Folio: ' . $busca . '</td>
                                                        <td>Estacion: ' . $header["estacion"] . '</td>
                                                        <td>Fecha: ' . $header["fecha"] . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Concepto:  ' . $header["concepto"] . '</td>
                                                        <td>Factura:  ' . $header["factura"] . '</td>
                                                        <td>Responsable: ' . $header["responsable"] . '</td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                        <div id="TablaDatosReporte"> <!-- Abre div estilos-->
                                            <div style="padding-top: 10px;">
                                                <div style="padding-bottom: 10px;"></div>
                                                    <table aria-hidden="true"> <!-- Abre tabla 1-->
                                                    <thead>
                                                        <tr class="<?= $ignore ?>">
                                                            <td>#</td>
                                                            <td>Clave</td>
                                                            <td>Producto</td>
                                                            <td>Cantidad</td>
                                                            <td>Precio</td>
                                                            <td>Total</td>
                                                        </tr>
                                                    </thead>

                                                    <tbody>';
                                }

                                if (!$close) {
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">Total</td>
                                        <td class="numero tableexport-number"><?= $total ?></td>
                                    </tr>
                                </tfoot>
                            </table> <!-- Cierra tabla 1 si hay menos de 25 registros-->                            
                        </div>
                        <div id="acuse">
                            <table>
                                <thead>
                                    <tr>
                                        <td>Observaciones</td>
                                        <td>Nombre y Firma</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                               
                    </div> <!-- Cierra div estilos-->
                </div> <!-- Cierra hoja si hay mas de 25 registros-->
                <?php
            }
            ?>
        </div>
    </body>
</html>     
