<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "moduloReingresos";
$arrayFilter = array();
$session = new PaginadorSession("re.id", "re.id", $nameSession, $arrayFilter, "Filtro");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 88;
$Titulo = "Reingresos";

$conditions = "re.cia = " . $usuarioSesion->getCia();
if (!empty($session->getSessionAttribute("returnLink"))) {
    
}

$paginador = new Paginador($Id,
        "",
        "LEFT JOIN prv ON re.proveedor = prv.id ",
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
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar", "Reporte"), array("")); ?>
                <tbody>
                    <?php
                    while ($paginador->next()) {
                        $row = $paginador->getDataRow();
                        ?>
                        <tr>
                            <td class="centrar">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href="<?= $cLink . "?" . DETALLE . "=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-edit"></i></a>
                                <?php else: ?>
                                    <a href="<?= $rLink . "?busca=NUEVO&Salida=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="centrar">
                                <?php if ($row["status"] === "Cerrada") : ?>
                                    <a href=javascript:winuni("notaee.php?busca=<?= $row["id"] ?>");><i class="icon fa fa-lg fa-print"></i>
                                    <?php endif; ?>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td class="centrar">
                               
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