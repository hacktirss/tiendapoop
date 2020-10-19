<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("softcoatl/Utilerias.php");
include_once ("softcoatl/HTTPUtils.php");

$link = conectarse();

require_once './Sistema_archivos_fileService.php';
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

                            <div class="toolbar pull-right">
                                <div class="btn-group  ">
                                    <a href="Sistema_archivos_fileDownload.php?idFile=<?= $filesFileVO->getId() ?>" class="btn btn-default"><i class="fa fa-download"></i> Descargar</a>
                                </div>

                            </div>
                            <h3><?= $filesFileVO->getFilename(); ?></h3>
                            <p><?= $filesFileVO->getDescription() ?></p>
                            <p class="text-muted"><i class="fa fa-clock-o"></i> <?= $filesFileVO->getCreated_at() ?></p>
                            <h4>Comentarios (<?= count($filesCommentsVO); ?>)</h4>
                            <form method="post" action="./?action=addfilecomment">
                                <div class="form-group">
                                    <textarea name="comment" required class="form-control"></textarea>
                                </div>
                                <input type="hidden" value="<?= $filesFileVO->getId() ?>" name="id">
                                <input type="submit" class="btn btn-primary" value="Enviar comentario">
                            </form>
                            <br>
                            <?php if (count($filesCommentsArray) > 0): ?>
                                <table class="table table-bordered">
                                    <?php foreach ($filesCommentsArray as $com): ?>
                                        <tr>
                                            <td>
                                                <h4><?= $com->getUser()->getFullname(); ?></h4>
                                                <p><?= $com->comment; ?></p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                        </div>

                    </div>   
                </td>
            </tr>
        </table>       

        <?php BordeSuperiorCerrar() ?>

        <script type="text/javascript" src="ficheros/js/jquery.min.js"></script>
        <script type="text/javascript" src="ficheros/js/bootstrap.min.js"></script>
    </body>

</html>
