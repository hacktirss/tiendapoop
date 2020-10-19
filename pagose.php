<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

require_once "./service/PagosService.php";

use com\softcoatl\utils as utils;
use com\softcoatl\cfdi\v33\validation\CFDIValidator;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();

$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Return = "pagos.php";
$Titulo = "Detalle por pago";

$objectVO = new PagoVO();
if (is_numeric($cValVar)) {
    $objectVO = $objectDAO->retrieve($cValVar);
} else {
    $objectVO->setFecha(date("Y-m-d H:i:s"));
    $objectVO->setFechap(date("Y-m-d"));
    $cliVO = $cliDAO->retrieve($request->getAttribute("Cliente"), "id", $UsuarioSesion->getCia());
    $objectVO->setCliente($cliVO->getId() . " | " . $cliVO->getNombre());
}

$selectPagos = "SELECT * FROM (
                SELECT p.id, p.idnvo, p.folio, DATE(p.fecha) fecha, p.total, p.concepto, p.importe, p.abonos, IFNULL(nc.total_nc,0) total_nc
                FROM( 
                    SELECT ingd.id,ingd.idnvo,ingd.referencia folio,fc.fecha,fc.concepto,fc.total,ingd.importe,sub.abonos
                    FROM ingd, fc, (
                        SELECT SUM(ingd.importe) abonos,fc.folio
                        FROM ingd, fc 
                        WHERE TRUE AND fc.cia = " . $UsuarioSesion->getCia() . "
                        AND ingd.referencia = fc.folio
                        GROUP BY ingd.referencia
                    ) AS sub
                    WHERE TRUE
                    AND fc.cia = " . $UsuarioSesion->getCia() . "
                    AND ingd.referencia = fc.folio
                    AND ingd.referencia = sub.folio
                    GROUP BY ingd.idnvo
                ) p LEFT JOIN (
                    SELECT factura,total total_nc FROM nc WHERE status = 'Cerrada' AND nc.cia = " . $UsuarioSesion->getCia() . "
                ) nc ON p.folio = nc.factura
                WHERE p.id = $cValVar 
                GROUP BY p.folio,IFNULL(nc.total_nc,0) DESC
            ) pagose
            GROUP BY pagose.folio 
            UNION 
            SELECT ing.id,ingd.idnvo,ingd.referencia folio,ing.fecha,ingd.importe,cxc.concepto,ingd.importe,0,0 
            FROM ing, ingd, cxc 
            WHERE TRUE 
            AND ing.cia = cxc.cia AND ing.cia = " . $UsuarioSesion->getCia() . "
            AND ing.id = ingd.id AND ingd.referencia = cxc.referencia 
            AND tm='H' AND cxc.recibo = ing.id AND ing.id = '$cValVar'";
//error_log($selectPagos);
$result = utils\ConnectionUtils::getRowsFromQuery($selectPagos);

$var = utils\ConnectionUtils::execSql("SELECT IFNULL(SUM(ingd.importe),0) importe FROM ingd WHERE id = '" . $cValVar . "'");


$selectDetalle = "
                SELECT  cli.correo,cli.rfc receptor, cias.rfc emisor, IFNULL( ing.uuid, '-----' ) uuid, 
                facturas.fecha_emision fecha, facturas.fecha_timbrado, ing.statusCFDI, ing.status, ing.cuenta, 
                ing.fecha fechacaptura, ing.fechap fechapago, ing.id foliopago, ing.concepto, ing.referencia, ing.importe, ing.formapago, ing.banco,
                IFNULL( IF( facturas.version IS NOT NULL AND facturas.version = '3.3', ExtractValue( facturas.cfdi_xml, '/cfdi:Comprobante/@Total' ), ExtractValue( facturas.cfdi_xml, '/cfdi:Comprobante/@total' ) ), ing.importe ) cfditotal,
                IFNULL( IF( facturas.version IS NOT NULL AND facturas.version = '3.3', ExtractValue( facturas.cfdi_xml, '/cfdi:Comprobante/@Sello' ), ExtractValue( facturas.cfdi_xml, '/cfdi:Comprobante/@sello' ) ), '' ) sello
                FROM cias, ing        
                JOIN cli ON ing.cuenta = cli.id AND cli.cia = " . $UsuarioSesion->getCia() . "
                LEFT JOIN facturas ON facturas.uuid = ing.uuid 
                WHERE TRUE 
                AND ing.cia = cias.id AND ing.cia = " . $UsuarioSesion->getCia() . "
                AND ing.id = '" . $cValVar . "'";

$Cpo = utils\ConnectionUtils::execSql($selectDetalle);

if ($objectVO->getUuid() !== PagoDAO::SIN_TIMBRAR) {

    $expresion = implode("&", [
        "id=" . $Cpo['uuid'],
        "re=" . $Cpo['emisor'],
        "rr=" . $Cpo['receptor'],
        "tt=" . number_format($Cpo['cfditotal'], 2, '.', ''),
        "fe=" . substr($Cpo['sello'], - 8)]);
    $statusCFDI = CFDIValidator::CallAPI($expresion);
    $verificacionURL = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?" . $expresion;
    error_log(print_r($statusCFDI, TRUE));
}
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>
        <link type="text/css" rel="stylesheet" media="screen" href="lib/predictive_styles.css"/>
        <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="lib/predictive_search.js"></script>

        <?php if ($request->hasAttribute("op") && ($request->getAttribute("op") === "CerrarTimbrar" || $request->getAttribute("op") === "Timbrar")) : ?>          
            <meta http-equiv="refresh" content="1;url=pagose.php?op=Genera" />
        <?php endif; ?>
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
                                            <td class="titulos" colspan="100%">Datos del pago</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id Pago:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha de captura:</div>
                                                        <div class="col-9"><span id="FechaSpan"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha del Pago:</div>
                                                        <div class="col-3"><input type="date" name="Fechap" id="Fechap"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Compañia:</div>
                                                        <div class="col-9">
                                                            <div style="position: relative; left: 0px; top: 0px;">
                                                                <input type="text" placeholder="Cliente a buscar" name="Cliente" id="autocomplete">
                                                            </div>
                                                            <div id="autocomplete-suggestions"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto:</div>
                                                        <div class="col-9"><input type="text" name="Concepto" id="Concepto" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Referencia:</div>
                                                        <div class="col-9"><input type="text" name="Referencia" id="Referencia" onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Forma de Pago</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getFormasDePago("Formapago"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Banco</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getBancos("Banco"); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe:</div>
                                                        <div class="col-3"><input type="text" name="Importe" id="Importe"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status del pago:</div>
                                                        <div class="col-9"><span id="StatusSpan"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if (!empty($objectVO->getUuid()) && $objectVO->getUuid() !== PagoDAO::SIN_TIMBRAR) { ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Reenvío de Archivos CFDI</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Correo electrónico:</div>
                                                            <div class="col-3"><input type="text" placeholder="Correo electronico" name="Correo" value="<?= $Cpo["correo"] ?>"/></div>
                                                            <div class="col-2"></div>
                                                            <div class="col-3"><input type="submit" name="Boton" value="Enviar correo"/></div>
                                                        </div>
                                                    </div>                                               
                                                </td>
                                            </tr>
                                        <?php } ?>                                         
                                    </tbody>
                                </table>

                                <?php regresar("pagos.php") ?>
                            </td>
                            <td>
                                <?php if (is_numeric($cValVar)) { ?>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="titulos" colspan="100%">Facturas Pagadas</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <?php if ($ppac->getPruebas() === "1") { ?>
                                                        <div style="text-align: center" class="parpadea"><i class="icon fa fa-lg fa-exclamation-circle"></i>ALERTA FACTURANDO EN MODO DE DEMOSTRACIÓN<i class="icon fa fa-lg fa-exclamation-circle"></i></div>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td  colspan="100%">
                                                    <div id="TablaDatos" style="min-height: 100px;">
                                                        <table style="width: 98%;">
                                                            <thead>
                                                                <tr class="titulo_blanco">
                                                                    <th></th>
                                                                    <th>Factura</th>
                                                                    <th>Fecha</th>
                                                                    <th>Concepto</th>
                                                                    <th>Importe</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($result as $rg) { ?>
                                                                    <tr>
                                                                        <td style="text-align: center; vertical-align: middle;">
                                                                            <?php if ($objectVO->getStatus() === StatusPago::ABIERTA) { ?>
                                                                                <a href="?change=1&cId=<?= $rg['idnvo'] ?>">cambiar abono</a></td>
                                                                        <?php } ?>
                                                                        </td>

                                                                        <td><?= $rg["folio"] ?></td>
                                                                        <td><?= $rg["fecha"] ?></td>
                                                                        <td><?= $rg["concepto"] ?></td>
                                                                        <td style="text-align: right;">
                                                                            <?php if ($request->getAttribute("change") === "1" && $request->getAttribute("cId") === $rg["idnvo"]) { ?>                                                                           
                                                                                <form name="form2" action="" method="post">
                                                                                    <input type="text" class="casilla" name="Abono" value="" placeholder="Ingresa el abono deseado" required title="Presiona Enter para asentar entrada">
                                                                                    <input type="hidden" name="cId" value="<?= $request->getAttribute("cId") ?>">
                                                                                    <input type="hidden" name="Factura" value="<?= $rg["folio"] ?>">
                                                                                </form>                                                                            
                                                                            <?php } else { ?>
                                                                                <?= $rg["importe"] ?>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td class="centrar">
                                                                            <?php if ($objectVO->getStatus() === StatusPago::ABIERTA) { ?>
                                                                                <a href="?op=del&cId=<?= $rg["idnvo"] ?>">eliminar</a></td>
                                                                        <?php } ?>
                                                                        </td>
                                                                        <?php
                                                                        $Importe += $rg["importe"];
                                                                        ?>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="6" style="text-align: right;">
                                                                        Total actual: <strong><?= number_format($Importe, 2) ?></strong>
                                                                        Diferencia: <strong><?= number_format($Cpo["importe"] - $Importe) ?></strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" style="text-align: right;font-weight: bold;">
                                                                        <?php if (is_numeric($cValVar) && $objectVO->getStatus() === StatusPago::ABIERTA && ($objectVO->getImporte() - $var["importe"]) > .5) { ?>
                                                                        <a class="enlaces" href="catcxc.php?criteria=ini&returnLink=pagose.php&backLink=pagose.php&cuenta=<?= $Cpo["cuenta"] ?>"><i class="icon fa fa-lg fa-plus-circle"></i> Agregar factura</a>
                                                                        <?php }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <?php if ($lBd) { ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-12 align-center"><img src="lib/working3.gif"></div>
                                                                <div class="col-12 align-center">Favor de esperar...</div>
                                                            </div>
                                                        <?php } else {
                                                            ?>
                                                            <?php
                                                            if ($objectVO->getStatus() === StatusPago::ABIERTA && ($objectVO->getImporte() - $var["importe"]) < .5 && ($objectVO->getImporte() - $var["importe"]) >= 0) {
                                                                ?>
                                                                <div class="row no-gutters">                                                    
                                                                    <div class="col-6 align-center"><a class="enlaces" href="?op=Cerrar">CERRAR SU PAGO</a></div>                                                    
                                                                    <div class="col-6 align-center"><a class="enlaces" href="?op=CerrarTimbrar">CERRAR Y TIMBRAR SU PAGO</a></div>
                                                                </div>
                                                            <?php } elseif ($objectVO->getStatus() === StatusPago::CERRADA && $Cpo['statusCFDI'] == "Abierto") {
                                                                ?>
                                                                <div class="row no-gutters">
                                                                    <div class="col-12 align-center"><a class="enlaces" href="?op=Timbrar&dif=Si">TIMBRAR PAGO</a></div>
                                                                </div>
                                                            <?php } elseif ($objectVO->getStatus() === StatusPago::ABIERTA) {
                                                                ?>
                                                                <div class="row no-gutters">
                                                                    <div class="col-12 align-center"><a class="enlaces" href="?op=cerrar&dif=Si">EL PAGO AÚN NO ESTA CUADRADO ¿DESEAS CERRARLO?</a></div>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <p style="text-align: center;color: red; font-weight: bold;"><?= $Msj ?></p>
                                                </td>
                                            </tr>                                            

                                            <?php if (is_numeric($cValVar)) { ?>
                                                <tr>
                                                    <td class="titulos" colspan="100%">Cancelar pago</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="100%">
                                                        <div class="container">
                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Emisor:</div>
                                                                <div class="col-9"><span><?= $Cpo['emisor'] ?></span></div>
                                                            </div>
                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Receptor:</div>
                                                                <div class="col-9"><span><?= $Cpo['receptor'] ?></span></div>
                                                            </div>
                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Folio Fiscal:</div>
                                                                <div class="col-9"><span><?= $objectVO->getUuid() ?></span></div>
                                                            </div>

                                                            <?php if ($objectVO->getUuid() !== PagoDAO::SIN_TIMBRAR) { ?>
                                                                <div class="row no-gutters">
                                                                    <div class="col-3 align-right">Fecha Timbrado:</div>
                                                                    <div class="col-9"><span><?= $Cpo['fecha_timbrado'] ?></span></div>
                                                                </div>
                                                                <div class="row no-gutters">
                                                                    <div class="col-3 align-right">Verificación de CFDI:</div>
                                                                    <div class="col-9"><span><a target="_BLANK" href="<?= $verificacionURL ?>">https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx</a></span></div>
                                                                </div>

                                                                <?php if (!empty($statusCFDI->Estado)) { ?>
                                                                    <div class="row no-gutters">
                                                                        <div class="col-3 align-right">Estatus del CFDI:</div>
                                                                        <div class="col-9"><span><?= $statusCFDI->Estado ?></span></div>
                                                                    </div>                                            
                                                                <?php } ?>
                                                                <?php if (!empty($statusCFDI->EsCancelable)) { ?>
                                                                    <div class="row no-gutters">
                                                                        <div class="col-3 align-right">Cancelable:</div>
                                                                        <?php if (contains($statusCFDI->EsCancelable, "No Cancelable")) { ?>
                                                                            <div class="col-9"><span style="font-weight: bold; color: red;"><?= $statusCFDI->EsCancelable ?></span></div>
                                                                        <?php } else { ?>
                                                                            <div class="col-9"><span><?= $statusCFDI->EsCancelable ?></span></div>
                                                                        <?php } ?>                                                
                                                                    </div>                                
                                                                <?php } ?>
                                                                <?php if (!empty($statusCFDI->EstatusCancelacion)) { ?>
                                                                    <div class="row no-gutters">
                                                                        <div class="col-3 align-right">Estatus de Cancelacion:</div>
                                                                        <div class="col-9"><span><?= $statusCFDI->EstatusCancelacion ?></span></div>
                                                                    </div>  
                                                                    <?php
                                                                }
                                                            }

                                                            if (!empty($objectVO->getUuid()) && $objectVO->getUuid() !== PagoDAO::SIN_TIMBRAR) {
                                                                if (!empty($statusCFDI->Estado) && contains($statusCFDI->Estado, "Cancelado")) {
                                                                    ?>
                                                                    <p>El CFDI ya se ha cancelado</p>
                                                                <?php } else if (!empty($statusCFDI->EsCancelable) && contains($statusCFDI->EsCancelable, "No Cancelable")) {
                                                                    ?>
                                                                    <p>El comprobante no es cancelable.</p>
                                                                    <p>Los siguientes comprobantes relacionados deben ser cancelados previamente:</p>
                                                                    <p><?= $relacionados ?></p>
                                                                    <?php
                                                                } else if ($objectVO->getStatus() !== StatusPago::CANCELADA || (!empty($statusCFDI->Estado) && contains($statusCFDI->Estado, "Vigente"))) {

                                                                    if (!empty($statusCFDI->EsCancelable) && contains($statusCFDI->EsCancelable, "Cancelable con")) {
                                                                        ?>
                                                                        <p>La solicitud de cancelación se enviará al receptor para su aceptación</p>
                                                                    <?php }
                                                                    ?> 
                                                                    <div class="row no-gutters">
                                                                        <div class="col-12 align-center">
                                                                            Para poder cancelar este CFDI es necesario proporcionar una clave.
                                                                        </div>
                                                                    </div>
                                                                    <div class="row no-gutters">
                                                                        <div class="col-3 align-right">Clave Master:</div>
                                                                        <div class="col-3 align-center"><input type="password" name="Clave" size="10" placeholder=" * * * * * * * * * "></div>
                                                                        <div class="col-1"></div>
                                                                        <div class="col-2 align-center"><input type="submit"  name="Boton" value="Cancelar"></div>
                                                                        <div class="col-3"></div>                                                            
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } elseif ($objectVO->getStatus() !== StatusPago::CANCELADA) {
                                                                ?>
                                                                <div class="row no-gutters">
                                                                    <div class="col-12 align-center">
                                                                        Para poder cancelar este CFDI es necesario proporcionar una clave.
                                                                    </div>
                                                                </div>
                                                                <div class="row no-gutters">
                                                                    <div class="col-3 align-right">Clave Master:</div>
                                                                    <div class="col-3 align-center"><input type="password" name="Clave" size="10" placeholder=" * * * * * * * * * "></div>
                                                                    <div class="col-1"></div>
                                                                    <div class="col-2 align-center"><input type="submit"  name="Boton" value="Cancelar"></div>
                                                                    <div class="col-3"></div>                                                            
                                                                </div>
                                                            <?php } else {
                                                                ?>
                                                                <div class="col-3 align-right">Pago cancelado</div>
                                                            <?php }
                                                            ?> 
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </td>
                        </tr>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>

        <script>

            $(document).ready(function () {
                var busca = "<?= $cValVar ?>";
                var status = "<?= $objectVO->getStatus() ?>";
                var cia = "<?= $UsuarioSesion->getCia() ?>";
                var disabled = false;

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#autocomplete").val("<?= $objectVO->getCliente() ?>").prop("disabled", disabled);
                    $("#Boton").val("Actualizar");
                    if (status === "<?= StatusPago::CERRADA ?>") {
                        disabled = true;
                        $("#Boton").hide();
                    }
                    if (status === "<?= StatusPago::CANCELADA ?>") {
                        $("#Boton").hide();
                        disabled = true;
                    }
                }
                $("#busca").val(busca);

                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', nombre) value FROM cli WHERE cli.cia = " + cia,
                        "nombre"
                        );

                $("#Id").html(busca);
                $("#FechaSpan").html("<?= $objectVO->getFecha() ?>");
                $("#Fechap").val("<?= $objectVO->getFechap() ?>");
                
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>").prop("disabled", disabled);
                $("#Referencia").val("<?= $objectVO->getReferencia() ?>").prop("disabled", disabled);
                $("#Formapago").val("<?= $objectVO->getFormapago() ?>").prop("disabled", disabled);
                $("#Banco").val("<?= $objectVO->getBanco() ?>");
                $("#Importe").val("<?= $objectVO->getImporte() ?>").prop("disabled", disabled);
                $("#StatusSpan").html("<?= $objectVO->getStatus() ?>");

                $("#Fechap").focus();
            });

        </script>
    </body>
</html>

