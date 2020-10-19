<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

include_once ("service/NotaMultipleService.php");

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();
$UsuarioSesion = getSessionUsuario();

$Id = 100;
$Titulo = "Nota de entrada multiple";

$nameSession = "moduloEntradasMulDetalle";
$arrayFilter = array();
$session = new PaginadorSession("nmd.idnvo", "nmd.idnvo", $nameSession, $arrayFilter, "criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$conditions = " nmd.id = '$cValVar' AND nmd.id > 0 ";

$paginador = new Paginador($Id,
        " nmd.marca, IF(producto > 0, inv.descripcion,nmd.modelo) modelo, 
          nmd.numero_serie, nmd.cantidad, nmd.costo, (nmd.cantidad * nmd.costo) precio",
        "LEFT JOIN inv ON nmd.producto = inv.id AND inv.cia = " . $UsuarioSesion->getCia(),
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "nota_mul_e.php");

$objectVO = new NotaMultipleVO();
$egresoDAO = new EgresoDAO();
if (is_numeric($cValVar)) {
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
} else {
    $Egreso = $request->getAttribute("Egreso");
    $egresoVO = $egresoDAO->retrieve($Egreso, "id", $UsuarioSesion->getCia());
    $objectVO->setProveedor($egresoVO->getOrden_proveedor());
    $objectVO->setConcepto($egresoVO->getOrden_concepto());
    $objectVO->setImporte($egresoVO->getPagoreal());
    $objectVO->setFecha_entra(date("Y-m-d"));
    $objectVO->setFechafac(date("Y-m-d"));
    $objectVO->setEgreso($Egreso);
    $objectVO->setOrdpago($egresoVO->getOrdendepago());
}

$self = utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF");
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="lib/predictive_styles.css"/>
        <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="lib/predictive_search.js"></script>
        <script>
            $(document).ready(function () {
                var busca = "<?= $cValVar ?>";
                var cia = "<?= $UsuarioSesion->getCia() ?>";
                var status = "<?= $objectVO->getStatus() ?>";

                $("#busca").val(busca);

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                    if(status === "Cerrada" || status === "Cancelada") {
                        $("#Boton").hide();
                    }
                }

                $("#Id").html("<?= $cValVar ?>");
                $("#Orden").html("<?= $objectVO->getOrdpago() ?>");
                $("#Fecha_entra").val("<?= $objectVO->getFecha_entra() ?>");
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>");
                $("#Fechafac").val("<?= $objectVO->getFechafac() ?>");
                $("#Factura").val("<?= $objectVO->getFactura() ?>");
                $("#Proveedor").val("<?= $objectVO->getProveedor() ?>");
                $("#Importe").val("<?= $objectVO->getImporte() ?>");
                $("#Ordpago").val("<?= $objectVO->getOrdpago() ?>");
                $("#Egreso").val("<?= $objectVO->getEgreso() ?>");

                $("#autocomplete").focus();
                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', descripcion, ' | ' , precio) value FROM inv WHERE inv.cia = " + cia,
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
                                            <td class="titulos" colspan="100%">Datos de la nota de entrada</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-1"><span id="Id"></span></div>
                                                        <div class="col-3 align-right">Orden:</div>
                                                        <div class="col-1"><span id="Orden"></span></div>
                                                    </div>  

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-3"><input type="date" name="Fecha_entra" id="Fecha_entra"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto:</div>
                                                        <div class="col-9"><input type="text" name="Concepto" id="Concepto" onkeyup="mayus(this);" placeholder="Motivo de la orden"></div>
                                                    </div>

                                                    <div class="row no-gutters">                                               
                                                        <div class="col-md-3 align-right">Proveedor</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getProveedores("Proveedor", " required='required'"); ?>
                                                        </div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Num. Factura:</div>
                                                        <div class="col-3"><input type="text" name="Factura" id="Factura" onkeyup="mayus(this);" placeholder="F-000"></div>
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-3"><input type="date" name="Fechafac" id="Fechafac"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe:</div>
                                                        <div class="col-2"><input type="text" name="Importe" id="Importe" required="required"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-3 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-3"></div>
                                                        <?php if ($objectVO->getStatus() === StatusNotaMultiple::ABIERTA && is_numeric($cValVar)) : ?>
                                                            <div class="col-md-3 align-center"><input type="submit" name="Boton" value="Cancelar nota"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php if ($objectVO->getStatus() === StatusNotaMultiple::CERRADA) : ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Cancelar entrada</td>
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
                                                            <div class="col-md-4 align-center"><input type="submit" name="Boton" value="Cancelar"></div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                    </tbody>
                                </table>

                                <?php regresar("nota_mul_e.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Registros de la nota</td>
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
                                                                        <?php if ($objectVO->getStatus() === StatusNotaMultiple::ABIERTA && is_numeric($cValVar)) : ?>
                                                                            <a href=javascript:borrar("<?= $row["idnvo"] ?>","<?= $self ?>"); data-id="<?= $row["idnvo"] ?>"><i aria-hidden="true" class="icon fa fa-lg fa-trash"></i></a>
                                                                        <?php endif; ?>
                                                                    </td>                            
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Registros de productos</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <?php if ($objectVO->getStatus() === StatusNotaMultiple::ABIERTA && is_numeric($cValVar)) : ?>
                                                    <div class="container">
                                                        <?php
                                                        if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") === utils\Messages::OP_NO_OPERATION_VALID) :
                                                            $Producto = trim(explode("|", $request->getAttribute("Producto"))[0]);
                                                            $productoDAO = new ProductoDAO();
                                                            $productoVO = $productoDAO->retrieve($Producto, "id", $UsuarioSesion->getCia());

                                                            $Precio = ($productoVO->getDlls() == 1) ? $productoVO->getPrecio() * $productoVO->getDlls() : $productoVO->getPrecio();
                                                            ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Producto:</div>
                                                                <div class="col-7"><input type="text" name="Descripcion" value="<?= $productoVO->getDescripcion() ?>" onkeyup="mayus(this);"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><a class="enlaces" href="facturase.php"> cancelar </a></div>
                                                            </div>

                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Costo:</div>
                                                                <div class="col-2"><input type="text" name="CostoP" value="<?= $productoVO->getCosto() ?>" placeholder=" $" autofocus="true" required="required"></div>                                                                    
                                                                <div class="col-2 align-right">Cantidad:</div>
                                                                <div class="col-2"><input type="number" id="CantidadP" name="CantidadP" min="0"></div>
                                                                <div class="col-2 align-right">P. Venta:</div>
                                                                <div class="col-2"><input type="text" id="Precio" name="Precio" value="<?= $productoVO->getPrecio() ?>" placeholder=" $0.00" required="required"></div>
                                                            </div>

                                                            <div class="row no-gutters">
                                                                <div class="col-9"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><input type="submit" name="BotonP" id="BotonP" value="Agregar"></div>
                                                            </div>                                                                                                                         
                                                            <input type="hidden" name="Producto" value="<?= $Producto ?>">
                                                        <?php else : ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-9 align-left">
                                                                    <input type="text"  placeholder="Concepto a buscar" name="Producto" id="autocomplete" onClick="this.select();" value="<?= urlencode($SProducto) ?>">
                                                                    <div id="autocomplete-suggestions"></div>
                                                                </div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><input type="submit" name="BotonD" value="Enviar"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="titulos" colspan="100%">Registros de equipos</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <?php if ($objectVO->getStatus() === StatusNotaMultiple::ABIERTA && is_numeric($cValVar)) : ?>
                                                    <div class="container">

                                                        <div class="row no-gutters">
                                                            <div class="col-md-3 align-right">Grupo:</div>
                                                            <div class="col-md-4"> <?= ListasCatalogo::getGrupos("Grupo", "") ?></div>
                                                        </div>

                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Marca:</div>
                                                            <div class="col-9"><input type="text" name="Marca" id="Marca" onkeyup="mayus(this);" autofocus="true"></div>
                                                        </div>

                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Modelo y/o caracteristicas:</div>
                                                            <div class="col-9"><input type="text" name="Modelo" id="Modelo" onkeyup="mayus(this);"></div>
                                                        </div>

                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Numero de serie:</div>
                                                            <div class="col-6"><input type="text" name="Serie" id="Serie"onkeyup="mayus(this);"></div>
                                                            <div class="col-2 align-right">Cantidad:</div>
                                                            <div class="col-1"><input type="text" name="Cantidad" id="Cantidad" value="1" min="1" max="100"></div>
                                                        </div>

                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Costo:</div>
                                                            <div class="col-3"><input type="text" name="Costo" id="Costo" placeholder=" $"></div>      
                                                            <div class="col-1"></div>
                                                            <div class="col-2"><input type="submit" name="BotonE" id="BotonE" value="Agregar"></div>
                                                        </div>

                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <p style="text-align: center;color: red; font-weight: bold;"><?= $Msj ?></p>
                                            </td>
                                        </tr>  
                                        <?php if ($objectVO->getStatus() === StatusNotaMultiple::ABIERTA && is_numeric($cValVar)) : ?>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <?php if (abs($objectVO->getImporte() - $objectVO->getDetalle()) < 1) : ?>
                                                                <div class="col-12">
                                                                    <a class="enlaces" href="nota_mul_ee.php?op=cdr">
                                                                        <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                        NOTA CUADRADA! CLICK AQUI PARA CERRARLA
                                                                        <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                            <?php else : ?> 
                                                                <div class="col-12">Total al momento: <?= $objectVO->getDetalle() ?></div>
                                                            <?php endif; ?>
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
            <input type="hidden" name="Ordpago" id="Ordpago">
            <input type="hidden" name="Egreso" id="Egreso">
        </form>

        <?php BordeSuperiorCerrar(); ?>

    </body>
</html> 
