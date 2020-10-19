<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "CatalogoActividadesD";
$arrayFilter = array("cValVar" => $request->getAttribute("cValVar"));
$session = new PaginadorSession("actd.concepto", "actd.concepto", $nameSession, $arrayFilter, "cValVar");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 98;
$Titulo = "Detalle de actividades";

$conditions = "actd.id = $cValVar ";
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
        "actividades.php");

$self = utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF");
$cLink = substr($self, 0, strrpos($self, ".")) . "e.php";
$cLinkd = substr($self, 0, strrpos($self, ".")) . "d.php";
$rLink = $session->getSessionAttribute("returnLink");


$selectHe = "SELECT act.*,
            CASE WHEN act.periodo = 1 THEN 'DIARIA'
            WHEN act.periodo = 2 THEN 'SEMANAL' 
            WHEN act.periodo = 3 THEN 'QUINCENAL'
            WHEN act.periodo = 4 THEN 'MENSUAL'
            WHEN act.periodo = 5 THEN 'ANUAL'
            END revision FROM act WHERE id = $cValVar";
$He = utils\ConnectionUtils::execSql($selectHe);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
    </head>
    <body>

        <?php BordeSuperior(); ?>

        <div id="Encabezado">
            <table aria-hidden="true">
                <tbody>
                    <tr>
                        <td>Tarea: <span><?= $cValVar ?></span></td>
                        <td>Descripción: <span><?= $He["descripcion"] ?></span></td>
                        <td>Revision: <span><?= $He["revision"] ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="TablaDatos">
            <table aria-hidden="true">
                <?php
                echo $paginador->headers(array("Editar"), array("Borrar"));
                ?>
                <tbody>
                    <?php
                    while ($paginador->next()) {
                        $row = $paginador->getDataRow();
                        ?>
                        <tr>
                            <td style="text-align: center">
                                <a href="<?= $cLink . "?busca=" . $row["id"] ?>" data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-edit"></i></a>
                            </td>
                            <?php echo $paginador->formatRow(); ?>
                            <td style="text-align: center">
                                <a href=javascript:borrar("<?= $row["id"] ?>","<?= $self ?>"); data-id="<?= $row["id"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-trash"></i></a>
                            </td>                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php
        $nLink = array();
        echo $paginador->footer(true, $nLink, true, true);
        echo $paginador->filter();
        BordeSuperiorCerrar();
        ?>

    </body>
</html>