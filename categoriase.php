<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$usuarioSesion = getSessionUsuario();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

require_once ("service/CatergoriaService.php");

$Titulo = "Registro de categoria nueva";

$objectVO = new CategoriaVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
    $Titulo = "Detalle de categoria: " . $objectVO->getNombre();
}
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>
        <script>
            $(document).ready(function () {
                var busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $busca ?>");
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("maxlength", 64);

            });
        </script>

    <body>

        <?php BordeSuperior(); ?> 

        <form name="form1" method="post" action="">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos fiscales del cliente</td>
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
                                                        <div class="col-9"><input type="text" name="Nombre" id="Nombre" onkeyup="mayus(this);" autofocus="true"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Descripcion:</div>
                                                        <div class="col-9"><textarea name="Descripcion" id="Descripcion" onkeyup="mayus(this);"><?= $objectVO->getDescripcion() ?></textarea></div>
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

                                <?php regresar("categorias.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de operación</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>
    </body>
</html> 
