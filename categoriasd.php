<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

require_once ("service/CatergoriaService.php");

$request = utils\HTTPUtils::getRequest();
$nameSession = "catalogoCategoriasD";
$arrayFilter = array(DETALLE => $request->getAttribute(DETALLE));
$session = new PaginadorSession("sub.id", "sub.id", $nameSession, $arrayFilter, "Filtro");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 102;
$Titulo = "Catalogo de sub-categorias";

$conditions = "sub.categoria = " . $cVarVal;
if ($Filtro !== "Todos") {
    
}

$paginador = new Paginador($Id,
        "",
        "",
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "categorias.php");

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
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar"), array(empty($session->getSessionAttribute("returnLink")) ? "Borrar" : "")); ?>
                <tbody>
                    <?php
                    while ($paginador->next()) {
                        $row = $paginador->getDataRow();
                        ?>
                        <tr>
                            <td style="text-align: center">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href="<?= $cLink . "?busca=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-edit"></i></a>
                                <?php else: ?>
                                    <a href="<?= $rLink . "?busca=NUEVO&Subcategoria=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td style="text-align: center">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href=javascript:borrarD("<?= $row["id"] ?>","<?= $self ?>"); data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-trash"></i></a>
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
