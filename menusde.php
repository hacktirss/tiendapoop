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

$Titulo = "Detalle de submenu";

require_once "./service/MenuService.php";

$objectVO = new SubmenuVO();
if (is_numeric($busca)) {
    $objectVO = $submenuDAO->retrieve($busca);
} else {
    $objectVO->setPermisos(1);
    $objectVO->setPosicion(0);
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
                                            <td class="titulos" colspan="100%">Datos del submenu</td>
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
                                                        <div class="col-9"><input type="text" name="Submenu" id="Submenu" placeholder=""></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Direccion:</div>
                                                        <div class="col-9"><input type="text" name="Direccion" id="Direccion" value="<?= $objectVO->getUrl()?>"></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Posicion:</div>
                                                        <div class="col-3"><input type="number" name="Posicion" id="Posicion" min="0"></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Permisos:</div>
                                                        <div class="col-3">
                                                            <select name="Permisos" id="Permisos">
                                                                <option value="0">N/A</option>
                                                                <option value="1">Privado</option>
                                                                <option value="2">Público</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="BotonD" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <?php regresar("menusd.php") ?>
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
                $("#Submenu").val("<?= $objectVO->getSubmenu()?>").prop("required", "required");
                $("#Posicion").val("<?= $objectVO->getPosicion() ?>").prop("required", "required");
                $("#Permisos").val("<?= $objectVO->getPermisos() ?>").prop("required", "required");

                $("#Submenu").focus();
            });
        </script>
    </body>
</html>


