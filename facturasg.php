<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

include_once ("service/FacturacionGService.php");

$request = utils\HTTPUtils::getRequest();
$mysql = conectarse();
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Titulo = "Favor de confirmar sus datos";
$Return = "facturase.php";

$selectHe = "
        SELECT fc.id,fc.folio,fc.cliente,cli.nombre,cli.rfc,cli.correo,cli.enviarcorreo,cli.cuentaban,
        fc.importe,fc.iva,fc.ieps,fc.total,fc.iva,fc.isr,fcd.descuento,
        fc.observaciones,fc.pagos,fc.cndpago,fc.concepto, fc.usocfdi, fc.formadepago, fc.metododepago
        FROM $Tabla AS fc
        JOIN (
            SELECT SUM(descuento) descuento FROM $TablaDetalle AS fcd WHERE fcd.id = $busca
        ) fcd
        LEFT JOIN cli ON fc.cliente = cli.id AND cli.cia = " . $UsuarioSesion->getCia() . "
        WHERE fc.id = $busca";

$rows = utils\ConnectionUtils::getRowsFromQuery($selectHe, $mysql);
$He = $rows[0];
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>
        <script>

            $(document).ready(function () {
                
                $("#Id").html("<?= $He["id"]?>");
                $("#Folio").html("<?= $He["folio"]?>");
                
                $("#Nombre").val("<?= $He["nombre"] ?>").prop("readonly", true);
                $("#Cliente").val("<?= $He["cliente"] ?>");
                $("#Rfc").val("<?= $He["rfc"] ?>").prop("readonly", true);
                $("#Cndpago").val("<?= $He["cndpago"] ?>");
                $("#Cuentaban").val("<?= $He["cuentaban"] ?>");
                $("#Correo").val("<?= $He["correo"] ?>");
                $("#Enviarcorreo").val("<?= $He["enviarcorreo"] ?>");
                $("#Observaciones").val("<?= $He["observaciones"] ?>");
                $("#Concepto").val("<?= $He["concepto"] ?>");

                $("#Subtotal").html("$ <?= number_format($He["importe"], 2) ?>");
                $("#Isr").html("$ <?= number_format($He["isr"], 2) ?>");
                $("#Iva").html("$ <?= number_format($He["iva"], 2) ?>");
                $("#Ieps").html("$ <?= number_format($He["ieps"], 2) ?>");
                $("#Descuento").html("$ <?= number_format($He["importe"] * $He["descuento"] / 100, 2) ?>");
                $("#Total").html("$ <?= number_format($He["importe"] + $He["iva"] - $He["isr"] + $He["ieps"] - $He["importe"] * $He["descuento"] / 100, 2) ?>");

                $("#cuso").val("<?= $He["usocfdi"] ?>").attr("required", "true");
                $("#Formadepago").val("<?= $He["formadepago"] ?>");
                $("#Tipodepago").val("<?= $He["metododepago"] ?>");
            });
        </script>
        <?php  if ($request->hasAttribute("op") && $request->getAttribute("op") === "Genera factura") : ?>          
            <meta http-equiv="refresh" content="1;url=facturasg.php?Boton=Timbrar" />
        <?php endif;?>
    </head>

    <body>

        <?php BordeSuperior() ?>

        <form name="form1" method="post" action="">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos del cliente</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-1"><span id="Id"></span></div>
                                                        <div class="col-2 align-right">Folio:</div>
                                                        <div class="col-1"><span id="Folio"></span></div>
                                                    </div>
                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Nombre:</div>
                                                        <div class="col-6"><input type="text" name="Nombre" id="Nombre" required="required"></div>
                                                        <input type="hidden" name="Cliente" id="Cliente">
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Rfc:</div>
                                                        <div class="col-6"><input type="text" name="Rfc" id="Rfc" required="required"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Forma de pago:</div>
                                                        <div class="col-6"><?php ComboboxFormaDePago::generate("Formadepago", "required='required'"); ?></div>
                                                    </div>
                                                    
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Método de pago:</div>
                                                        <div class="col-6"><?php ComboboxMetodoDePago::generate("Tipodepago"); ?></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Condcs.de pago:</div>
                                                        <div class="col-6"><input type="text" name="Cndpago" id="Cndpago" onkeyup="mayus(this);" placeholder=" Condiciones de pago ejemplo: Parcialidades 1 de 5"></div>
                                                        <div class="col-3"><i class="icon fa fa-lg fa-exclamation-circle" title="Condiciones de pago ejemplo: Parcialidades 1 de 5"></i></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Numero de cuenta:</div>
                                                        <div class="col-3"><input type="text" name="Cuentaban" id="Cuentaban"></div>
                                                        <div class="col-6">4 ult.digitos, en caso de ser por transferencia</div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Correo electronico:</div>
                                                        <div class="col-6"><input type="email" name="Correo" id="Correo"></div>
                                                        <div class="col-2 align-right">Enviar:</div>
                                                        <div class="col-1">
                                                            <select name="Enviarcorreo" id="Enviarcorreo">
                                                                <option value="Si" label="Si"/>
                                                                <option value="No" label="No"/>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right"> Uso de CFDI:</div>
                                                        <div class="col-6"><?php ComboboxUsoCFDI::generate("cuso"); ?></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-6"><input type="text" name="Observaciones" id="Observaciones" onkeyup="mayus(this);"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto de la factura:</div>
                                                        <div class="col-6"><input type="text" name="Concepto" id="Concepto" required="required" onkeyup="mayus(this);"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" value="Guardar estos cambios"></div>
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
                                            <td class="titulos" colspan="100%">Importes del CFDI</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <?php if ($ppac->getPruebas() === "1") { ?>
                                                <div style="text-align: center" class="parpadea"><i class="icon fa fa-lg fa-exclamation-circle"></i>ALERTA FACTURANDO EN MODO DE DEMOSTRACIÓN<i class="icon fa fa-lg fa-exclamation-circle"></i></div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Sub-total:</div>
                                                        <div class="col-3"><span id="Subtotal"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Isr:</div>
                                                        <div class="col-3"><span id="Isr"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Iva:</div>
                                                        <div class="col-3"><span id="Iva"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Ieps:</div>
                                                        <div class="col-3"><span id="Ieps"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Descuento:</div>
                                                        <div class="col-3"><span id="Descuento"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Total:</div>
                                                        <div class="col-3"><span id="Total"></span></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center">
                                                            <?php if ($request->hasAttribute("op") && $request->getAttribute("op") === "Genera factura") : ?>
                                                                <div align="center"><img src="lib/working3.gif"></div>
                                                                <div align="center" class="colorDefault">Favor de esperar...</div>
                                                            <?php else: ?>
                                                                <button name="op" value="Genera factura">Timbrar CFDI</button>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-4"></div>
                                                    </div>

                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div style="color: red; text-align: center"><?= $Msj?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <?php BordeSuperiorCerrar(); ?>

    </body>
</html>
