<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$link = conectarse();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

$Return = "egresos.php";
$Titulo = "Orden de pago y/o compra";

if ($busca === "NUEVO" && !($request->hasAttribute("Proveedor"))) {
    header("Location: proveedores.php?criteria=ini&returnLink=ordpagose.php&backLink=ordpagos.php");
}

require_once './service/OrdenPagoService.php';

$objectVO = new OrdenPagoVO();
if (is_numeric($busca)) {
   $objectVO = $objectDAO->retrieve($busca, "id", $UsuarioSesion->getCia());
} else {
    $objectVO->setFecha(date("Y-m-d"));
    $objectVO->setProveedor($request->getAttribute("Proveedor"));
}
?>
<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 

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
                                            <td class="titulos" colspan="100%">Datos del la orden</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-3"><input type="date" name="Fecha" id="Fecha"></div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Proveedor</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getProveedores("Proveedor"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Rubro</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::listaNombreCatalogo("Rubro", "RUBROS_PAGOS"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto:</div>
                                                        <div class="col-9"><input type="text" name="Concepto" id="Concepto" onkeyup="mayus(this);" placeholder="Motivo de la orden"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Solicito:</div>
                                                        <div class="col-9"><input type="text" name="Solicito" id="Solicito" onkeyup="mayus(this);" placeholder="Persona que lo solicita"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Cotizacion:</div>
                                                        <div class="col-3"><input type="text" name="Cotizacion" id="Cotizacion"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Total:</div>
                                                        <div class="col-2"><input type="text" name="Total" id="Total" placeholder=" Monto total"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe:</div>
                                                        <div class="col-2"><input type="text" name="Importe" id="Importe"></div>
                                                        <div class="col-1 align-right">Iva:</div>
                                                        <div class="col-2"><input type="text" name="Iva" id="Iva"></div>
                                                        <div class="col-3 align-right">Calcular iva:</div>
                                                        <div class="col-1 align-left"><button id="D_Iva" title="Calcular impuesto de iva (16% del importe)">=</button></div>
                                                    </div> 
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Retenciones Isr:</div>
                                                        <div class="col-2"><input type="text" name="Isr" id="Isr"></div>
                                                        <div class="col-1 align-right">Iva:</div>
                                                        <div class="col-2"><input type="text" name="IvaRet" id="IvaRet"></div>
                                                        <div class="col-3 align-right">Calcular retenciones:</div>
                                                        <div class="col-1 align-left"><button id="D_Retencion" title="Calcular retencion de impuestos">=</button></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Hospedaje:</div>
                                                        <div class="col-2"><input type="text" name="Hospedaje" id="Hospedaje"></div>
                                                        <div class="col-3 align-right">Calcular Hospedaje:</div>
                                                        <div class="col-1 align-left"><button id="D_Hospedaje" title="Calcular impuesto de hospedaje (3% de importe)">=</button></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status:</div>
                                                        <div class="col-2">
                                                            <select name="Status" id="Status">
                                                                <option value="Abierta">Abierta</option>
                                                                <option value="Cerrada">Cerrada</option>
                                                                <option value="Cancelada">Cancelada</option>
                                                            </select>
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
                                            <td class="titulos" colspan="100%">Datos auxiliares</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-9">
                                                            <textarea name="Observaciones" cols="65" rows="6"><?= $objectVO->getObservaciones() ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if (is_numeric($busca)): ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Cancelar</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Clave:</div>
                                                            <div class="col-6"><input type="password" name="Clave" id="Clave" placeholder="Ingresar clave de cancelación" autocomplete="new-password"></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-4 align-center"><input type="submit" name="Boton" Id="BotonCancel" value="Cancelar"></div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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
                $("#Fecha").val("<?= $objectVO->getFecha() ?>").prop("required", "required");
                $("#Proveedor").val("<?= $objectVO->getProveedor() ?>").prop("required", "required");
                $("#Rubro").val("<?= $objectVO->getRubro() ?>").prop("required", "required");
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>").prop("required", "required");
                $("#Solicito").val("<?= $objectVO->getSolicito() ?>").prop("required", "required");
                $("#Cotizacion").val("<?= $objectVO->getCotizacion() ?>").prop("required", "required");
                $("#Importe").val("<?= $objectVO->getImporte() ?>");
                $("#Iva").val("<?= $objectVO->getIva() ?>");
                $("#IvaRet").val("<?= $objectVO->getIva_ret() ?>");
                $("#Isr").val("<?= $objectVO->getIsr() ?>");
                $("#Hospedaje").val("<?= $objectVO->getHospedaje() ?>");
                $("#Total").val("<?= $objectVO->getTotal() ?>").prop("required", "required");
                $("#Status").val("<?= $objectVO->getStatus() ?>");
              

                $("#BotonCancel").click(function (e) {
                    if ($("#Clave").val().length < 4) {
                        e.preventDefault();
                        $("#Clave").focus();
                        alert("La clave ingresada no cumple con el mínimo de caracteres permitidos. \nMínimo 4 caracteres");
                    }
                });

                $("#D_Iva").click(function (e) {
                    e.preventDefault();
                    let total = $("#Total").val();
                    if (total > 0) {
                        var importe = total / 1.16;
                        $("#Importe").val(importe.toFixed(4));
                        $("#Iva").val((total - importe).toFixed(4));
                    }
                });

                $("#D_Retencion").click(function (e) {
                    e.preventDefault();
                    let importe = $("#Importe").val();
                    let iva = $("#Iva").val();
                    if (importe > 0) {
                        var isr = importe * 0.10;
                        var iva_ret = (iva / 3) * 2;
                        $("#Isr").val(isr.toFixed(4));
                        $("#IvaRet").val(iva_ret.toFixed(4));
                    }
                });

                $("#D_Hospedaje").click(function (e) {
                    e.preventDefault();
                    let importe = $("#Importe").val();
                    if (importe > 0) {
                        var hospedaje = importe * 0.03;
                        $("#Hospedaje").val(hospedaje.toFixed(4));
                    }
                });

                if ($("#Status").val() === "<?= StatusOrdenPago::CANCELADA ?>") {
                    $("#Boton").hide();
                    $("#BotonCancel").hide();
                }

                $("#Fecha").focus();
            });
        </script>
    </body>
</html>


