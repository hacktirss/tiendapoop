<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

include_once ("service/NotaSalidaService.php");

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();
$UsuarioSesion = getSessionUsuario();

$Id = 100;
$Titulo = "Nota de salida de equipos y producto detalle";

$nameSession = "moduloSalidasEqDetalle";
$arrayFilter = array();
$session = new PaginadorSession("nmd.idnvo", "nmd.idnvo", $nameSession, $arrayFilter, "criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$conditions = " nmd.id = '$cValVar' AND nmd.id > 0 ";

$paginador = new Paginador($Id,
        "nmd.marca, IF(nmd.producto > 0 AND nmd.tipo = 1, inv.descripcion,nmd.modelo) modelo, 
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
        "nota_pro_s.php",
        "nsd nmd");

$objectVO = new NotaSalidaVO();
if (is_numeric($cValVar)) {
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
} else {
    $objectVO->setFecha(date("Y-m-d"));
}

$selectAllEquipos = "SELECT equipos.* FROM equipos
                    WHERE TRUE AND equipos.id NOT IN 
                    (SELECT nsd.producto FROM nsd WHERE TRUE AND nsd.id = $cValVar AND nsd.tipo = " . TipoNotaSalida::EQUIPO . ");";
$listEquipos = $equipoDAO->getAll($selectAllEquipos);

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
                $("#Fecha").val("<?= $objectVO->getFecha() ?>");
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>");
                $("#Factura").val("<?= $objectVO->getFactura() ?>");
                $("#Cliente").val("<?= $objectVO->getCliente() ?>");
                $("#Importe").html("<?= number_format($objectVO->getDetalle(), 2) ?>");

                $("#autocompleteP").focus();
                $("#autocompleteP").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', descripcion, ' | ' , precio) value FROM inv WHERE inv.cia = " + cia,
                        "descripcion"
                        );

                //$("#autocompleteE").focus();
                $("#autocompleteE").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', marca, ' | ' , modelo , ' | ' , numero_serie) value FROM equipos WHERE equipos.cia = " + cia,
                        "marca"
                        );


                $("#BotonD").click(function (e) {

                });
                
                $("#DataEquipo").change(function(){
                    var texto = $(this).val();
                    var value = texto.split("|");
                    console.log($.trim(value[0]));
                    $("#Equipo").val($.trim(value[0]));
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
                                            <td class="titulos" colspan="100%">Datos de la nota de salida</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-1"><span id="Id"></span></div>
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
                                                        <div class="col-md-3 align-right">Cliente:</div>
                                                        <div class="col-md-9">
                                                            <?php ListasCatalogo::getClientes("Cliente", " required='required'"); ?>
                                                        </div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Num. Factura:</div>
                                                        <div class="col-3"><input type="text" name="Factura" id="Factura" onkeyup="mayus(this);" placeholder="F-000"></div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe de la salida:</div>
                                                        <div class="col-3">$ <span id="Importe"></span></div>
                                                    </div>  

                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Observaciones:</div>
                                                        <div class="col-9">
                                                            <textarea name="Observaciones" cols="65" rows="6"><?= $objectVO->getObservaciones() ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="row no-gutters">
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-3 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-3"></div>
                                                        <?php if ($objectVO->getStatus() === StatusNotaSalida::ABIERTA && is_numeric($cValVar)) : ?>
                                                            <div class="col-md-3 align-center"><input type="submit" name="Boton" value="Cancelar nota"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php if ($objectVO->getStatus() === StatusNotaSalida::CERRADA) : ?>
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

                                <?php regresar("nota_pro_s.php") ?>
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
                                                                        <?php if ($objectVO->getStatus() === StatusNotaSalida::ABIERTA && is_numeric($cValVar)) : ?>
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

                                        <?php if ($objectVO->getStatus() === StatusNotaSalida::ABIERTA && is_numeric($cValVar)) : ?>

                                            <tr>
                                                <td class="titulos" colspan="100%">Agregar producto</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">

                                                        <?php
                                                        if ($request->hasAttribute("BotonP") && $request->getAttribute("BotonP") === utils\Messages::OP_NO_OPERATION_VALID) :
                                                            $Producto = trim(explode("|", $request->getAttribute("Producto"))[0]);
                                                            $productoDAO = new ProductoDAO();
                                                            $productoVO = $productoDAO->retrieve($Producto, "id", $UsuarioSesion->getCia());
                                                            ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Producto:</div>
                                                                <div class="col-7"><span><?= $productoVO->getDescripcion() ?></span></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><a class="enlaces" href="nota_pro_se.php"> cancelar </a></div>
                                                            </div>

                                                            <div class="row no-gutters">
                                                                <div class="col-3 align-right">Cantidad:</div>
                                                                <div class="col-1"><input type="text" name="Cnt" min="1" value="1"></div>                                                                    
                                                                <div class="col-3 align-right">Precio unitario:</div>
                                                                <div class="col-1"><input type="text" name="Costo" placeholder=" $" value="<?= $productoVO->getPrecio() ?>"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-3"><input type="submit" name="BotonP" id="BotonP" value="Agregar"></div>
                                                            </div>                                                                                                                         
                                                            <input type="hidden" name="Producto" value="<?= $Producto ?>">
                                                        <?php else : ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Producto:</div>
                                                                <div class="col-8">
                                                                    <input type="text" placeholder="Producto a buscar por descripcion" name="Producto" id="autocompleteP">
                                                                    <div id="autocomplete-suggestions"></div>
                                                                </div>
                                                                <div class="col-2"><input type="submit" name="BotonP" value="Enviar"></div>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="titulos" colspan="100%">Agregar Equipos</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">

                                                        <?php
                                                        if ($request->hasAttribute("BotonE") && $request->getAttribute("BotonE") === utils\Messages::OP_NO_OPERATION_VALID) :
                                                            $Equipo = trim(explode("|", $request->getAttribute("Equipo"))[0]);
                                                            $equipoDAO = new EquipoDAO();
                                                            $equipoVO = $equipoDAO->retrieve($Equipo, "id", $UsuarioSesion->getCia());
                                                            ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Equipo:</div>
                                                                <div class="col-7"><span><?= $equipoVO->getId() . " | " . $equipoVO->getMarca() . " | " . $equipoVO->getModelo() ?></span></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-2"><a class="enlaces" href="nota_pro_se.php"> cancelar </a></div>
                                                            </div>

                                                            <div class="row no-gutters">                                    
                                                                <div class="col-3 align-right">Precio unitario:</div>
                                                                <div class="col-3"><input type="text" name="Costo" placeholder=" $" value="<?= $equipoVO->getPrecio() ?>"></div>
                                                                <div class="col-1"></div>
                                                                <div class="col-3"><input type="submit" name="BotonE" id="BotonE" value="Agregar"></div>
                                                            </div>                                                                                                                         
                                                            <input type="hidden" name="Equipo" value="<?= $Equipo ?>">
                                                            <input type="hidden" name="Cnt" value="1">
                                                        <?php else : ?>
                                                            <div class="row no-gutters">
                                                                <div class="col-2 align-right">Equipo:</div>                                                                
                                                                <div class="col-8">
                                                                    <input list="listaequipos" placeholder="Equipo a buscar" name="Equipo" id="Equipo">
                                                                    <datalist id="listaequipos">
                                                                        <?php
                                                                        foreach ($listEquipos as $equipoVO) :
                                                                            if ($equipoVO instanceof EquipoVO) :
                                                                                $label =  $equipoVO->getId() . " | " . $equipoVO->getModelo() . " | " . $equipoVO->getMarca();
                                                                                ?>
                                                                                <option value="<?= $label ?>" data-id="<?= $equipoVO->getId() ?>"/>
                                                                                <?php
                                                                            endif;
                                                                        endforeach;
                                                                        ?>
                                                                    </datalist>
                                                                </div>
                                                                <div class="col-2"><input type="submit" name="BotonE" value="Enviar"></div>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td colspan="100%">
                                                <p style="text-align: center;color: red; font-weight: bold;"><?= $Msj ?></p>
                                            </td>
                                        </tr>  
                                        <?php if ($objectVO->getStatus() === StatusNotaSalida::ABIERTA && is_numeric($cValVar)) : ?>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <?php if ($objectVO->getDetalle() > 0) : ?>
                                                                <div class="col-12">
                                                                    <a class="enlaces" href="nota_pro_se.php?op=cdr">
                                                                        <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                        NOTA CUADRADA! CLICK AQUI PARA CERRARLA
                                                                        <i class="icon fa fa-flag parpadea" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
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
        </form>

        <?php BordeSuperiorCerrar(); ?>

    </body>
</html> 
