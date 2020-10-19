<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;


#Variables comunes;
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));
$Titulo = "Sistema de control administrativo";
$Id = 1;

?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php";?>
        <title><?= $Gcia ?></title>        
    </head>
    <body>

        <?php BordeSuperior(); ?>

        <p><?= $Msj ?></p>

        <?php BordeSuperiorCerrar() ?>

    </body>
</html>
