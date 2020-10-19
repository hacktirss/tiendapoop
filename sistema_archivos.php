<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

$link = conectarse();

require_once './Sistema_archivosService.php';
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>
        <link rel="stylesheet" type="text/css" href="ficheros/css/ficherosStyles.css">
        <link rel="stylesheet" type="text/css" href="ficheros/css/bootstrap.css">
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
                                <?php
                                foreach ($array_path as $key => $value) {
                                    echo '<li><a href="sistema_archivos.php?FolderSession=' . $key . '"><i class="fa fa-folder-open"></i> ' . $value . '</a></li>';
                                }
                                ?>
                            </ol>

                            <div class="toolbar pull-right">
                                <div class="btn-group  ">
                                    <a href="sistema_archivos_compartidos.php" class="btn btn-default"><i class="fa fa-globe"></i> Archivos públicos</a>
                                </div>
                                <div class="btn-group  ">
                                    <a href="sistema_archivos_nuevo.php?type=0" class="btn btn-default"><i class="fa fa-plus"></i> Archivo</a>
                                    <a href="sistema_archivos_nuevo.php?type=1" class="btn btn-default"><i class="fa fa-folder"></i> Carpeta</a>
                                </div>
                            </div>
                            <h3><?= $title_file; ?></h3>
                        </div>
                    </div>   
                </td>
            </tr>

            <tr>
                <td>
                    <div id="TablaDatos" style="min-height: 30px;">
                        <table>
                            <?php
                            PonEncabezado2();
                            echo "<tbody>";
                            $count = 0;
                            while ($rg = FetchQuery($resultDataGrid)) {
                                $icon = '<i class="fa fa-file"></i>';
                                $href = "sistema_archivos_file.php?FileSession=" . $rg['id_file'] . "";
                                $size = $rg['size_file'] . "Kb";

                                if ($rg[is_folder_file] == 1) {
                                    $icon = '<i class="fa fa-folder"></i>';
                                    $href = "sistema_archivos.php?FolderSession=" . $rg['id_file'] . "";
                                } else {
                                    $size = number_format($rg['size_file'] / 1024, 2) . "Kb";
                                }
                                ?>
                                <tr>
                                    <td>
                                        <a href="sistema_archivos_editar.php?idFile=<?= $rg['id_file'] ?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Editar</a>

                                    </td>
                                    <td class="enlace"><a href="<?= $href ?>"><?= $icon ?><?= $rg['filename_file'] ?></a></td>
                                    <td><?= $rg['description_file'] ?></td>
                                    <td><?= $size ?></td>
                                    <td><?= $rg['created_at_file'] ?></td>
                                    <td>
                                        <a href="Sistema_archivos_fileDelete.php?idFile=<?= $rg['id_file'] ?>" class="btn btn-xs btn-default"><i class="fa fa-trash"></i> Eliminar</a>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                            }
                            echo "</tbody>";
                            ?> 
                        </table>
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
