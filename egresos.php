<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "registroEgresos";
$arrayFilter = array();
$session = new PaginadorSession("egresos.id", "egresos.fecha, egresos.id", $nameSession, $arrayFilter, "Filtro");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 87;
$Titulo = "Registro de egresos(pagos/compras)";

$conditions = "egresos.cia = " . $usuarioSesion->getCia();
if (!empty($session->getSessionAttribute("returnLink"))) {
    $conditions .= " AND entradaid = 0 AND proveedorde <> 'Servicios' ";
}

$paginador = new Paginador($Id,
        "prv.proveedorde, egresos.entradaid, egresos.ordendepago",
        "LEFT JOIN bancos ON egresos.banco = bancos.id 
        LEFT JOIN ordpagos ON egresos.ordendepago = ordpagos.id 
        LEFT JOIN prv ON ordpagos.proveedor = prv.id",
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "");

$self = utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF");
$cLink = substr($self, 0, strrpos($self, ".")) . "e.php";
$cLinkd = substr($self, 0, strrpos($self, ".")) . "d.php";
$rLink = $session->getSessionAttribute("returnLink");


?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        

        <script>
            $(document).ready(function () {

                $("#autocomplete").val("<?= $busca ?>");
                $("#autocomplete").focus();
            });
        </script>
        <?php $paginador->script(); ?>
    </head>
    <body>

        <?php BordeSuperior(); ?>

        <div id="TablaDatos">
            <table aria-hidden="true">
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar"), array("Cotizacion", "Fin")); ?>
                <tbody>
                    <?php
                    while ($paginador->next()) {
                        $row = $paginador->getDataRow();
                        ?>
                        <tr>
                            <td class="centrar">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href="<?= $cLink . "?busca=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-edit"></i></a>
                                <?php else: ?>
                                    <a href="<?= $rLink . "?busca=NUEVO&Egreso=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>                            
                            <?php echo $paginador->formatRow(); ?>
                            <td class="centrar">
                                <?php if ($row["ordendepago"] > 0) { ?>
                                    <?php if (abs($row["importe"] - ($row["pagoreal"] + $row["otropago"])) > 10) { ?>
                                        <img src="lib/amarillo.png" title="Diferencia entre cotizacion y pago">
                                    <?php } else { ?>
                                        <img src="lib/verde.png" title="El pago corresponde a lo cotizado">
                                    <?php } ?>
                                <?php } else { ?>
                                    <img src="lib/rojo.png" title="El movimiento ha sido cancelado">
                                <?php } ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["ordendepago"] > 0) { ?>
                                    <?php if ($row["proveedorde"] === "Servicios") { ?>
                                        <img src="lib/check.gif" title="El pago es un servicio">
                                    <?php } elseif ($row["entradaid"] > 0) { ?>
                                        <img src="lib/check.gif" title="Ya se recibio productos">
                                    <?php } else { ?>
                                        <img src="lib/amarillo.png">
                                    <?php } ?>
                                <?php } else { ?>
                                    <img src="lib/rojo.png" title="El movimiento ha sido cancelado">
                                <?php } ?>
                            </td>                       
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php
        $nLink = array();
        if (!empty($session->getSessionAttribute("backLink"))) {
            $nLink["<i class=\"icon fa fa-lg fa-arrow-circle-left\" aria-hidden=\"true\"></i> Regresar"] = $session->getSessionAttribute("backLink");
        }
        echo $paginador->footer(empty($session->getSessionAttribute("returnLink")), $nLink, true, true);
        echo $paginador->filter();
        BordeSuperiorCerrar();
        ?>

    </body>
</html>