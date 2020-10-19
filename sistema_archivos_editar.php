<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("softcoatl/Utilerias.php");
include_once ("softcoatl/HTTPUtils.php");

$link = conectarse();

require_once './Sistema_archivos_editarService.php';
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
                                <?php
                                if (is_array($array_path) && count($array_path) > 0) {
                                    foreach ($array_path as $key => $value) {
                                        echo '<li><a href="sistema_archivos.php?FolderSession=' . $key . '"><i class="fa fa-folder-open"></i> ' . $value . '</a></li>';
                                    }
                                }
                                ?>
                            </ol>
                            <h3>
                                <?php if ($filesFileVO->getIs_folder()): ?>
                                    <?= $filesFileVO->getFilename() ?>
                                <?php else : ?>
                                    <a href="sistema_archivos_file.php?idFile=<?= $filesFileVO->getId() ?>"><?= $filesFileVO->getFilename() ?></a> 
                                <?php endif; ?>
                                <small><?= $Titulo ?></small></h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" method="post" action="sistema_archivos_editar.php" enctype="multipart/form-data">
                                <?php if (!$filesFileVO->getIs_folder()): ?>
                                    <div class="form-group">
                                        <label for="">Archivo</label>
                                        <input type="file" name="filename" required>
                                    </div>
                                <?php else: ?>
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <input type="text" class="form-control" name="filename" value="<?= $filesFileVO->getFilename() ?>" required>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="">Descripcion</label>
                                    <textarea class="form-control" name="description"><?= $filesFileVO->getDescription() ?></textarea>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_public" id="is_public"> Archivo publico
                                    </label>
                                </div>
                                <input type="submit" class="btn btn-success" name="Boton" value="Actualizar">

                                <input type="hidden" name="idFile" value="<?= $File ?>">
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        </table>       

        <?php BordeSuperiorCerrar() ?>

        <script type="text/javascript" src="ficheros/js/jquery.min.js"></script>
        <script type="text/javascript" src="ficheros/js/bootstrap.min.js"></script>

        <script>
            $(document).ready(function () {
                var is_public = "<?= $filesFileVO->getIs_public() ?>";
                console.log(is_public);
                if (is_public) {
                    $("#is_public").prop('checked', true);
                }
            });
        </script>
    </body>

</html>
