<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/UsuariosService.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "configUsuarios";
$arrayFilter = array();
$session = new PaginadorSession("a.id", "a.id", $nameSession, $arrayFilter, "");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 94;
$Titulo = "Configuracion de Usuarios";

$conditions = "a.cia = " . $usuarioSesion->getCia() . " AND a.level < " . UsuarioDAO::LEVEL_DISABLE;
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
                <?php echo $paginador->headers(array(empty($session->getSessionAttribute("returnLink")) ? "Editar" : "Seleccionar", empty($session->getSessionAttribute("returnLink")) ? "Detalle" : ""), array("Borrar")); ?>
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
                                    <a href="<?= $rLink . "?busca=NUEVO&Usuario=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href="<?= $cLinkd . "?busca=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-list-alt"></i></a>
                                <?php endif; ?>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td style="text-align: center">
                                <?php if (empty($session->getSessionAttribute("returnLink"))) : ?>
                                    <a href=javascript:borrar("<?= $row["id"] ?>","<?= $self ?>"); data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-trash"></i></a>
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
