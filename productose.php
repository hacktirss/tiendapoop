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
error_log($objectVO->getImage());
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
                $("#Menudeo").val("<?= $objectVO->getMenudeo() ?>");
                $("#Mayoreo").val("<?= $objectVO->getMayoreo() ?>");
                $("#Costo").html("<?= $objectVO->getCosto() ?>");
                $("#Costopromedio").html("<?= $objectVO->getCostopromedio() ?>");
                $("#Observaciones").val("<?= $objectVO->getObservaciones() ?>");
                $("#Existencia").val("<?= $objectVO->getExistencia() ?>");
                $("#Dlls").val("<?= $objectVO->getDlls() ?>");
                $("#Grupo").val("<?= $objectVO->getGrupo() ?>");
                $("#Activo").val("<?= $objectVO->getActivo() ?>");
                $("#Categoria").val("<?= $objectVO->getCategoria() ?>").prop("required", true);
                $("#Subcategoria").val("<?= $objectVO->getSubcategoria() ?>");
                $("#Codigo").val("<?= $objectVO->getCodigo() ?>");

            });
        </script>

    <body>

        <?php BordeSuperior(); ?> 

        <form name="form1" method="post" action="" enctype="multipart/form-data">
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
                                                        <div class="col-md-4"> <?= ListasCatalogo::getGrupos("Grupo") ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Precio menudeo $</div>
                                                        <div class="col-md-2"><input type="text" name="Menudeo" id="Menudeo"></div>
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-3 align-right">Precio mayoreo $</div>
                                                        <div class="col-md-2"><input type="text" name="Mayoreo" id="Mayoreo"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Ultimo costo:</div>
                                                        <div class="col-md-1"><span id="Costo"></span></div>
                                                        <div class="col-md-2 align-right">Costo prom:</div>
                                                        <div class="col-md-1"><span id="Costopromedio"></span></div>
                                                        <div class="col-md-3 align-right">Precio al publico $</div>
                                                        <div class="col-md-2"><input type="text" name="Precio" id="Precio"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Unida de medida:</div>
                                                        <div class="col-md-5"><?= ComboboxUnidades::generate("Umedida"); ?></div>
                                                        <div class="col-md-3 align-right">Activo:</div>
                                                        <div class="col-md-1">
                                                            <select name="Activo" id="Activo">
                                                                <option value="Si">Si</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Categoria:</div>
                                                        <div class="col-md-9"><?= ListasCatalogo::getCategorias("Categoria") ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Sub-Categoria:</div>
                                                        <div class="col-md-9"><?= ListasCatalogo::getSubCategorias("Subcategoria", $objectVO->getCategoria()) ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Código:</div>
                                                        <div class="col-5"><input type="text" name="Codigo" id="Codigo" onkeyup="mayus(this);" autofocus="true"></div>
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

                                <?php regresar("productos.php") ?>
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
                                                        <div class="col-md-3 align-right">Observaciones:</div>
                                                        <div class="col-md-9"><textarea name="Observaciones" id="Observaciones"><?= $objectVO->getObservaciones() ?></textarea></div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Cargar foto</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-md-12 align-center">
                                                            <?php if(!is_null($objectVO->getImage() &&!empty($objectVO->getImage()))): ?>
                                                            <img src="data:image/jpeg;base64,<?= base64_encode($objectVO->getImage()) ?>" class="foto" alt="Foto">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-md-12 align-center"><input type="file" name="Imagen"></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Upload" value="Cargar"></div>
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

        <script>
            $(document).ready(function () {
                $("#Categoria").change(function () {
                    $.ajax({
                        url: "getCategorias.php",
                        type: "post",
                        data: {menu: $("#Categoria").val()},
                        dataType: "json",
                        success: function (response) {
                            var len = response.length;

                            $("#Subcategoria").empty();
                            for (var i = 0; i < len; i++) {
                                var id = response[i]["id"];
                                var name = response[i]["nombre"];

                                $("#Subcategoria").append("<option value='" + id + "'>" + id + " | " + name + "</option>");
                            }
                        },
                        error: function (jqXHR, ex) {
                            console.log("Status: " + jqXHR.status);
                            console.log("Uncaught Error.\n" + jqXHR.responseText);
                            console.log(ex);
                        }
                    });
                });
            });
        </script>
    </body>
</html> 
