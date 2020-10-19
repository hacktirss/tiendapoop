<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

require_once './service/ReportesService.php';

$Titulo = "Ayuda";

$cSql = "";

//$He = utils\ConnectionUtils::execSql($cSql);

//$registros = utils\ConnectionUtils::getRowsFromQuery($selectDetalleOrden, conectarse());
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
