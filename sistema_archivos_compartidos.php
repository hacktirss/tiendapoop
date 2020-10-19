<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("softcoatl/Utilerias.php");
include_once ("softcoatl/HTTPUtils.php");

$link = conectarse();

require_once './Sistema_archivos_compartidosService.php';
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
    </head>

    <body>

        <?php BordeSuperior() ?>

        <table id="Principal">
            <tr>
                <td>
                    <div class="row">
                        <div class="col-md-12">
                            <ol class="breadcrumb">
                                <li><a href="sistema_archivos.php?busca=ini"><i class="fa fa-home"></i> Inicio</a></li>

                            </ol>

                            <h3><?= $title_file; ?></h3>
                        </div>
                    </div>   
                </td>
            </tr>

            <tr>
                <td>
                    <div id="Tabla">
                        <?php
                        PonEncabezado();
                        $count = 0;
                        while ($rg = FetchQuery($resultDataGrid)) {
                            $icon = '<i class="fa fa-file"></i>';
                            $href = "sistema_archivos_file.php?FileSession=" . $rg['id_file'] . "";
                            $size = $rg['size_file'] . "Kb";
                            ;
                            if ($rg[is_folder_file] == 1) {
                                $icon = '<i class="fa fa-folder"></i>';
                                $href = "sistema_archivos.php?FolderSession=" . $rg['id_file'] . "";
                            } else {
                                $size = number_format($rg['size_file'] / 1024, 2) . "Kb";
                            }
                            ?>
                            <tr class="texto_tablas">
                                <td style="width: 5px;"><?= $rg['ruta_file'] ?></td>
                                <td class="enlace"><?= $icon ?><?= $rg['filename_file'] ?></td>
                                <td><?= $rg['description_file'] ?></td>
                                <td><?= $size ?></td>
                                <td><?= $rg['created_at_file'] ?></td>
                                <td><a href="Sistema_archivos_fileDownload.php?idFile=<?= $rg['id_file'] ?>" class="btn btn-default"><i class="fa fa-download"></i> Descargar</a></td>
                            </tr>
                            <?php
                            $count++;
                        }
                        echo "</table>";
                        ?>   
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($count == 0): ?>
                                <div class="jumbotron" style="text-align: center">
                                    <h2>No hay archivos</h2>
                                    <p>No se encontraron archivos en la carpeta actual.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>       
        <div id="Msj" style="text-align: center"></div>
        <?php BordeSuperiorCerrar() ?>

        <script type="text/javascript" src="ficheros/js/jquery.min.js"></script>
        <script type="text/javascript" src="ficheros/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#Msj").html("<?= "$Msj" ?>");
            });
        </script>
    </body>

</html>
