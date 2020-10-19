<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("CFDIComboBoxes.php");

$queryParameters = array();
foreach ($_REQUEST as $key => $value) {
    $queryParameters[$key] = $value;
}

$tipo = $queryParameters['tipo'];
$division = $queryParameters['division'];
$grupo = $queryParameters['grupo'];
$clase = $queryParameters['clase'];
$cbConcepto = $queryParameters['quicksearch'] != '' ? trim(substr($queryParameters['quicksearch'], 0, strpos($queryParameters['quicksearch'], "|"))) : $queryParameters['claveps'];

$link = conectarse();

$Msj = $_REQUEST[Msj];
$op = $_REQUEST[op];
$busca = $_REQUEST[busca];
$Return = "productose.php?busca=$busca";
$Titulo = "Catálogo de claves de Producto/Servicio SAT CFDI 3.3";

$Usr = $_COOKIE[USERNAME];

$ct_ps_q = mysqli_query($link, "
    SELECT 
            CT.clave ccategoria,
            CT.clave_PADRE,
            C.nombre, 
            C.clave,
            CT.descripcion, 
            CT.tipo,
            CASE
                WHEN CT.clave = CONCAT(SUBSTR(C.clave, 1, 2), '000000') THEN 'division'
                WHEN CT.clave = CONCAT(SUBSTR(C.clave, 1, 4), '0000') THEN 'grupo'
                WHEN CT.clave = CONCAT(SUBSTR(C.clave, 1, 6), '00') THEN 'clase'
                WHEN CT.clave = C.clave  THEN 'concepto'
            END categoria
        FROM cfdi33_c_conceptos C
        JOIN cfdi33_c_categorias CT 
            ON CT.clave = CONCAT(SUBSTR(C.clave, 1, 2), '000000') 
            OR CT.clave = CONCAT(SUBSTR(C.clave, 1, 4), '0000')
            OR CT.clave = CONCAT(SUBSTR(C.clave, 1, 6), '00')
        WHERE C.clave = '" . $cbConcepto . "'
        ORDER BY CT.clave
");

$ctipo = "";

$cdivision = "";
$cgrupo = "";
$cclase = "";

while (($ct_rs_rs = mysqli_fetch_array($ct_ps_q))) {

    if ($ct_rs_rs['categoria'] == 'division') {
        $cdivision = $ct_rs_rs['ccategoria'];
    }
    if ($ct_rs_rs['categoria'] == 'grupo') {
        $cgrupo = $ct_rs_rs['ccategoria'];
    }
    if ($ct_rs_rs['categoria'] == 'clase') {
        $cclase = $ct_rs_rs['ccategoria'];
    }

    $ctipo = $ct_rs_rs['tipo'];
}

if (substr($_REQUEST[Boton], 0, 7) == "Agregar") {        //Para agregar uno nuevo
    $cSql = "UPDATE cfdi33_c_conceptos SET status = 1 WHERE clave = '" . $cbConcepto . "'";
    if (!mysqli_query($link, $cSql)) {
        echo "<div align='center'>$cSql</div>";
        $Archivo = 'INV ';
        die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysqli_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
    }

    $Msj = "Registro dado de alta";

    header("Location: $Return?Msj=$Msj&busca=$busca&common_claveps=$cbConcepto");
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
        <script type="text/javascript" src="js/js-usuarios.js"></script>

        <script>

            $(document).ready(function () {
                $('#tipo').val('<?= $ctipo != '' ? $ctipo : $tipo ?>');
                $('#division').val('<?= $cdivision != '' ? $cdivision : $division ?>');
                $('#grupo').val('<?= $cgrupo != '' ? $cgrupo : $grupo ?>');
                $('#clase').val('<?= $cclase != '' ? $cclase : $clase ?>');
                $('#claveps').val('<?= $cbConcepto != '' ? $cbConcepto : $claveps ?>');

                $('#tipo').change(function () {
                    $('#division').val('');
                    $('#grupo').val('');
                    $('#clase').val('');
                    $('#claveps').val('');
                    document.form1.submit();
                });
                $('#division').change(function () {
                    $('#grupo').val('');
                    $('#clase').val('');
                    $('#claveps').val('');
                    document.form1.submit();
                });
                $('#grupo').change(function () {
                    $('#clase').val('');
                    $('#claveps').val('');
                    document.form1.submit();
                });
                $('#clase').change(function () {
                    $('#claveps').val('');
                    document.form1.submit();
                });
                $('#claveps').change(function () {
                    $('#Boton').val('Agregar Clave ' + $('#claveps').val());
                });

                $('#autocomplete').val('<?= $SCliente ?>').addClass('texto_tablas')
                        .attr('size', '145')
                        .attr('placeholder', '                                             <-- ------ Favor de seleccionar el concepto ------ -->')
                        .click(function () {
                            this.select();
                        })
                        .activeComboBox(
                                $('[name=\'form1\']'),
                                'SELECT clave as data, CONCAT(clave, \' | \', nombre) value FROM cfdi33_c_conceptos WHERE 1=1',
                                'nombre');

            });
            function load() {
                document.form10.busca.focus();
            }
        </script>
    </head>
    
    <body>

        <table style="width: 100%; height: 100%; border: 0px; text-align: center; padding: 0px; border-collapse: collapse; border: 1px solid #066;">
            <tr>
                <td style="width: 99%; text-align: center; vertical-align: top;">
                    <?php BordeSuperior() ?>
                    <form name='form1' method='post' action="">
                        <input type="hidden" name="busca" id="busca" value="<?= $busca ?>"/> 
                        <table style="width: 90%; text-align: center; border: 0px; margin: 5px;">                                            
                            <caption class="nombre_cliente">Herramienta de búsqueda por Categorías de Clave de Producto/Servicio definidas por el SAT para el CFDI versión 3.3</caption>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1; padding: 5px;">
                                <td class="nombre_cliente"> 
                                    <b>Búsqueda rápida.</b> <br/>
                                    <small>Escriba el texto a buscar y seleccione el más apropiado.</small>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <div style="position: relative;">
                                        <input style="font-size: 12px;" type="text" name="quicksearch" id="autocomplete"/>
                                        <?= "<br/>" . $ccategoria ?>
                                    </div>
                                    <div id="autocomplete-suggestions"></div>
                                </td>
                            </tr>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1;">
                                <td class="nombre_cliente">
                                    <b>Producto/Servicio</b>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <select style="font-family: Tahoma, Geneva, sans-serif; font-size: 12px; font-weight: bold; color: #3E5A8F;" name="tipo" id="tipo">
                                        <option value="">SELECCIONE TIPO</option>
                                        <option value="Producto">Producto</option>
                                        <option value="Servicio">Servicio</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1;">
                                <td class="nombre_cliente">
                                    <b>División</b>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <?= ComboboxDivison::generate("division", $ctipo != '' ? $ctipo : $tipo); ?>
                                </td>
                            </tr>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1;">
                                <td class="nombre_cliente">
                                    <b>Grupo</b>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <?= ComboboxGrupo::generate("grupo", $cdivision != '' ? $cdivision : $division); ?>
                                </td>
                            </tr>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1;">
                                <td class="nombre_cliente">
                                    <b>Clase</b>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <?= ComboboxClase::generate("clase", $cgrupo != '' ? $cgrupo : $grupo); ?>
                                </td>
                            </tr>
                            <tr class="nombre_cliente" style="background-color:#DEDEF1;">
                                <td class="nombre_cliente">
                                    <b>Clave de Producto/Servicio</b> <br/>
                                    <small>Clave de producto requerida por el SAT.</small>
                                </td>
                                <td class="nombre_cliente"  style="text-align: left;"> 
                                    <?= ComboboxProductoServicio::generate("claveps", $cclase != '' ? $cclase : $clase); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php regresar($Return) ?>
                                </td>
                                <td><input class="numeros_pagina" type="submit" id="Boton" name="Boton" value="Agregar Clave <?= $cbConcepto ?>"></td>
                            </tr>
                        </table>
                        <?php
                        BordeSuperiorCerrar();
                        ?>
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>

