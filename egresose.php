<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$link = conectarse();

$busca = $request->hasAttribute("id") ? $request->getAttribute("id") : $request->getAttribute("busca");

$Titulo = "Registro de pagos(egresos/compras)";

if ($busca === "NUEVO" && !($request->hasAttribute("Ordendepago"))) {
    header("Location: ordpagos.php?criteria=ini&returnLink=egresose.php&backLink=egresos.php");
}

require_once './service/EgresoService.php';

$objectVO = new EgresoVO;
$ordenPagoDAO = new OrdenPagoDAO();
$objectVO->setId($busca);
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $UsuarioSesion->getCia());
    $ordenPagoVO = $ordenPagoDAO->retrieve($objectVO->getOrdendepago(), "id", $UsuarioSesion->getCia());
} else {
    $ordenPagoVO = $ordenPagoDAO->retrieve($request->getAttribute("Ordendepago"));
    $objectVO->setCreacion(date("Y-m-d H:i:s"));
    $objectVO->setFecha(date("Y-m-d"));
    $objectVO->setOrdendepago($request->getAttribute("Ordendepago"));
    $objectVO->setPagoreal($ordenPagoVO->getTotal());
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
                                            <td class="titulos" colspan="100%">Datos de egreso</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>     
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha de creación:</div>
                                                        <div class="col-9"><span id="Creacion"></span></div>
                                                    </div>     
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha del deposito:</div>
                                                        <div class="col-2"><input type="text" name="Fecha" id="Fecha"></div>
                                                        <div class="col-1"><img id="cFecha" src="lib/calendar.png"></div>
                                                        <div class="col-6">Moviminento en el banco</div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Banco:</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getBancos("Banco", "required='required'"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Forma de pago:</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getFormasDePago("Formadepago"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Pago real:</div>
                                                        <div class="col-3"><input type="text" name="Pagoreal" id="Pagoreal"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Otro gasto:</div>
                                                        <div class="col-3"><input type="text" name="Otropago" id="Otropago"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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
                                    </tbody>
                                </table>

                                <?php regresar($Return) ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de la Orden</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Orden #:</div>
                                                        <div class="col-3"><input type="text" id="Orden"></div>
                                                    </div>   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-3"><input type="text" id="Orden_fecha"></div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Proveedor:</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getProveedores("Proveedor"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto:</div>
                                                        <div class="col-9"><input type="text" name="Concepto" id="Concepto"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe $:</div>
                                                        <div class="col-3"><input type="text" id="Orden_Importe"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Iva $:</div>
                                                        <div class="col-3"><input type="text" id="Orden_Iva"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Total $:</div>
                                                        <div class="col-3"><input type="text" id="Orden_Total"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Observaciones</td>
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
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
            <input type="hidden" name="Ordendepago" id="Ordendepago">
        </form>

        <?php BordeSuperiorCerrar(); ?>

        <script>
            $(document).ready(function () {
                let busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                    $("#BotonCancel").hide();
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $objectVO->getId() ?>");
                $("#Creacion").html("<?= $objectVO->getCreacion() ?>");
                $("#Fecha").val("<?= $objectVO->getFecha() ?>").prop("required", true);
                $("#Ordendepago").val("<?= $objectVO->getOrdendepago() ?>");
                $("#Banco").val("<?= $objectVO->getBanco() ?>").prop("required", true);
                $("#Formadepago").val("<?= $objectVO->getFormadepago() ?>").prop("required", true);
                $("#Observaciones").val("<?= $objectVO->getObservaciones() ?>");
                $("#Pagoreal").val("<?= $objectVO->getPagoreal() ?>").prop("required", true);
                $("#Otropago").val("<?= $objectVO->getOtropago() ?>");


                $("#Orden").val("<?= $ordenPagoVO->getId() ?>").prop("disabled", true);
                $("#Orden_fecha").val("<?= $ordenPagoVO->getFecha() ?>").prop("disabled", true);
                $("#Proveedor").val("<?= $ordenPagoVO->getProveedor() ?>").prop("disabled", true);
                $("#Concepto").val("<?= $ordenPagoVO->getConcepto() ?>").prop("disabled", true);
                $("#Orden_Importe").val("<?= $ordenPagoVO->getImporte() ?>").prop("disabled", true);
                $("#Orden_Iva").val("<?= $ordenPagoVO->getIva() ?>").prop("disabled", true);
                $("#Orden_Total").val("<?= $ordenPagoVO->getTotal() ?>").prop("disabled", true);

                $("#cFecha").css("cursor", "hand").click(function () {
                    displayCalendar($("#Fecha")[0], "yyyy-mm-dd", $(this)[0]);
                });

                $("#Impuestos").change(function () {
                    if (this.checked) {
                        let total = $("#Total").val();
                        var importe = total / 1.16;
                        $("#Importe").val(importe.toFixed(2));
                        $("#Iva").val((total - importe).toFixed(2));
                    }
                });

                $("#BotonCancel").click(function (e) {
                    if ($("#Clave").val().length < 4) {
                        e.preventDefault();
                        $("#Clave").focus();
                        alert("La clave ingresada no cumple con el mínimo de caracteres permitidos. \nMínimo 4 caracteres");
                    }
                });

                if ($("#Ordendepago").val() === "0") {
                    $("#Boton").hide();
                    $("#BotonCancel").hide();
                }

                $("#Fecha").focus();
            });
        </script>
    </body>
</html>

