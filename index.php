<?php 
include_once "./lib/lib.php";

use com\softcoatl\utils as utils;


$empresa = utils\ConnectionUtils::execSql("SELECT * FROM empresa WHERE id = 1");
?>
<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <title>Bienvenid@ Tienda PoopCorn</title>
    </head>
    <body>
        <div><h1><?= $empresa["razonsocial"]?></h1></div>
        
    </body>
</html>