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

require_once ("service/CliService.php");

$Titulo = "Registro de cliente nuevo";

$objectVO = new CliVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
    $Titulo = "Detalle de cliente: " . $objectVO->getNombre();
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
                $("#Rfc").val("<?= $objectVO->getRfc() ?>").prop("required", true);
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("required", true);
                $("#Alias").val("<?= $objectVO->getAlias() ?>").prop("required", true);
                $("#Direccion").val("<?= $objectVO->getDireccion() ?>");
                $("#Numeroext").val("<?= $objectVO->getNumeroext() ?>");
                $("#Numeroint").val("<?= $objectVO->getNumeroint() ?>");
                $("#Telefono").val("<?= $objectVO->getTelefono() ?>");
                $("#Colonia").val("<?= $objectVO->getColonia() ?>");
                $("#Municipio").val("<?= $objectVO->getMunicipio() ?>");
                $("#Estado").val("<?= $objectVO->getEstado() ?>");
                $("#Codigo").val("<?= $objectVO->getCodigo() ?>").prop("required", false);
                $("#Correo").val("<?= $objectVO->getCorreo() ?>");
                $("#Enviarcorreo").val("<?= $objectVO->getEnviarcorreo() ?>").prop("required", true);
                $("#Cuentaban").val("<?= $objectVO->getCuentaban() ?>");
                $("#Poliza").val("<?= $objectVO->getPoliza() ?>");
                $("#Activo").val("<?= $objectVO->getActivo() ?>").prop("required", true);             
                $("#Contacto").val("<?= $objectVO->getContacto() ?>");
                $("#Status").val("<?= $objectVO->getStatus() ?>").prop("required", true);     
                
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
                                                        <div class="col-3 align-right">RFC:</div>
                                                        <div class="col-3"><input type="text" name="Rfc" id="Rfc"  onkeyup="mayus(this);"></div>
                                                        <div class="col-2 align-right">Alias:</div>
                                                        <div class="col-4"><input type="text" name="Alias" id="Alias" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Direccion:</div>
                                                        <div class="col-9"><input type="text" name="Direccion" id="Direccion" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">No.exterior:</div>
                                                        <div class="col-md-3"><input type="text" name="Numeroext" id="Numeroext"></div>
                                                        <div class="col-md-3 align-right">No.interior:</div>
                                                        <div class="col-md-3"><input type="text" name="Numeroint" id="Numeroint"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Colonia:</div>
                                                        <div class="col-md-9"><input type="text" name="Colonia" id="Colonia" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Mpio. ó Ciudad:</div>
                                                        <div class="col-md-4"><input type="text" name="Municipio" id="Municipio" onkeyup="mayus(this);"></div>
                                                        <div class="col-md-2 align-right">Estado:</div>
                                                        <div class="col-md-3"><input type="text" name="Estado" id="Estado" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Código Postal:</div>
                                                        <div class="col-md-2"><input type="text" name="Codigo" id="Codigo" onkeyup="mayus(this);"></div>
                                                        <div class="col-4 align-right">Telefono:</div>
                                                        <div class="col-3"><input type="text" name="Telefono" id="Telefono"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Correo Electrónico:</div>
                                                        <div class="col-md-5"><input type="email" name="Correo" id="Correo"></div>
                                                        <div class="col-md-3 align-right">Enviar correo:</div>
                                                        <div class="col-md-1">
                                                            <select name="Enviarcorreo" id="Enviarcorreo">
                                                                <option value="Si">Si</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Cuenta (4 digitos):</div>
                                                        <div class="col-md-1"><input type="text" name="Cuentaban" id="Cuentaban" placeholder="***1234"></div>
                                                        <div class="col-md-3 align-right">Costo de la poliza:</div>
                                                        <div class="col-md-2"><input type="text" name="Poliza" id="Poliza"></div>
                                                        <div class="col-md-2 align-right">Activo:</div>
                                                        <div class="col-md-1">
                                                            <select name="Activo" id="Activo">
                                                                <option value="Si">Si</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Status:</div>
                                                        <div class="col-md-3">
                                                            <select name="Status" id="Status">
                                                                <option value="Activo">Activo</option>
                                                                <option value="Pasivo">Pasivo</option>
                                                                <option value="Baja">Baja</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 align-left">Rubro para separar cuentas en reportes</div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>                                       
                                    </tbody>
                                </table>

                                <?php regresar("clientes.php") ?>
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
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Contacto:</div>
                                                        <div class="col-9"><input type="text" name="Contacto" id="Contacto" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Observaciones:</div>
                                                        <div class="col-md-9"><textarea name="Observaciones" id="Observaciones"><?= $objectVO->getObservaciones() ?></textarea></div>
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
