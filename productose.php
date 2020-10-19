<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");
include_once ("CFDIComboBoxes.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$usuarioSesion = getSessionUsuario();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

require_once ("service/ProductoService.php");

$Titulo = "Registro de producto nuevo";

$objectVO = new ProductoVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $usuarioSesion->getCia());
    $Titulo = "Detalle de producto: " . $objectVO->getDescripcion();
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
                $("#Descripcion").val("<?= $objectVO->getDescripcion() ?>");
                $("#Umedida").val("<?= $objectVO->getUmedida() ?>");
                $("#Precio").val("<?= $objectVO->getPrecio() ?>");
                $("#Costo").val("<?= $objectVO->getCosto() ?>");
                $("#Iva").val("<?= $objectVO->getIva() ?>");
                $("#Costopromedio").html("<?= $objectVO->getCostopromedio() ?>");
                $("#Observaciones").val("<?= $objectVO->getObservaciones() ?>");
                $("#Existencia").val("<?= $objectVO->getExistencia() ?>");
                $("#Dlls").val("<?= $objectVO->getDlls() ?>");
                $("#Grupo").val("<?= $objectVO->getGrupo() ?>");
                $("#Activo").val("<?= $objectVO->getActivo() ?>");
                $("#Inv_cunidad").val("<?= $objectVO->getInv_cunidad() ?>");
                $("#Inv_cproducto").val("<?= $objectVO->getInv_cproducto() ?>");

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
                                                        <div class="col-3 align-right">Descripcion:</div>
                                                        <div class="col-9"><input type="text" name="Descripcion" id="Descripcion" onkeyup="mayus(this);" autofocus="true"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Existencia:</div>
                                                        <div class="col-2"><input type="number" name="Existencia" id="Existencia" min="0"></div>
                                                        <div class="col-md-3 align-right">Grupo:</div>
                                                        <div class="col-md-4"> <?= ListasCatalogo::getGrupos("Grupo")?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Ultimo costo:</div>
                                                        <div class="col-md-1"><input type="text" name="Costo" id="Costo"></div>
                                                        <div class="col-md-2 align-right">Costo prom:</div>
                                                        <div class="col-md-1"><span id="Costopromedio"></span></div>
                                                        <div class="col-md-3 align-right">Precio $:</div>
                                                        <div class="col-md-2"><input type="text" name="Precio" id="Precio"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Unida de medida:</div>
                                                        <div class="col-md-5">
                                                            <select name="Umedida" id="Umedida">
                                                                <option value="Pzas">Pzas</option>
                                                                <option value="Lts">Lts</option>
                                                                <option value="Cajas">Cajas</option>
                                                                <option value="Mts">Mts</option>
                                                                <option value="Kgs">Kgs</option>
                                                                <option value="Servicio">Servicio</option>
                                                                <option value="Tarjeta">Tarjeta</option>
                                                                <option value="Componente">Componente</option>
                                                                <option value="No aplica">No aplica</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 align-right">Activo:</div>
                                                        <div class="col-md-1">
                                                            <select name="Activo" id="Activo">
                                                                <option value="Si">Si</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                            </td>
                                        </tr>                                       
                                    </tbody>
                                </table>

                                <?php regresar("productos.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Clasificacion necesario para poder facturar</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Unida de medida:</div>
                                                        <div class="col-md-9"><?= ComboboxUnidades::generate("Inv_cunidad");?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Clave de Producto/Servicio:</div>
                                                        <div class="col-md-9"><?= ComboboxCommonProductoServicio::generate("Inv_cproducto");?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Iva:</div>
                                                        <div class="col-md-1">
                                                            <select name="Iva" id="Iva">
                                                                <option value="1">Si</option>
                                                                <option value="0">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8"> Indica si el precio del producto será más IVA</div>
                                                    </div>   
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de operación</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">                                                    
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
