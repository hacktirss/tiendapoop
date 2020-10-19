<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FcDAO.php");
include_once ("CFDIComboBoxes.php");
require_once ("data/RelacionesCfdiDAO.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "relacionFacturacion";
$arrayFilter = array("Tipo" => 1, "Cuenta" => $request->getAttribute("cuenta"));
$session = new PaginadorSession("fc.folio", "fc.folio", $nameSession, $arrayFilter, "Tipo");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$connection = conectarse();
$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 53;
$Titulo = "Facturas pendientes de pago";
$Tabla = "fc";

$conditions = "fc.cia = " . $usuarioSesion->getCia();

if ($Tipo == 1 || $request->hasAttribute("retornar")) {
    $conditions .= " AND $Tabla.status IN ('" . StatusFacturas::CERRADA . "')";
    $conditions .= " AND fc.cliente = $Cuenta AND cli.cia = " . $usuarioSesion->getCia();
    $conditions .= " AND c.saldo > 1";
}

$paginador = new Paginador($Id,
        "fc.id, fc.uuid, fc.status, c.*",
        "LEFT JOIN (
            SELECT cxc.cuenta cliente,cxc.referencia,cxc.fecha,cxc.tm,SUBSTRING(cxc.referencia,-5) folio,
            ROUND(SUM(IF(cxc.tm = 'H',cxc.importe * -1,cxc.importe)),2) saldo
            FROM cxc 
            WHERE cxc.cuenta = $Cuenta
            GROUP BY SUBSTRING(cxc.referencia,-5)
            ORDER BY cxc.cuenta,cxc.referencia
        ) c ON c.folio = fc.folio ",
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
                
                $("#checkall").change(function () {
                    $("input:checkbox").prop("checked", $(this).prop("checked"));
                });
            });
        </script>
        <?php $paginador->script(); ?>
    </head>
    <body>

        <?php BordeSuperior(); ?>

        <form name="formCxc" action="<?= $session->getSessionAttribute("returnLink") ?>" method="post">
            <div id="TablaDatos">
                <table aria-hidden="true">
                    <?php echo $paginador->headers(array("Seleccionar", "PDF", "XML", "Acuse"), array("Saldo", "Seleccionar <input type='checkbox' id='checkall'>")); ?>
                    <tbody>
                        <?php
                        while ($paginador->next()) {
                            $row = $paginador->getDataRow();
                            ?>
                            <tr>
                                <td class="centrar">
                                    <a href="<?= $rLink . "?Factura=" . $row["id"]?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
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
                                <td><?= $row["alias"] ?></td>
                                <td><?= $row["nombre"] ?></td>
                                <td class="moneda"><?= number_format($row["cantidad"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["importe"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["iva"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["total"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["saldo"], 2) ?></td>
                                
                                <td class="centrar"><input type="checkbox" name="Facturas[]" value="<?= $row["id"] ?>"></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div style="width: 20%; float: right;"><button name="Btn" value="Enviar"><i class="icon fa fa-lg fa-plus-circle"></i> Agregar seleccionadas</button></div>
        </form>

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