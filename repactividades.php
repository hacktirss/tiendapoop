<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check_reports.php");

use com\softcoatl\utils as utils;

require_once "./service/ReportesService.php";

$Titulo = "Relacion de actividades del $FechaI al $FechaF";

$cSql = $selectActividades;

$registros = utils\ConnectionUtils::getRowsFromQuery($cSql, conectarse());
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

        <script>
            $(document).ready(function () {
                $("#Fecha").val("<?= $Fecha ?>").attr("size", "10");                
                $("#Rubro").val("<?= $Rubro ?>");
                $("#Exportar").click(function () {
                    var instance = new TableExport($("#Reportes"), {
                        headers: true,
                        footers: true,
                        formats: ["xlsx"],
                        ignoreCSS: ".tableexport-ignore",
                        trimWhitespace: true,
                        fileName: "Relacion de facturas",
                        RTL: false,
                        bootstrap: true,
                        position: "well",
                        exportButtons: false
                    });
                    var exportData = instance.getExportData()["Reportes"]["xlsx"];
                    instance.export2file(exportData.data, exportData.mimeType, exportData.filename, exportData.fileExtension);
                });
            });
        </script>
    </head>

    <body>

        <?php EncabezadoReportes() ?>

        <div id="container">

            <div id="Reportes">
                <table width="95%">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th># Tarea</th>
                            <th>Descripcion</th>
                            <th>Revision</th>
                            <th>Cantidad</th>
                            <th>F.Última Tarea</th>
                            <th>F.Promesa</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nRng = 1;
                        foreach ($registros as $rg) {
                            ?>
                            <tr>
                                <td><?= number_format($nRng++) ?></td>
                                <td><?= $rg["tipo"] ?></td>
                                <td><?= $rg["tarea"] ?></td>
                                <td><?= $rg["descripcion"] ?></td>
                                <td><?= $rg["revision"] ?></td>
                                <td><?= $rg["lapso"] ?></td>
                                <td><?= $rg["fecha"] ?></td>
                                <td><?= $rg["promesa"] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>

                    <tfoot>

                    </tfoot>
                </table>
            </div>

            <div id="Footer">
                <form name="form1" class="oculto" method="post" action="">
                    <table width="95%"  align="center" class="form">
                        <tbody>
                            <tr>
                                <td style="width: 30px;">
                                    <table>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td>
                                                <input type="date" id="Fecha" name="Fecha">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: center;">
                                    Tipo de tarea: 
                                    <select name="Rubro" id="Rubro">
                                        <option value="Todos">Todos</option>
                                        <option value="Actividad">Actividad</option>
                                        <option value="Servicio">Servicio</option>
                                        <option value="Matriz">Matriz</option>
                                    </select>
                                </td>
                                <td style="text-align: center;">
                                    <span><input type="submit" name="Boton" value="Enviar"></span>
                                    <span><input type="submit" name="Imprimir" value="Imprimir" onCLick="print()"></span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="exportar">
                                        <span id="Exportar">Exportar</span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
    </body>
</html>
