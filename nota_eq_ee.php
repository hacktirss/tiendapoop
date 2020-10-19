<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

include_once ("service/NotaEntradaEquipoService.php");

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();
$UsuarioSesion = getSessionUsuario();

$Id = 85;
$Titulo = "Nota de entrada de equipos detalle";

$nameSession = "moduloEntradasEqDetalle";
$arrayFilter = array();
$session = new PaginadorSession("need.idnvo", "need.idnvo", $nameSession, $arrayFilter, "criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$conditions = " need.id = '$cValVar' AND need.id > 0 ";

$paginador = new Paginador($Id,
        "need.idnvo",
        "LEFT JOIN grupos ON need.grupo = grupos.id AND grupos.cia = " . $UsuarioSesion->getCia(),
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "nota_eq_e.php");

$objectVO = new NotaEntradaEquipoVO();
$egresoDAO = new EgresoDAO();
if (is_numeric($cValVar)) {
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
} else {
    $Egreso = $request->getAttribute("Egreso");
    $egresoVO = $egresoDAO->retrieve($Egreso, "id", $UsuarioSesion->getCia());
    $objectVO->setProveedor($egresoVO->getOrden_proveedor());
    $objectVO->setConcepto($egresoVO->getOrden_concepto());
    $objectVO->setImporte($egresoVO->getPagoreal());
    $objectVO->setFecha(date("Y-m-d"));
    $objectVO->setFechafac(date("Y-m-d"));
    $objectVO->setEgreso($Egreso);
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

                $("#busca").val(busca);

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $cValVar ?>");
                $("#Nota").html("<?= $objectVO->getEgreso() ?>");
                $("#Fecha").val("<?= $objectVO->getFecha() ?>");
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>");
                $("#Fechafac").val("<?= $objectVO->getFechafac() ?>");
                $("#Factura").val("<?= $objectVO->getFactura() ?>");
                $("#Proveedor").val("<?= $objectVO->getProveedor() ?>");
                $("#Importe").val("<?= number_format($objectVO->getImporte(), 2) ?>");
                $("#Egreso").val("<?= $objectVO->getEgreso() ?>");
                $("#CostoEntrada").html("<?= number_format($objectVO->getCosto_entrada(), 2) ?>");
                $("#CantidadEntrada").html("<?= $objectVO->getCantidad() ?>");

                $("#autocomplete").focus();
                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', descripcion, ' | ' , precio) value FROM inv WHERE inv.cia = " + cia,
                        "descripcion"
                        );

                $("#Precio").focus(function () {
                    var costo = Number($("#Costo").val());
                    precio = (costo + costo * .20);
                    $("#Precio").val(precio);
                });

                $("#BotonD").click(function (e) {
                    if ($("#Grupo").val() === "") {
                        e.preventDefault();
                        $("#Grupo").focus();
                        $("#Grupo").prop("required", true);
                        return false;
                    }
                    if ($("#Marca").val() === "") {
                        e.preventDefault();
                        $("#Marca").focus();
                        $("#Marca").prop("required", true);
                        return false;
                    }
                    if ($("#Modelo").val() === "") {
                        e.preventDefault();
                        $("#Modelo").focus();
                        $("#Modelo").prop("required", true);
                        return false;
                    }
                    if ($("#Serie").val() === "") {
                        e.preventDefault();
                        $("#Serie").focus();
                        $("#Serie").prop("required", true);
                        return false;
                    }
                    if ($("#Costo").val() === "") {
                        e.preventDefault();
                        $("#Costo").focus();
                        $("#Costo").prop("required", true);
                        return false;
                    }
                    if ($("#Precio").val() === "") {
                        e.preventDefault();
                        $("#Precio").focus();
                        $("#Precio").prop("required", true);
                        return false;
                    }
                });
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
                                            <td class="titulos" colspan="100%">Datos de la nota de entrada "Compra de equipo"</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-1"><span id="Id"></span></div>
                                                        <div class="col-3 align-right">Nota de egreso:</div>
                                                        <div class="col-1"><span id="Nota"></span></div>
                                                    </div>  

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Fecha:</div>
                                                        <div class="col-3"><input type="date" name="Fecha" id="Fecha"></div>
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
                                                        <div class="col-3 align-right">Importe de la factura:</div>
                                                        <div class="col-2"><input type="text" name="Importe" id="Importe" required="required"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Costo de los productos:</div>
                                                        <div class="col-1"><span id="CostoEntrada"></span></div>
                                                        <div class="col-3 align-right">Cantidad:</div>
                                                        <div class="col-1"><span id="CantidadEntrada"></span> pzs.</div>
                                                    </div>  

                                                    <div class="row no-gutters">
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-3 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-3"></div>
                                                        <?php if ($objectVO->getStatus() === StatusNotaEntrada::ABIERTA && is_numeric($cValVar)) : ?>
                                                            <div class="col-md-3 align-center"><input type="submit" name="Boton" value="Cancelar nota"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php if ($objectVO->getStatus() === StatusNotaEntrada::CERRADA) : ?>
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

                                <?php regresar("nota_eq_e.php") ?>
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
                                                                        <?php if ($objectVO->getStatus() === StatusNotaEntrada::ABIERTA && is_numeric($cValVar)) : ?>
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
                                            <td colspan="100%">
                                                <?php if ($objectVO->getStatus() === StatusNotaEntrada::ABIERTA && is_numeric($cValVar)) : ?>
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
                                                            <div class="col-2"><input type="text" name="Costo" id="Costo" placeholder=" $"></div>                                                                    
                                                            <div class="col-2 align-right">Precio sugerido:</div>
                                                            <div class="col-2"><input type="text" id="Precio" name="Precio" placeholder=" $"></div>
                                                            <div class="col-1"></div>
                                                            <div class="col-2"><input type="submit" name="BotonD" id="BotonD" value="Agregar"></div>
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
                                        <?php if ($objectVO->getStatus() === StatusNotaEntrada::ABIERTA && is_numeric($cValVar)) : ?>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <?php if ($objectVO->getDetalle() > 0 && abs($objectVO->getImporte() - $objectVO->getDetalle()) < 1) : ?>
                                                                <div class="col-12">
                                                                    <a class="enlaces" href="nota_eq_ee.php?op=cdr">
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
            <input type="hidden" name="Egreso" id="Egreso">
        </form>

        <?php BordeSuperiorCerrar(); ?>

    </body>
</html> 
