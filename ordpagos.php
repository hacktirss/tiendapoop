<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/OrdenPagoDAO.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "ordenenesPago";
$arrayFilter = array();
$session = new PaginadorSession("ordpagos.id", "ordpagos.id", $nameSession, $arrayFilter, "Filtro");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 86;
$Titulo = "Ordenes de pago y/o compras";

$conditions = "ordpagos.cia = " . $usuarioSesion->getCia();
if (!empty($session->getSessionAttribute("returnLink"))) {
    $conditions .= " AND ordpagos.pagonumero = 0 AND ordpagos.status = 'Cerrada' ";
}

$paginador = new Paginador($Id,
        "ordpagos.pagonumero, ordpagos.status",
        "LEFT JOIN prv ON ordpagos.proveedor = prv.id AND ordpagos.cia = prv.cia",
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
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar", "Reporte"), array("Status")); ?>
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
                                    <a href="<?= $rLink . "?busca=NUEVO&Ordendepago=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["status"] === "Cerrada") : ?>
                                    <a href=javascript:winuni("notaspagos.php?busca=<?= $row["id"] ?>");><i class="icon fa fa-lg fa-print"></i>
                                    <?php endif; ?>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td class="centrar">
                                <?php if ($rg[status] !== StatusOrdenPago::CANCELADA) : ?>
                                    <?php if ($rg[pagonumero] > 0) : ?>
                                        <img src="lib/verde.png" title="No.pago: <?= $rg["pagonumero"] ?>">
                                    <?php else : ?>
                                        <img src="lib/amarillo.png" title="Aun sin pagarse">
                                    <?php endif; ?>
                                <?php else : ?>
                                    <img src="lib/rojo.png" title="Movimiento cancelado">
                                <?php endif; ?>
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