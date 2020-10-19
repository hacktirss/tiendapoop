<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

$Titulo = "Detalle de menu";

require_once "./service/MenuService.php";

$objectVO = new MenuVO();
if (is_numeric($busca)) {
    $objectVO = $menuDAO->retrieve($busca);
} else {
    $objectVO->setTipo(0);
    $objectVO->setOrden(0);
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
    </head>
    <body>

        <?php BordeSuperior(); ?> 

        <form name="form1" id="form1" method="post" action="" autocomplete="off">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos del menu</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Nombre:</div>
                                                        <div class="col-9"><input type="text" name="Nombre" id="Nombre" placeholder=""></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Descripcion:</div>
                                                        <div class="col-9"><input type="text" name="Descripcion" id="Descripcion" placeholder=""></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Orden:</div>
                                                        <div class="col-3"><input type="number" name="Orden" id="Orden" min="0"></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Tipo:</div>
                                                        <div class="col-3">
                                                            <select name="Tipo" id="Tipo">
                                                                <option value="0">Menu superior</option>
                                                                <option value="1">Menu lateral</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <?php regresar("menus.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos auxiliares</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-9">
                                                            <span id="FeclaveSpan"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>

        <script>
            $(document).ready(function () {
                let busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $busca ?>");
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("required", "required");
                $("#Descripcion").val("<?= $objectVO->getDescripcion() ?>").prop("required", "required");
                $("#Orden").val("<?= $objectVO->getOrden() ?>").prop("required", "required");
                $("#Tipo").val("<?= $objectVO->getTipo() ?>").prop("required", "required");

                $("#Nombre").focus();
            });
        </script>
    </body>
</html>


