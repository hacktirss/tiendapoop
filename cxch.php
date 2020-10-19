<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

include_once ("service/ReportesService.php");

$usuarioSesion = getSessionUsuario();

$Titulo = "Estado de cuenta historico del " . $FechaI . " al " . $FechaF;

//error_log($selectCxc);
$registros = utils\ConnectionUtils::getRowsFromQuery($selectCxcH, conectarse());
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <link type="text/css" rel="stylesheet" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>

        <script>
            $(document).ready(function () {
                var cia = "<?= $usuarioSesion->getCia() ?>";
                var orden = "<?= $Orden ?>";
                
                $("#autocomplete").activeComboBox(
                        $("[name='form1']"),
                        "SELECT id as data, CONCAT(id, \' | \', nombre) value FROM cli WHERE cli.cia = " + cia,
                        "nombre"
                        );
                $("#autocomplete").val("<?= html_entity_decode($SCliente) ?>");
                $("#FechaI").val("<?= $FechaI ?>").attr("size", "10");
                $("#FechaF").val("<?= $FechaF ?>").attr("size", "10");
                $("input[name=Orden][value=" + orden + "]").attr("checked", "checked");

                $("#autocomplete").prop("placeholder", " Cliente a buscar");
                $("#autocomplete").focus();

            });

            function Confirma() {
                return confirm('ATENCION!!! enviaras todos los movimientos saldados a historico, sin afectar tu saldo actual, favor de confirmar...');
            }
            function ConfirmaD() {
                return confirm('Favor de confirmar la operacion?');
            }
        </script>
    </head>

    <body>

        <?php BordeSuperior() ?>

        <form name="form1" method="post" action="">
            <div id="Formularios">
                <table aria-hidden="true">
                    <tbody>
                        <tr>
                            <td colspan="100%"> 
                                <table aria-hidden="true">
                                    <tbody>
                                        <tr>
                                            <td colspan="100%">  
                                                <div class="container" style="width: 100%">
                                                    <div class="row no-gutters">
                                                        <div class="col-9 align-right">
                                                            <div style="position: relative;">
                                                                <input type="search" name="SCliente" id="autocomplete" onClick="this.select();" value="<?= $SCliente ?>">                                                                
                                                            </div>
                                                            <div id="autocomplete-suggestions"></div>
                                                        </div>
                                                        <div class="col-1"><span>Ordenado por</span></div>
                                                        <div class="col-1"><span><input type="radio" name="Orden" value="referencia" onChange=submit();></span><label>Ref.</label></div>
                                                        <div class="col-1"><span><input type="radio" name="Orden" value="fecha" onChange=submit(); ></span><label>Fecha</label></div>
                                                    </div>   
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-1 align-right">F.inicio:</div>
                                                        <div class="col-1"><input type="date" name="FechaI" id="FechaI"></div>
                                                        <div class="col-1 align-right">F.final:</div>
                                                        <div class="col-1"><input type="date" name="FechaF" id="FechaF"></div>
                                                        <div class="col-1"></div>
                                                        <div class="col-1"><input type="submit" name="Boton" value="Enviar"></div>
                                                    </div>
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
        </form>


        <div id="TablasContainer">

            <table aria-hidden="true">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Fecha</td>
                        <td>Referencia </td>
                        <td>Nota Credito </td>
                        <td>Concepto</td>
                        <td>Cargo</td>
                        <td>Abono</td>
                        <td>Saldo</td>
                        <td></td>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $Saldo = $Cargo = $Abono = $nRng = 0;
                    foreach ($registros as $registro) :
                        $Cargo += ($registro["tm"] == "C" ? $registro["importe"] : 0);
                        $Abono += ($registro["tm"] == "H" ? $registro["importe"] : 0);
                        $MovA = $registro["tm"] == "C" ? 0 : $registro["importe"];
                        $MovC = $registro["tm"] == "C" ? $registro["importe"] : 0;
                        $cRef = $registro["tm"] == "C" ? $registro["factura"] : "";
                        $nImpRefCar = $registro["tm"] == "C" ? $registro["importe"] : 0;
                        if ($registro["tm"] == "H" && $registro["factura"] === $cRef) {
                            $nImpRefCar = $nImpRefCar - $registro["importe"];
                        }
                        ?>
                        <tr>
                            <td><?= number_format( ++$nRng) ?></td>
                            <td><?= $registro["fecha"] ?></td>
                            <td><?= $registro["factura"] ?></td>
                            <td><?= $registro["folio"] === "0" ? $registro["folio"] : $registro["referencia"] ?></td>
                            <td><?= strtoupper($registro["concepto"]) ?></td>
                            <td class="numero"><?= number_format($MovC, 2) ?></td>
                            <td class="numero"><?= number_format($MovA, 2) ?></td>
                            <td class="numero"><?= abs($nImpRefCar) < 1 ? number_format($Cargo - $Abono, 2) : number_format($Cargo - $Abono, 2) ?></td>
                            <td class="numero"><?= abs($nImpRefCar) < 1 ? "<i class='icon fa fa-lg fa-check-square'></i>" : "" ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">Totales</td>
                        <td><?= number_format($Cargo, 2) ?></td>
                        <td><?= number_format($Abono, 2) ?></td>
                        <td><?= number_format($Cargo - $Abono, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php BordeSuperiorCerrar(); ?>

    </body>

</html>
