<?php
global $Gcia, $Gestacion, $Gdir, $Gfdogrid, $Gbarra, $InputCol, $Giva;

$Gcia = "PoopCorn Store";
$Gestacion = "PoopCorn";
$Gdir = "Calle Normal de Maestros No.10 Col. Tulantongo Texcoco Edo.de Mexico";

$Gfdogrid = "#E1E1E1";
$Gbarra = "#b9cdee";
$InputCol = "#22508f";

$Giva = .16;

$GRetencionIva = .1067;       // lo mando en factor el 10.67% 
?>
<link rel="shortcut icon" href="lib/iconoPoop.jpeg" type="image/x-icon"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css" media="screen" href="css/estilos_colores.css?sum=<?= md5_file("css/estilos_colores.css") ?>"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/estilos_reportes_print.css?sum=<?= md5_file("css/estilos_reportes_print.css") ?>">
<link rel="stylesheet" type="text/css" media="screen" href="fonts-awesome/css/font-awesome.min.css"/>
<!--<link rel="stylesheet" type="text/css" media="screen" href="css/normalize.min.css?sum=<?= md5_file("css/normalize.min.css") ?>">-->
<link rel="stylesheet" type="text/css" media="screen" href="css/paper.css?sum=<?= md5_file("css/paper.css") ?>">

<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/FileSaver.js"></script>
<script type="text/javascript" src="js/xlsx.core.min.js"></script>
<script type="text/javascript" src="js/excel/js/tableexport.js"></script>


<script>
    $(document).ready(function(){
        $(".fa-file-pdf-o").css({"color": "red"});
        $(".fa-file-code-o").css({"color": "green"});
    });
</script>