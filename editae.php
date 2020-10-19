<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

include_once ("service/CxcService.php");

$Titulo = "Agregar movimiento manual";

$objectVO = new CxcVO();
$Cpo = array();
if (is_numeric($busca)) {
    $objectVO = $objectDAO->retrieve($busca, "id", $UsuarioSesion->getCia());
    $Titulo = "Detalle de movimiento: " . $objectVO->getId();
    $cliVO = $cliDAO->retrieve($objectVO->getCuenta(), "id", $UsuarioSesion->getCia());
    $objectVO->setCuenta($cliVO->getId() . " | " . $cliVO->getNombre());
} else {
    $objectVO->setFecha(date("Y-m-d"));
    $objectVO->setFechav(date("Y-m-d"));
}
?>


<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
        <script>
            $(document).ready(function () {
                var busca = "<?= $busca ?>";
                var cia = "<?= $UsuarioSesion->getCia() ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, ' | ', nombre) value FROM cli WHERE cli.cia = " + cia,
                        "nombre"
                        );

                $("#Id").html("<?= $busca ?>");
                $("#autocomplete").val("<?= $objectVO->getCuenta() ?>");
                $("#Referencia").val("<?= $objectVO->getReferencia() ?>").prop("placeholder"," F-00000");
                $("#Fecha").val("<?= $objectVO->getFecha() ?>");
                $("#Fechav").val("<?= $objectVO->getFechav() ?>");
                $("#Concepto").val("<?= $objectVO->getConcepto() ?>");
                $("#Importe").val("<?= $objectVO->getImporte() ?>");
                $("#Tm").val("<?= $objectVO->getTm() ?>");

                $("#autocomplete").focus();
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
                                            <td class="titulos" colspan="100%">Datos del movimiento</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>          
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Cliente:</div>
                                                        <div class="col-9">
                                                            <div style="position: relative;">
                                                                <input type="search" name="Cliente" id="autocomplete" onClick="this.select();" value="<?= $SCliente ?>">                                                                
                                                            </div>
                                                            <div id="autocomplete-suggestions"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-3 align-right">Aplicacion:</div>
                                                        <div class="col-md-3"><input type="date" name="Fecha" id="Fecha"></div>
                                                        <div class="col-md-3 align-right">Vencimiento:</div>
                                                        <div class="col-md-3"><input type="date" name="Fechav" id="Fechav"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Concepto:</div>
                                                        <div class="col-9"><input type="text" name="Concepto" id="Concepto"  onkeyup="mayus(this);"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Referencia:</div>
                                                        <div class="col-3"><input type="text" name="Referencia" id="Referencia"  onkeyup="mayus(this);"></div>
                                                        <div class="col-6 align-left">Factura o Nota de crédito:</div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Importe:</div>
                                                        <div class="col-3"><input type="text" name="Importe" id="Importe"></div>
                                                        <div class="col-3 align-right">Tipo de Movto.:</div>
                                                        <div class="col-3">
                                                            <select name="Tm" id="Tm">
                                                                <option value="C" label="Cargo"/>
                                                                <option value="H" label="Abono"/>
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

                                <?php regresar("clientes.php") ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos de ...</td>
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
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
        </form>

        <?php BordeSuperiorCerrar(); ?>
    </body>
</html> 

