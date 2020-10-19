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
<link rel="stylesheet" type="text/css" media="screen" href="css/Formularios.css?sum=<?= md5_file("css/Formularios.css") ?>"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/menus.css?sum=<?= md5_file("css/menus.css") ?>"/>

<link rel="stylesheet" type="text/css" media="screen" href="css/estilos_reportes.css?sum=<?= md5_file("css/estilos_reportes.css") ?>">
<!--<link rel="stylesheet" type="text/css" media="screen" href="ficheros/css/bootstrap.css"/>-->
<link rel="stylesheet" type="text/css" media="screen" href="fonts-awesome/css/font-awesome.min.css"/>

<link rel="stylesheet" type="text/css" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>

<script type="text/javascript" src="js/FileSaver.js"></script>
<script type="text/javascript" src="js/xlsx.core.min.js"></script>
<script type="text/javascript" src="js/excel/js/tableexport.js"></script>


<script type="text/javascript" src="lib/dhtmlgoodies_calendar.js?random=90090518"></script>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="paginador/predictive_search.js"></script>

<script>
    

    $(document).ready(function(){
        $(".fa-file-pdf-o").css({"color": "red"});
        $(".fa-file-code-o").css({"color": "green"});
        
        
    });
</script>