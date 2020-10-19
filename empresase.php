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

$Titulo = "Detalle de empresa";

require_once "./service/EmpresasService.php";

$objectVO = new CiasVO();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca);
}
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>
    </head>

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
                                            <td class="titulos" colspan="100%">Datos de la compañia</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>     
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Razon social:</div>
                                                        <div class="col-9"><input type="text" name="Nombre" id="Nombre" onkeyup="mayus(this);"></div>
                                                    </div>  
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">RFC:</div>
                                                        <div class="col-3"><input type="text" name="Rfc" id="Rfc" onkeyup="mayus(this);"></div>
                                                        <div class="col-3 align-right">Alias:</div>
                                                        <div class="col-3"><input type="text" name="Alias" id="Alias" onkeyup="mayus(this);"></div>
                                                    </div>  
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Dirección:</div>
                                                        <div class="col-9"><input type="text" name="Direccion" id="Direccion" onkeyup="mayus(this);"></div>
                                                    </div>  
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right"># Exterior:</div>
                                                        <div class="col-3"><input type="text" name="Exterior" id="Exterior" onkeyup="mayus(this);"></div>
                                                        <div class="col-3 align-right"># Interior:</div>
                                                        <div class="col-3"><input type="text" name="Interior" id="Interior" onkeyup="mayus(this);"></div>
                                                    </div>  
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Colonia:</div>
                                                        <div class="col-3"><input type="text" name="Colonia" id="Colonia" onkeyup="mayus(this);"></div>
                                                        <div class="col-3 align-right">Municipio:</div>
                                                        <div class="col-3"><input type="text" name="Municipio" id="Municipio" onkeyup="mayus(this);"></div>
                                                    </div>  
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Estado:</div>
                                                        <div class="col-3"><input type="text" name="Estado" id="Estado" onkeyup="mayus(this);"></div>
                                                        <div class="col-3 align-right">Código Postal:</div>
                                                        <div class="col-3"><input type="text" name="Codigo" id="Codigo"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Correo:</div>
                                                        <div class="col-3"><input type="email" name="Correo" id="Correo"></div>
                                                        <div class="col-3 align-right">Telefono:</div>
                                                        <div class="col-3"><input type="text" name="Telefono" id="Telefono" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Contacto:</div>
                                                        <div class="col-9"><input type="text" name="Contacto" id="Contacto" onkeyup="mayus(this);"></div>
                                                    </div> 
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-9">
                                                            <textarea name="Observaciones" cols="65" rows="6"><?= $objectVO->getObservaciones() ?></textarea>
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

                                <?php regresar($Return) ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos para Timbrar</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Permitir facturación:</div>
                                                        <div class="col-3"><?php ListasCatalogo::listaNombreCatalogo("Facturacion", "ACTIVO"); ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Regimen fiscal:</div>
                                                        <div class="col-9"><?= ComboboxRegimen::generate("Regimen") ?></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Serie:</div>
                                                        <div class="col-3"><input type="text" name="Serie" id="Serie" onkeyup="mayus(this);"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Clave de certificados:</div>
                                                        <div class="col-3"><input type="text" name="Clavesat" id="Clavesat"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Iva %:</div>
                                                        <div class="col-3"><input type="text" name="Iva" id="Iva"></div>
                                                        <div class="col-3 align-right">Retencio Iva %:</div>
                                                        <div class="col-3"><input type="text" name="Retencioniva" id="Retencioniva"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Isr %:</div>
                                                        <div class="col-3"><input type="text" name="Isr" id="Isr"></div>
                                                        <div class="col-3 align-right">Ieps %:</div>
                                                        <div class="col-3"><input type="text" name="Ieps" id="Ieps"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Cargar certificados</td>
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
                
                $("#Id").html("<?= $objectVO->getId() ?>");
                $("#Nombre").val("<?= $objectVO->getNombre() ?>").prop("required",true);
                $("#Alias").val("<?= $objectVO->getAlias() ?>").prop("required",true);
                $("#Rfc").val("<?= $objectVO->getRfc() ?>").prop("required",true);
                $("#Direccion").val("<?= $objectVO->getDireccion() ?>").prop("required",true);
                $("#Exterior").val("<?= $objectVO->getNumeroext() ?>");
                $("#Interior").val("<?= $objectVO->getNumeroint() ?>");
                $("#Colonia").val("<?= $objectVO->getColonia() ?>").prop("required",true);
                $("#Municipio").val("<?= $objectVO->getMunicipio() ?>").prop("required",true);
                $("#Estado").val("<?= $objectVO->getEstado() ?>").prop("required",true);
                $("#Codigo").val("<?= $objectVO->getCodigo() ?>").prop("required",true);
                $("#Correo").val("<?= $objectVO->getCorreo() ?>");
                $("#Telefono").val("<?= $objectVO->getTelefono() ?>");
                $("#Contacto").val("<?= $objectVO->getContacto() ?>");
                $("#Facturacion").val("<?= $objectVO->getFacturacion() ?>").prop("required",true);
                $("#Regimen").val("<?= $objectVO->getRegimen() ?>").prop("required",true);
                $("#Serie").val("<?= $objectVO->getSerie() ?>");
                $("#Clavesat").val("<?= $objectVO->getClavesat() ?>").prop("required",true);
                $("#Iva").val("<?= $objectVO->getIva() ?>").prop("required",true);
                $("#Retencioniva").val("<?= $objectVO->getRetencioninva() ?>");
                $("#Isr").val("<?= $objectVO->getIsr() ?>");
                $("#Ieps").val("<?= $objectVO->getIeps() ?>");
                

            });
        </script>
    </body>
</html>

