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

$Titulo = "Detalle de sublista";

require_once "./service/ListaService.php";

/**
 * @var ListaVO Lista de valores
 */
$listaVO = $objectDAO->retrieve($cVarVal);
//error_log(print_r($listaVO, TRUE));
$objectVO = new ListaValoresVO();
if (is_numeric($busca)) {
    $objectVO = $objectDDAO->retrieve($busca);
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
                                                        <div class="col-3 align-right">Llave o identificador:</div>
                                                        <div class="col-6">
                                                            <?php if ($listaVO->getTipo_dato() === "STRING" || $listaVO->getTipo_dato() === "TEXT"): ?>
                                                                <input type="text" name="Llave" id="Llave" maxlength="<?= $listaVO->getLongitud() ?>" <?= $listaVO->isMayus() == 1 ? " onkeyup='mayus(this);' " : "" ?>>
                                                            <?php elseif ($listaVO->getTipo_dato() === "INTEGER" || $listaVO->getTipo_dato() === "NUMBER") : ?>
                                                                <input type="number" name="Llave" id="Llave" min="<?= $listaVO->getMin() ?>" max="<?= $listaVO->getMax() ?>">
                                                            <?php else : ?>
                                                                <input type="<?= $listaVO->getTipo_dato() ?>" name="Llave" id="Llave">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Valor o etiqueta:</div>
                                                        <div class="col-6">
                                                            <?php if ($listaVO->getTipo_dato() === "STRING" || $listaVO->getTipo_dato() === "TEXT"): ?>
                                                                <input type="text" name="Valor" id="Valor" maxlength="<?= $listaVO->getLongitud() ?>" <?= $listaVO->isMayus() == 1 ? " onkeyup='mayus(this);' " : "" ?>>
                                                            <?php elseif ($listaVO->getTipo_dato() === "INTEGER" || $listaVO->getTipo_dato() === "NUMBER") : ?>
                                                                <input type="number" name="Valor" id="Valor" min="<?= $listaVO->getMin() ?>" max="<?= $listaVO->getMax() ?>">
                                                            <?php else : ?>
                                                                <input type="<?= $listaVO->getTipo_dato() ?>" name="Valor" id="Valor">
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-1"></div>
                                                        <div class="col-1"><i id="CopyValue" class="icon fa fa-lg fa-copy" title="Copiar llave en etiqueta"></i></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status:</div>
                                                        <div class="col-3"><?php ListasCatalogo::listaNombreCatalogo("Estado", "ACTIVO") ?></div>
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

                                <?php regresar("listasd.php") ?>
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
                                                        <div class="col-9"><span id=""></span></div>
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
                $("#Llave").val("<?= $objectVO->getLlave() ?>").prop("required", "required");
                $("#Valor").val("<?= $objectVO->getValor() ?>").prop("required", "required").prop("placeholder", "Se desplegara en los combos");
                $("#Estado").val("<?= $objectVO->getEstado() ?>").prop("required", "required");

                $("#CopyValue").click(function () {
                    $("#Valor").val($("#Llave").val());
                });
            });
        </script>
    </body>
</html>


