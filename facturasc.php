<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

include_once ("service/FacturacionCService.php");

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();
$nameSession = "moduloFacturacionDetalle";
$arrayFilter = array();
$session = new PaginadorSession("fcd.idnvo", "fcd.idnvo", $nameSession, $arrayFilter, "criteria");

$conditions = " fcd.id = '$busca'";

$paginador = new Paginador($Id,
        "fcd.idnvo",
        "LEFT JOIN inv ON fcd.producto = inv.id",
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "",
        $Tablad . " AS fcd");
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
                $("#busca").val(busca);

                $("#Id").html("<?= $busca ?>");
                $("#Fecha").html("<?= $Cpo["fecha"] ?>");
                $("#Nombre").html("<?= $Cpo["nombre"] ?>");
                $("#Rfc").html("<?= $Cpo["rfc"] ?>");
                $("#Iva").html("<?= number_format($Cpo["iva"], 2) ?>");
                $("#Importe").html("<?= number_format($Cpo["importe"], 2) ?>");
                $("#Cantidad").html("<?= number_format($Cpo["cantidad"], 0) ?>");
                $("#Status").html("<?= $Cpo["status"] ?>");

                $("#Tiporelacion").val("<?= $Cpo["tiporelacion"] ?>").prop("disabled", true);
                $("#autocomplete").focus();
                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', descripcion, ' | ' , precio) value FROM inv WHERE inv.id > 0 ",
                        "descripcion"
                        );
            });

        </script>
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
                                            <td class="titulos" colspan="100%">Datos de la factura</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-2"><span id="Id"></span></div>
                                                        <div class="col-7"></div>
                                                    </div>  

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-9"><span id="Fecha"></span></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Nombre:</div>
                                                        <div class="col-6"><span id="Nombre"></span></div>
                                                        <div class="col-1 align-right">Rfc:</div>
                                                        <div class="col-2"><span id="Rfc"></span></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Iva:</div>
                                                        <div class="col-1"><span id="Iva"></span></div>
                                                        <div class="col-3 align-right">Importe:</div>
                                                        <div class="col-1"><span id="Importe"></span></div>
                                                        <div class="col-3 align-right">Cantidad:</div>
                                                        <div class="col-1"><span id="Cantidad"></span></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status CFDI:</div>
                                                        <div class="col-9"><span id="Status"></span></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos obligatorios para poder timbrar...</td>
                                        </tr>

                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">                                                   
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">CFDIs Relacionados:</div>
                                                        <div class="col-9">
                                                            <?php
                                                            foreach ($relaciones as $relacion) {
                                                                echo "<b>" . $relacion->getUuid_relacionado() . "</b><br/>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Tipo de Relacion:</div>
                                                        <div class="col-9"><?php ComboboxTipoRelacion::generate("Tiporelacion"); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="titulos" colspan="100%">Estatus del CFDI ante el SAT</td>
                                        </tr>

                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Factura:</div>
                                                        <div class="col-9"><?= $Cpo['foliofactura'] . " (id=" . $busca . ")" ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Emisor:</div>
                                                        <div class="col-9"><?= $Cpo['emisor'] ?></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Receptor:</div>
                                                        <div class="col-9"><?= $Cpo['receptor'] ?></div>
                                                    </div>
                                                    <?php if (!empty($Cpo['uuid']) && $Cpo['uuid'] != FcDAO::SINTIMBRAR) : ?>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Folio Fiscal:</div>
                                                            <div class="col-9"><?= $Cpo['uuid'] ?></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Fecha Emision:</div>
                                                            <div class="col-9"><?= $Cpo['fecha'] ?></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Fecha Timbrado:</div>
                                                            <div class="col-9"><?= $Cpo['fecha_timbrado'] ?></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Total:</div>
                                                            <div class="col-9"><?= number_format($Cpo['cfditotal'], 2) ?></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Verificación de CFDI:</div>
                                                            <div class="col-9"><a class="enlaces" target="_BLANK" href="<?= $verificacionURL ?>">https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx</a></div>
                                                        </div>

                                                        <?php if (!empty($statusCFDI->Estado)) : ?>

                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Estatus del CFDI:</div>
                                                                <div class="col-9"><?= $statusCFDI->Estado ?></div>
                                                            </div>

                                                        <?php endif; ?>

                                                        <?php if (!empty($statusCFDI->EsCancelable)) : ?>

                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Cancelable ?:</div>
                                                                <div class="col-9">
                                                                    <?= ($statusCFDI->EsCancelable == "No Cancelable") ? $statusCFDI->EsCancelable : $statusCFDI->EsCancelable ?>
                                                                </div>
                                                            </div>

                                                        <?php endif; ?>

                                                        <?php if (!empty($statusCFDI->EstatusCancelacion)) : ?>

                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Estatus de Cancelacion:</div>
                                                                <div class="col-9"><?= $statusCFDI->EstatusCancelacion ?></div>
                                                            </div>

                                                        <?php endif; ?>

                                                    <?php else : ?>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Estado:</div>
                                                            <div class="col-9">Factura SIN TIMBRAR</div>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <?php regresar("facturas.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Operaciones con el CFDI</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">

                                                <div id="TablaDatos" style="min-height: 150px;min-width: 250px;">
                                                    <table aria-hidden="true">
                                                        <?php echo $paginador->headers(array(), array()); ?>
                                                        <tbody>
                                                            <?php
                                                            while ($paginador->next()) {
                                                                $row = $paginador->getDataRow();
                                                                ?>
                                                                <tr>
                                                                    <?php echo $paginador->formatRow(); ?>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div> 
                                            </td>
                                        </tr>


                                        <?php if ($Cpo["status"] !== StatusFacturas::CANCELADAST) : ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Cancelación del CFDI</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-12 align-center">
                                                                <?php if (!empty($statusCFDI->Estado) && contains($statusCFDI->Estado, "Cancelado")) : ?>
                                                                    El CFDI ya se ha cancelado
                                                                <?php elseif (!empty($relacionados)) : ?>
                                                                    El comprobante no es cancelable.<br/>
                                                                    Los siguientes comprobantes relacionados deben ser cancelados previamente: </br>
                                                                    <?= $relacionados ?>
                                                                <?php elseif ($Cpo["status"] !== StatusFacturas::CANCELADA || (!empty($statusCFDI->Estado) && contains($statusCFDI->Estado, "Vigente"))) : ?>
                                                                    <?php
                                                                else : echo "";
                                                                endif
                                                                ?>
                                                                <?php if (empty($Cpo["uuid"]) || $Cpo["uuid"] === FcDAO::SINTIMBRAR) : ?>
                                                                    <b>Factura sin timbrar</b><br/>
                                                                <?php endif; ?>

                                                                <?php if (!empty($statusCFDI->EsCancelable) && contains($statusCFDI->EsCancelable, "Cancelable con")) : ?>
                                                                    <b>La solicitud de cancelación se enviará al receptor para su aceptación</b><br><br/>
                                                                <?php endif; ?> 
                                                            </div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-12 align-center">
                                                                Para poder cancelar este CFDI es necesario proporcionar una clave.
                                                            </div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Clave Master:</div>
                                                            <div class="col-3 align-center"><input type="password" name="Clave" size="10" placeholder=" * * * * * * * * * " required="required"></div>
                                                            <div class="col-1"></div>
                                                            <div class="col-2 align-center"><input type="submit"  name="Boton" value="Cancelar"></div>
                                                            <div class="col-3"></div>                                                            
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-12 align-center">
                                                                <?php if (!empty($Cpo["uuid"]) && $Cpo["uuid"] !== FcDAO::SINTIMBRAR && contains($statusCFDI->Estado, "No Encontrado")) { ?>
                                                                    Nota***  El Comprobante Fiscal Digital por Internet aún no está registrado ante el SAT <br/>
                                                                    Un CFDI puede tomar hasta 72 horas después de la fecha de timbrado antes de reflejarse en los registros del SAT aún siendo válido.<br/>
                                                                    Este tiempo está definido por el SAT.
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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
