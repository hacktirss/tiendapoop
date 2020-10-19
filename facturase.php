<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

include_once ("service/FacturacionService.php");

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();
$UsuarioSesion = getSessionUsuario();

$nameSession = "moduloFacturacionDetalle";
$arrayFilter = array();
$session = new PaginadorSession("fcd.idnvo", "fcd.idnvo", $nameSession, $arrayFilter, "criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

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

$selectCfdi = "
        SELECT fc.id, fc.folio,fc.fecha, fc.cliente, fc.cantidad, fc.iva, fc.importe,
        cli.rfc, cli.nombre, fc.uuid, fc.concepto, fc.total, fc.status,
        IFNULL(ns.id,0) nota, fc.relacioncfdi,
        fc.tiporelacion, cli.correo
        FROM $Tabla AS fc
        LEFT JOIN cli ON fc.cliente = cli.id AND cli.cia = " . $UsuarioSesion->getCia() . "
        LEFT JOIN ns ON ns.factura = fc.id
        WHERE fc.id = '$busca'";

$Cpo = array();
if (($rg = $mysqli->query($selectCfdi))) {
    $Cpo = $rg->fetch_array();
} else {
    error_log($mysqli->error);
    error_log($selectCfdi);
}

$self = utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF");
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
        <script>
            $(document).ready(function () {
                var busca = "<?= $busca ?>";
                var cia = "<?= $UsuarioSesion->getCia() ?>";
                
                $("#busca").val(busca);

                $("#Id").html("<?= $busca ?>");
                $("#Folio").html("<?= $Cpo["folio"] ?>");
                $("#Fecha").html("<?= $Cpo["fecha"] ?>");
                $("#Nombre").html("<?= $Cpo["nombre"] ?>");
                $("#Rfc").html("<?= $Cpo["rfc"] ?>");
                $("#Iva").html("<?= number_format($Cpo["iva"], 2) ?>");
                $("#Importe").html("<?= number_format($Cpo["importe"], 2) ?>");
                $("#Cantidad").html("<?= number_format($Cpo["cantidad"], 0) ?>");
                $("#UUID").html("<?= $Cpo["uuid"] ?>");

                $("#Tiporelacion").val("<?= $Cpo["tiporelacion"] ?>").prop("disabled", true);
                $("#autocomplete").focus();
                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', descripcion, ' | ' , precio) value FROM inv WHERE inv.cia = " + cia,
                        "descripcion"
                        );
            });

            function openCfdiRelacionados() {
                window.open("cfdirelacionados.php?criteria=ini&id=<?= $busca ?>&cliente=<?= $Cpo['cliente'] ?>", "_blank", "width=1070,height=520,resizable=no,scrollbars=no");
            }
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
                                                        <div class="col-1"><span id="Id"></span></div>
                                                        <div class="col-3 align-right">Folio:</div>
                                                        <div class="col-1"><span id="Folio"></span></div>
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
                                                        <div class="col-3 align-right">UUID:</div>
                                                        <div class="col-9"><span id="UUID"></span></div>
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
                                                    <?php if ($Cpo["status"] === StatusFacturas::ABIERTA) : ?>.
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Relacionar CFDIs:</div>
                                                            <div class="col-9">
                                                                <a href="javascript:openCfdiRelacionados()"><img style="width: 20px;height: 20px;" title="Requerido para sustiuir o relacionarla con alguna factura anterior" src="lib/lupa.jpg"/></a> Sólo en caso de ser necesario
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
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


                                        <?php if ($Cpo["status"] === StatusFacturas::CERRADA || $Cpo["status"] === StatusFacturas::CANCELADA) : ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Envio de CFDI</td>
                                            </tr>

                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Correo:</div>
                                                            <div class="col-5"><input type="text" placeholder="Correo electronico..." name="Correo" value="<?= $Cpo[correo] ?>"></div>
                                                            <div class="col-1"></div>
                                                            <div class="col-3 align-right"><input type="submit" name="Boton" value="Enviar correo"></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-12"><?= $Msj?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="titulos" colspan="100%">Enviar factura a Estado de Cuenta</td>
                                            </tr>

                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-3"></div>
                                                            <div class="col-5 align-content-center"><input type="submit" name="Boton" value="Enviar a estado de cuenta"></div>
                                                            <div class="col-4"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>                                                                                                               
                                        <?php endif; ?>
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

                                                <div id="TablaDatos" style="min-height: 100px;min-width: 250px;">
                                                    <table aria-hidden="true">
                                                        <?php echo $paginador->headers(array(), array("")); ?>
                                                        <tbody>
                                                            <?php
                                                            while ($paginador->next()) {
                                                                $row = $paginador->getDataRow();
                                                                ?>
                                                                <tr>
                                                                    <?php echo $paginador->formatRow(); ?>
                                                                    <td style="text-align: center">
                                                                        <?php if ($Cpo["status"] === StatusFacturas::ABIERTA) : ?>
                                                                            <a href=javascript:borrar("<?= $row["idnvo"] ?>","<?= $self ?>"); data-id="<?= $row["idnvo"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-trash"></i></a>
                                                                        <?php endif; ?>
                                                                    </td>                            
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php if ($Cpo["status"] === StatusFacturas::ABIERTA) : ?>
                                                    <div class="container">
                                                        <?php
                                                        if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") === utils\Messages::OP_NO_OPERATION_VALID) :
                                                            $Producto = trim(explode("|", $request->getAttribute("Producto"))[0]);
                                                            $productoDAO = new ProductoDAO();
                                                            $productoVO = $productoDAO->retrieve($Producto, "id", $UsuarioSesion->getCia());
                                                            
                                                            $Precio = ($productoVO->getDlls() == 1) ? $productoVO->getPrecio() * $productoVO->getDlls()  : $productoVO->getPrecio();
                                                            ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Producto:</div>
                                                                <div class="col-7"><input type="text" name="Descripcion" value="<?= $productoVO->getDescripcion() ?>" onkeyup="mayus(this);"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><a class="enlaces" href="facturase.php"> cancelar </a></div>
                                                            </div>

                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Precio:</div>
                                                                <div class="col-2"><input type="text" name="Precio" value="<?= $productoVO->getPrecio() ?>" placeholder="  $" autofocus="true" required="required"></div>                                                                    
                                                                <div class="col-2 align-right">Descto:</div>
                                                                <div class="col-2"><input type="text" id="Descuento" name="Descuento" placeholder=" %"></div>
                                                                <div class="col-2 align-right">Cantidad:</div>
                                                                <div class="col-2"><input type="text" id="Cantidad" name="Cantidad" placeholder=" 0" required="required"></div>
                                                            </div>

                                                            <div class="row no-gutters">
                                                                <div class="col-6"></div>
                                                                <div class="col-2 align-right">Dlls:</div>
                                                                <div class="col-1 align-left"><input type="checkbox" name="Dlls" value="1" checked="<?= $productoVO->getDlls() == 1 ? "disabled" : "" ?>"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><input type="submit" name="Boton" value="Agregar"></div>
                                                            </div>                                                                                                                         
                                                            <input type="hidden" name="Producto" value="<?= $Producto ?>">
                                                        <?php else : ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-9 align-left">
                                                                    <input type="text"  placeholder="Concepto a buscar" name="Producto" id="autocomplete" onClick="this.select();" value="<?= urlencode($SProducto) ?>">
                                                                    <div id="autocomplete-suggestions"></div>
                                                                </div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><input type="submit" name="Boton" value="Enviar"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <p style="text-align: center;color: red; font-weight: bold;"><?= $Msj ?></p>
                                                </td>
                                            </tr>  

                                            <?php if ($Cpo["status"] === StatusFacturas::ABIERTA && $Cpo["importe"] > 0) : ?>
                                                <tr>
                                                    <td class="titulos" colspan="100%">Timbrar CFDI</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="100%">
                                                        <div class="container">
                                                            <div class="col-12 align-center">
                                                                <a class="enlaces" href="facturasg.php">
                                                                    <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                    CLICK AQUI PARA TIMBRAR
                                                                    <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
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
