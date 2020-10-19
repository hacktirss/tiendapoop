<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Orden de compra $busca";

$cSql = $selectOrdenHeader;

$He = utils\ConnectionUtils::execSql($cSql);

$registros = utils\ConnectionUtils::getRowsFromQuery($selectDetalleOrden, conectarse());
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

    </head>

    <body>

        <?php EncabezadoReportes() ?>

        <div id="container" style="min-height: 10cm">

            <div id="Reportes">

                <table>
                    <tbody>
                        <tr>
                            <td>Folio: <?= $He["id"] ?></td>
                            <td>Fecha:<?= $He["fecha"] ?></td>
                        </tr>
                        <tr>
                            <td>Concepto: <?= $He["concepto"] ?></td>
                            <td>Solicito: <?= $He["solicito"] ?></td>
                        </tr>
                    </tbody>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th>proveedor</th>
                            <th>concepto</th>
                            <th>importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro) { ?>
                            <tr>
                                <td><?= $registro["alias"] ?></td>
                                <td><?= ucwords(strtolower($registro["concepto"])) ?></td>
                                <td class="numero"><?= ucwords(strtoupper($registro["importe"])) ?></td>
                            <tr>
                            <?php } ?>
                    </tbody>
                </table>

                <table style="margin-top: 20px;">
                    <tfoot>
                        <tr>
                            <th>Observaciones</th>
                            <th>Nombre y Firma</th>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><?= $He["observaciones"] ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        <?php EncabezadoReportes() ?>

        <div id="container">

            <div id="Reportes">

                <table>
                    <tbody>
                        <tr>
                            <td>Folio: <?= $He["id"] ?></td>
                            <td>Fecha:<?= $He["fecha"] ?></td>
                        </tr>
                        <tr>
                            <td>Concepto: <?= $He["concepto"] ?></td>
                            <td>Solicito: <?= $He["solicito"] ?></td>
                        </tr>
                    </tbody>
                </table>


                <table>
                    <thead>
                        <tr>
                            <th>proveedor</th>
                            <th>concepto</th>
                            <th>importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro) { ?>
                            <tr>
                                <td><?= $registro["alias"] ?></td>
                                <td><?= ucwords(strtolower($registro["concepto"])) ?></td>
                                <td class="numero"><?= ucwords(strtoupper($registro["importe"])) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table style="margin-top: 20px;">
                    <tfoot>
                        <tr>
                            <th>Observaciones</th>
                            <th>Nombre y Firma</th>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><?= $He["observaciones"] ?></td>
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
                            <td style="text-align: center;">
                                <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </body>

</html>
