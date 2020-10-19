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

$Titulo = "Detalle de lista";

require_once "./service/ListaService.php";

$objectVO = new ListaVO();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca);
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
                                                        <div class="col-md-9"><textarea name="Descripcion" id="Descripcion"><?= $objectVO->getDescripcion() ?></textarea></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Valor por default:</div>
                                                        <div class="col-9"><input type="text" name="Default" id="Default" placeholder=""></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Longitud de los datos:</div>
                                                        <div class="col-3"><input type="number" name="Longitud" id="Longitud" min="0"></div>
                                                        <div class="col-6">Será considerado como valor máximo de captura</div>
                                                    </div> 
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Tipo de datos:</div>
                                                        <div class="col-3"><?php ListasCatalogo::listaNombreCatalogo("Tipo_dato", "TIPO DE DATOS")?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right"></div>
                                                        <div class="col-9 align-center">En caso de ser INTEGER</div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Mínimo:</div>
                                                        <div class="col-3"><input type="number" name="Min" id="Min" min="0"></div>
                                                        <div class="col-3 align-right">Máximo:</div>
                                                        <div class="col-3"><input type="number" name="Max" id="Max" min="0" max="999"></div>
                                                    </div> 
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Convertir a mayusculas:</div>
                                                        <div class="col-3"><?php ListasCatalogo::listaNombreCatalogo("Mayus", "ACTIVO")?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status:</div>
                                                        <div class="col-3"><?php ListasCatalogo::listaNombreCatalogo("Estado", "ACTIVO")?></div>
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

                                <?php regresar("listas.php") ?>
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
                $("#Default").val("<?= $objectVO->getDefault() ?>").prop("required", "required");
                $("#Longitud").val("<?= $objectVO->getLongitud() ?>").prop("required", "required");
                $("#Tipo_dato").val("<?= $objectVO->getTipo_dato() ?>").prop("required", "required");
                $("#Min").val("<?= $objectVO->getMin() ?>").prop("required", "required");
                $("#Max").val("<?= $objectVO->getMax() ?>").prop("required", "required");
                $("#Mayus").val("<?= $objectVO->isMayus() ?>").prop("required", "required");
                $("#Estado").val("<?= $objectVO->getEstado() ?>").prop("required", "required");

                $("#Nombre").focus();
            });
        </script>
    </body>
</html>


