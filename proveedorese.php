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

require_once ("service/ProveedorService.php");

$Titulo = "Registro de proveedor nuevo";

$objectVO = new ProveedorVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
    $Titulo = "Detalle de proveedor: " . $objectVO->getNombre();
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
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("required", true);
                $("#Alias").val("<?= $objectVO->getAlias() ?>").prop("required", true);
                $("#Rfc").val("<?= $objectVO->getRfc() ?>").prop("required", true);
                $("#Direccion").val("<?= $objectVO->getDireccion() ?>");
                $("#Numeroint").val("<?= $objectVO->getNumeroint() ?>");
                $("#Numeroext").val("<?= $objectVO->getNumeroext() ?>");
                $("#Colonia").val("<?= $objectVO->getColonia() ?>");
                $("#Municipio").val("<?= $objectVO->getMunicipio() ?>");
                $("#Estado").val("<?= $objectVO->getEstado() ?>");
                $("#Telefono").val("<?= $objectVO->getTelefono() ?>");
                $("#Codigo").val("<?= $objectVO->getCodigo() ?>").prop("required", false);
                $("#Correo").val("<?= $objectVO->getCorreo() ?>");
                $("#Enviarcorreo").val("<?= $objectVO->getEnviarcorreo() ?>");
                $("#Cuentaban").val("<?= $objectVO->getCuentaban() ?>");
                $("#Proveedorde").val("<?= $objectVO->getProveedorde() ?>");
                $("#Activo").val("<?= $objectVO->getActivo() ?>");
                $("#Contacto").val("<?= $objectVO->getContacto() ?>");
                $("#Tipodepago").val("<?= $objectVO->getTipodepago() ?>");
                
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
                                                        <div class="col-md-3 align-right">Proveedor de:</div>
                                                        <div class="col-md-2">
                                                            <select name="Proveedorde" id="Proveedorde">
                                                                <option value="Productos">Productos</option>
                                                                <option value="Servicios">Servicios</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 align-right">Activo:</div>
                                                        <div class="col-md-1">
                                                            <select name="Activo" id="Activo">
                                                                <option value="Si">Si</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Tipo de pago:</div>
                                                        <div class="col-md-3">
                                                            <select name="Tipodepago" id="Tipodepago">
                                                                <option value="Credito">Crédito</option>
                                                                <option value="Prepago">Prepago</option>
                                                                <option value="Contado">Contado</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>                                       
                                    </tbody>
                                </table>

                                <?php regresar("proveedores.php") ?>
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
