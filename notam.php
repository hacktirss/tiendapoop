<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once "./service/ReportesService.php";

$Titulo = "Nota de entrada multiple";

$selectEntrada = "SELECT nm.factura, nm.fecha_entra fecha, nm.responsable, LPAD(nm.egreso,5,0) folio,
        prv.nombre, prv.rfc, ROUND(nm.importe,2) importe, nm.fechafac, nm.concepto 
        FROM prv, nm
        WHERE nm.proveedor = prv.id AND nm.cia = prv.cia AND nm.id = $busca;";

$selectDetalleEntrada = "
        SELECT IF(nmd.producto = 0, nmd.numero_serie, nmd.producto) producto,
        IF(nmd.producto = 0, CONCAT(nmd.marca, ' | ', nmd.modelo ), inv.descripcion) descripcion,
        nmd.cantidad, nmd.costo
        FROM nmd 
        LEFT JOIN inv ON nmd.producto = inv.id AND inv.cia = " . $UsuarioSesion->getCia() . " 
        WHERE nmd.id = $busca";

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
                            <td colspan="2">Folio: <?= $He["folio"] ?></td>
                            <td></td>
                            <td align="left">Fecha: <?= $He["fecha"] ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">Concepto: <?= $He["concepto"] ?></td>
                            <td>Factura: <?= $He["factura"] ?></td>
                            <td>Importe: <?= $He["importe"] ?></td>
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
