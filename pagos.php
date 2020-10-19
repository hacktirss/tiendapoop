<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/PagoDAO.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "CatalogoDePago";
$arrayFilter = array();
$session = new PaginadorSession("ing.folio", "ing.folio", $nameSession, $arrayFilter, "Filtro");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 8;
$Titulo = "Registro de pagos";

$conditions = "ing.cia = " . $usuarioSesion->getCia() ." AND ing.banco = bancos.id";

$paginador = new Paginador($Id,
        "ing.status, DATE_FORMAT(ing.fechar, '%Y-%m-%d') fechaR, ing.uuid, ing.statusCFDI ",
        "JOIN cli ON ing.cuenta = cli.id",
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
                $("#Filtro").val("<?= $Filtro ?>");
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
                <?php
                echo $paginador->headers(
                        array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar", "PDF", "XML", "Acuse"),
                        array("Status"));
                ?>
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
                                    <a href="<?= $rLink . "?busca=NUEVO&Pago=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["uuid"] !== PagoDAO::SIN_TIMBRAR && !empty($row["uuid"])) : ?>                                    
                                    <a href=javascript:winuni("descargaArchivo.php?id=<?= $row["id"] ?>&file=ing&type=pdf");><i class="icon fa fa-lg fa-file-pdf-o" aria-hidden="true"></i></a>                                    
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["uuid"] !== "-----" && !empty($row["uuid"])) : ?>
                                    <a href="descargaArchivo.php?id=<?= $row["id"] ?>&file=ing&type=xml"><i class="icon fa fa-lg fa-file-code-o" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["uuid"] !== PagoDAO::SIN_TIMBRAR&& !empty($row["uuid"]) && strpos($row["statusCFDI"], "Cancelad") !== FALSE) : ?>                                    
                                    <a href=javascript:winuni("acusecanpdf.php?table=ing&busca=<?= $row["id"] ?>")><i class="icon fa fa-lg fa-file-pdf-o" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td class="centrar"><?= $row["status"] ?></td>                            
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




