<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once "./service/ReportesService.php";

$Titulo = "Nota de salida";

$selectEntrada = "SELECT ns.factura, ns.fecha, ns.responsable, ns.factura folio,
        cli.nombre, cli.rfc, ns.concepto 
        FROM cli, ns
        WHERE ns.cliente = cli.id AND ns.cia = cli.cia AND ns.id = $busca;";

$selectDetalleEntrada = "
        SELECT IF(nsd.producto = 0, nsd.numero_serie, nsd.producto) producto,
        IF(nsd.tipo = 2, CONCAT(nsd.marca, ' | ', nsd.modelo ), inv.descripcion) descripcion,
        nsd.cantidad, nsd.costo
        FROM nsd 
        LEFT JOIN inv ON nsd.producto = inv.id AND inv.cia = " . $UsuarioSesion->getCia() . " AND nsd.tipo = 1
        WHERE nsd.id = $busca";

$He = utils\ConnectionUtils::execSql($selectEntrada);

$rows = utils\ConnectionUtils::getRowsFromQuery($selectDetalleEntrada);
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

    </head>

    <body>
        <?php EncabezadoReportes() ?>

        <div id="container">
            <div id="Encabezado">
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">Folio: <?= $He["rfc"] . " | " . $He["nombre"] ?></td>
                            <td align="left">Fecha: <?= $He["fecha"] ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">Concepto: <?= $He["concepto"] ?></td>
                            <td>Factura: <?= $He["folio"] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="Reportes">
                <table width="95%">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $Total = 0;
                        foreach ($rows as $row) :
                            ?>
                            <tr>
                                <td><?= $row["producto"] ?></td>
                                <td><?= $row["descripcion"] ?></td>
                                <td class="numero"><?= $row["cantidad"] ?></td>
                                <td class="numero"><?= number_format($row["costo"], 2) ?></td>
                                <td class="numero"><?= number_format($row["cantidad"] * $row["costo"], 2) ?></td>
                            </tr>
                            <?php
                            $Total += $row["cantidad"] * $row["costo"];
                        endforeach;
                        ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4">Total</td>
                            <td><?= number_format($Total, 2) ?></td>
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
