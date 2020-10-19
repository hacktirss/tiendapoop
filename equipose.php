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

require_once ("service/EquipoService.php");

$Titulo = "Registro de equipo nuevo";

$objectVO = new EquipoVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
    $Titulo = "Detalle de equipo: " . $objectVO->getDescripcion();
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
                
                $("#Boton").prop("disabled", true);

                $("#Id").html("<?= $busca ?>");
                $("#Marca").val("<?= $objectVO->getMarca() ?>");
                $("#Descripcion").val("<?= $objectVO->getDescripcion() ?>");
                $("#Grupo").val("<?= $objectVO->getGrupo() ?>");
                $("#Numero_serie").val("<?= $objectVO->getNumero_serie() ?>");
                $("#Modelo").val("<?= $objectVO->getModelo() ?>");
                $("#Costo").html("<?= number_format($objectVO->getCosto(), 2) ?>");
                $("#Precio").html("<?= number_format($objectVO->getPrecio(), 2) ?>");
                $("#Numero_entrada").html("<?= $objectVO->getNumero_entrada() ?>");

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
                                                        <div class="col-3"><span id="Id"></span></div>
                                                        <div class="col-3 align-right"># Entrada:</div>
                                                        <div class="col-3"><span id="Numero_entrada"></span></div>
                                                    </div>          
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Descripcion:</div>
                                                        <div class="col-9"><input type="text" name="Descripcion" id="Descripcion" onkeyup="mayus(this);" autofocus="true"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Marca:</div>
                                                        <div class="col-9"><input type="text" name="Marca" id="Marca" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Modelo:</div>
                                                        <div class="col-3"><input type="text" name="Modelo" id="Modelo" onkeyup="mayus(this);"></div>
                                                        <div class="col-3 align-right"># Serie:</div>
                                                        <div class="col-3"><input type="text" name="Numero_serie" id="Numero_serie" min="0"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Grupo:</div>
                                                        <div class="col-md-4"> <?= ListasCatalogo::getGrupos("Grupo") ?></div>
                                                    </div>                                
                                                </div>
                                            </td>
                                        </tr>                                       
                                    </tbody>
                                </table>

                                <?php regresar("equipos.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Importes</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Cantidad:</div>
                                                        <div class="col-md-9"><span id="Cantidad">1</span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Costo:</div>
                                                        <div class="col-md-9"><span id="Costo"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Precio sugerido:</div>
                                                        <div class="col-md-9"><span id="Precio"></span></div>
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
