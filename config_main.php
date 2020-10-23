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

<link rel="stylesheet" type="text/css" media="screen" href="lib/estilos.css?sum=<?= md5_file("lib/estilos.css") ?>"/>
<link rel="stylesheet" type="text/css" media="screen" href="lib/dhtmlgoodies_calendar.css?random=90051112"/>
<link rel="stylesheet" type="text/css" media="screen" href="ficheros/css/ficherosStyles.css"/>
<!--<link rel="stylesheet" type="text/css" media="screen" href="ficheros/css/bootstrap.css"/>-->
<link rel="stylesheet" type="text/css" media="screen" href="fonts-awesome/css/font-awesome.min.css"/>

<link rel="stylesheet" type="text/css" media="screen" href="bootstrap-4.0.0/dist/css/bootstrap-grid.css"/>

<link rel="stylesheet" type="text/css" media="screen" href="paginador/paginador.css?sum=<?= md5_file("paginador/paginador.css") ?>">
<link rel="stylesheet" type="text/css" media="screen" href="paginador/predictive_styles.css?n=2">
<link rel="stylesheet" type="text/css" media="screen" href="paginador/tablas.css?sum=<?= md5_file("paginador/tablas.css") ?>">

<script type="text/javascript" src="lib/dhtmlgoodies_calendar.js?random=90090518"></script>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="paginador/predictive_search.js"></script>

<script>
    var bntFormSubmit = 0;

    function wingral(url) {
        window.open(url, 'wingeneral', 'status=no,tollbar=yes,scrollbars=yes,menubar=no,width=1000,height=600,left=10,top=50');
    }
    
    function winuni(url) {
        window.open(url, 'filtros', 'status=no,tollbar=yes,scrollbars=yes,menubar=no,width=1050,height=550,left=250,top=80');
    }
    
    function winmin(url) {
        window.open(url, 'miniwin', 'width=400,height=500,left=150,top=120,location=no');
    }
    
    function confirmar(mensaje, url) {
        if (confirm(mensaje)) {
            document.location.href = url;
        }
    }
    
    function borrar(identificador, url) {
        if (confirm("¿Deseas dar de baja el registro " + identificador + "?")) {
            document.location.href = url + "?op=Si&cId=" + identificador;
        }
    }
    
    function borrarD(identificador, url) {
        if (confirm("¿Deseas dar de baja el registro " + identificador + "?")) {
            document.location.href = url + "?opD=Si&cId=" + identificador;
        }
    }
    
    function mayus(e) {
        e.value = e.value.toUpperCase();
    }
    
    $(document).ready(function(){
        
        $(".fa-file-pdf-o").css({"color": "red"});
        $(".fa-file-code-o").css({"color": "green"});
        $(".fa-file-pdf-o").prop("title", "Obtener PDF");
        $(".fa-file-code-o").prop("title", "Obtener XML");
        $(".fa-print").prop("title", "Obtener reporte o acuse");
        $(".fa-edit").prop("title", "Modificar el contenido del registro");
        
        $("form").submit(function (e){
            if(bntFormSubmit > 0){
                bntFormSubmit += 1;
                e.preventDefault();
                console.log("Form send " + bntFormSubmit + " times. ");
            }else{
                bntFormSubmit = 1;
                return true;
            }
        });
    });
</script>