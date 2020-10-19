<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FcDAO.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "catalogoFacturacion";
$arrayFilter = array("Tipo" => 1, "Paso" => 1);
$session = new PaginadorSession("fc.folio", "fc.folio", $nameSession, $arrayFilter, "Tipo");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 53;
$Titulo = "Facturacion";
$Tabla = "fc";

$conditions = "fc.cia = " . $usuarioSesion->getCia();

if ($Tipo == 1 || $request->hasAttribute("retornar")) {
    $conditions .= " AND $Tabla.tipo = 1 AND $Tabla.status IN ('" . StatusFacturas::CERRADA . "','" . StatusFacturas::CANCELADA . "')";
}

$paginador = new Paginador($Id,
        "fc.id, fc.cliente, fc.uuid, fc.status",
        "",
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "",
        $Tabla . " AS fc");

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
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar", "PDF", "XML", "Acuse"), array()); ?>
                <tbody>
                    <?php
                    while ($paginador->next()) {
                        $row = $paginador->getDataRow();
                        ?>
                        <tr>
                            <td class="centrar">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href="<?= $cLink . "?busca=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-edit"></i></a>
                                <?php elseif (!empty($session->getSessionAttribute("returnLink")) && $row["uuid"] !== FcDAO::SINTIMBRAR): ?>
                                    <a href="<?= $rLink . "?Factura=" . $row["id"] . "&importe=" . $row["total"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>

                            <td class="centrar">
                                <?php if (($row["status"] === StatusFacturas::CERRADA || $row["status"] == "Cerrada" || $row["status"] === StatusFacturas::CANCELADA) && $row["uuid"] !== FcDAO::SINTIMBRAR) : ?>
                                    <a href=javascript:winuni("descargaArchivo.php?id=<?= $row["id"] . "&file=" . $Tabla . "&type=pdf" ?>");><i class="icon fa fa-lg fa-file-pdf-o" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["uuid"] !== "-----") : ?>
                                    <a href="descargaArchivo.php?id=<?= $row["id"] . "&file=" . $Tabla . "&type=xml" ?>"><i class="icon fa fa-lg fa-file-code-o" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["status"] === StatusFacturas::CANCELADA && $row["uuid"] !== FcDAO::SINTIMBRAR) : ?>
                                    <a href=javascript:winuni("acusecanpdf.php?busca=<?= $row["id"] . "&table=" . $Tabla ?>");><i class="icon fa fa-lg fa-file-pdf-o" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>

                            <td><?= $row["folio"] ?></td>
                            <td><?= $row["fecha"] ?></td>
                            <td><?= $row["cliente"] ?></td>
                            <td><?= $row["nombre"] ?></td>
                            <td class="moneda"><?= number_format($row["cantidad"], 2) ?></td>
                            <td class="moneda"><?= number_format($row["importe"], 2) ?></td>
                            <td class="moneda"><?= number_format($row["iva"], 2) ?></td>
                            <td class="moneda"><?= number_format($row["total"], 2) ?></td>                          

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
