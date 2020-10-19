<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;
use com\softcoatl\utils\HTTPUtils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$link = conectarse();

$nameSession = "moduloReingresos";
$busca = $request->hasAttribute("id") ? $request->getAttribute("id") : $request->getAttribute("busca");

if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    HTTPUtils::setSessionBiValue($nameSession, DETALLE, NULL);
    header("Location: nota_pro_s.php?criteria=ini&returnLink=reingresose.php&backLink=reingresos.php");
}

if ($request->hasAttribute(DETALLE)) {
    HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute(DETALLE));
}

$cValVar = HTTPUtils::getSessionBiValue($nameSession, DETALLE);


if (isset($_REQUEST[pagina])) {
    $_SESSION['OnToy1'][1] = $_REQUEST[pagina];
}
if (isset($_REQUEST[orden])) {
    $_SESSION['OnToy1'][2] = $_REQUEST[orden];
}
if (isset($_REQUEST[Sort])) {
    $_SESSION['OnToy1'][3] = $_REQUEST[Sort];
}
if (isset($_REQUEST[Ret])) {
    $_SESSION['OnToy1'][4] = $_REQUEST[Ret];
}

#Saco los valores de las sessiones los cuales normalmente no cambian;
$pagina = $_SESSION[OnToy1][1];
$OrdenDef = $_SESSION[OnToy1][2];
$Sort = $_SESSION[OnToy1][3];
$Filtro = $_SESSION[OnToy1][5];

$Usr = $_COOKIE[USERNAME];


$Msj = $_REQUEST[Msj];
$op = $_REQUEST[op];
$Id = 85;                            //Numero de query dentro de la base de datos
$cId = $_REQUEST[cId];
$Return = substr($_SERVER[PHP_SELF], 0, -5) . ".php?busca=ini";
$Titulo = "Nota de entrada de equipos detalle";


if ($_REQUEST[Boton] == "Agregar") {        //Para agregar uno nuevo
    $now = date("Y-m-d H-i_s");
    $entrada = mysqli_fetch_array(mysqli_query($link, "SELECT entrada FROM cia;"));
    $cSql = "INSERT INTO nee (responsable,proveedor,importe,factura,fechafac,fecha,concepto,entrada)
                    VALUES
                    ('$Usr','$_REQUEST[Proveedor]','$_REQUEST[Importe]','$_REQUEST[Factura]',
                    '$_REQUEST[Fechafac]','$now','$_REQUEST[Concepto]','$entrada[0]')
                    ";

    if (!mysqli_query($link, $cSql)) {
        echo "<div align='center'>$cSql</div>";
        $Archivo = 'PRV ';
        die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysqli_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
    }

    $Msj = "Registro dado de alta";
    $busca = mysqli_insert_id($link);

    mysqli_query($link, "UPDATE cia SET entrada = entrada +1;");
    mysqli_query($link, "UPDATE egresos SET entradaid = '$busca' WHERE id='$cId'");

    header("Location: $_SERVER[PHP_SELF]?cValVar=$busca");
} elseif ($_REQUEST[Boton] == "Actualizar") {

    $cSql = "UPDATE nee SET fechafac='$_REQUEST[Fechafac]',concepto='$_REQUEST[Concepto]',
           proveedor='$_REQUEST[Proveedor]',importe='$_REQUEST[Importe]',factura='$_REQUEST[Factura]'            
           WHERE id='$cValVar' limit 1";
    if (!mysqli_query($link, $cSql)) {
        echo "<div align='center'>$cSql</div>";
        $Archivo = 'ne ';
        die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysqli_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
    }

    $Msj = "Registro actualizado";


    header("Location: $_SERVER[PHP_SELF]?Msj=$Msj");
} elseif ($_REQUEST[Boton] == 'Cancelar') {

    if ($_REQUEST[Cancelacion] <> $PASSWORD) {
        header("Location: $_SERVER[PHP_SELF]?Mjs=Clave incorrecta");
    }

    $cSql = "DELETE FROM equipos WHERE id IN (SELECT idnvo FROM need WHERE id = $cValVar)";
    mysqli_query($link, $cSql) or die(mysqli_error($link));

    mysqli_query($link, "UPDATE nee SET status='Cancelada' WHERE id = $cValVar LIMIT 1") or dir(mysqli_error($link));
    $Msj = "La entrada ha sido cancelada con exito";

    header("Location: $Return&Msj=$Msj");
} elseif ($_REQUEST[Boton] == "Agregar equipo") {

    $ExiA = mysqli_query($link, "SELECT numero_serie,modelo FROM need 
            WHERE id='$cValVar' AND numero_serie='$_REQUEST[Serie]'");

    $Exi = mysqli_fetch_array($ExiA);

    if ($Exi[numero_serie] == '') {

        $cSql = "INSERT INTO need (id,grupo,marca,modelo,numero_serie,costo,precio)
                 VALUES('$cValVar','$_REQUEST[Grupo]','$_REQUEST[Marca]','$_REQUEST[Modelo]',
                 '$_REQUEST[Serie]','$_REQUEST[Costo]','$_REQUEST[Precio]')";

        if (!mysqli_query($link, $cSql)) {
            echo "<div align='center'>$cSql</div>";
            $Archivo = 'NED ';
            die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysqli_error($link) . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
        }

        TotalizaMov($cValVar);

        $Msj = "Equipo agregado con exito";
    } else {

        $Msj = 'Favor de verificar! el numero de serie ya se capturo [' . $_REQUEST[Serie] . ']';
    }

    header("Location: $_SERVER[PHP_SELF]?Msj=$Msj");
}


if ($op == 'Si') {

    $lUp = mysqli_query($link, "DELETE FROM need WHERE idnvo='$_REQUEST[cId]' limit 1");

    $Msj = "Entrada elminada";

    TotalizaMov($cValVar);

    header("Location: $_SERVER[PHP_SELF]?Msj=$Msj");
} elseif ($_REQUEST[op] == 'cdr') {

    $Sql = "INSERT INTO equipos 
            SELECT '',need.marca,need.descripcion,
            need.grupo,need.numero_serie,need.modelo,
            need.costo,need.precio,need.id
            FROM need WHERE need.id = $cValVar";

    mysqli_query($link, $Sql) or die(mysqli_error($link));

    $lUp = mysqli_query($link, "UPDATE nee SET status='Cerrada' WHERE id='$cValVar'");
    header("Location: $Return");
}



#Tomo los datos principales campos a editar, tablas y filtros;
$QryA = mysqli_query($link, "SELECT campos,froms,edi,tampag,filtro,lef FROM qrys WHERE id=$Id");
$Qry = mysqli_fetch_array($QryA);

if (strlen($Qry[filtro]) > 5) {
    $Dsp = 'Filtro activo';
}

$Palabras = str_word_count($busca);  //Dame el numero de palabras
if ($Palabras > 1) {
    $P = explode(" ", $busca);          //Metelas en un arreglo
    for ($i = 0; $i < $Palabras; $i++) {
        if (!isset($BusInt)) {
            $BusInt = " $OrdenDef LIKE  '%$P[$i]%' ";
        } else {
            $BusInt = $BusInt . " AND $OrdenDef like '%$P[$i]%' ";
        }
    }
} else {
    $BusInt = $OrdenDef . " LIKE '%%'";
}


#Armo el query segun los campos tomados de qrys;
$cSql = "SELECT $Qry[campos],need.idnvo FROM $Qry[froms] LEFT JOIN $Qry[lef] ON need.grupo = $Qry[lef].id WHERE need.id = $cValVar ";
//echo $cSql;

$aCps = explode(",", $Qry[campos]);    // Es necesario para hacer el order by  desde lib;
$aIzq = array();    //Arreglo donde se meten los encabezados; Izquierdos
$aDat = explode(",", $Qry[edi]);     //Arreglo donde llena el grid de datos
$aDer = array("", "", "");    //Arreglo donde se meten los encabezados; Derechos;
$tamPag = 10;


$HeA = mysqli_query($link, "SELECT id,fecha,concepto,fechafac,responsable,proveedor,factura,
       status,ROUND(importe,2) importe,cantidad,costo_entrada 
       FROM nee WHERE id='$cValVar'");
$He = mysqli_fetch_array($HeA);
?>
<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title> 
        <script>

            $(document).ready(function () {
                $("#Precio").click(function () {
                    var costo = Number($("#Costo").val());
                    precio = (costo + costo * .20);
                    $("#Precio").val(precio);
                });
            });



        </script>

    </head>

    <body bgcolor='#FFFFFF' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>

        <table width='100%' height='100%' border='0' align='center' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border: 1px solid #066;'>
            <tr>
                <td width='99%' align='left' valign='top'>

                    <?php BordeSuperior() ?>

                    <table width='100%' align='center' border='0'>
                        <tr>
                            <td class='texto_tablas' align='left' height='410' valign='top'>




                                <table width='100%' border='0' cellpadding='2' cellspacing='1'>

                                    <tr>
                                        <td height='400' valign='top' align='center' width='50%'>        


                                            <table width='100%' border='0' cellpadding='2' cellspacing='1' bgcolor='#f1f1f1' style='border-collapse: collapse; border: 1px solid #999;'>

                                                <tr>
                                                    <td colspan='2' align='left' class='titulo_blanco'> &nbsp; Datos de la nota de entrada [Compra de equipos]</td>
                                                </tr>     

                                                <form name='form1' method='post' action="">

                                                    <?php
                                                    cInput("Id:", "Text", "5", "Id", "right", $cValVar, "40", false, true, "");

                                                    if ($_REQUEST[busca] == 'NUEVO') {
                                                        $Opago = mysqli_fetch_array(mysqli_query($link,
                                                                        "SELECT egresos.id,ordpagos.proveedor,ordpagos.concepto, egresos.pagoreal 
                                                             FROM `egresos` LEFT JOIN ordpagos ON egresos.ordendepago=ordpagos.id 
                                                             WHERE egresos.id='$_REQUEST[cId]'"));
                                                        $Proveedor = $Opago[proveedor];
                                                        $Concepto = $Opago[concepto];
                                                        $Fecha = date("Y-m-d");
                                                        $Fechafac = date("Y-m-d");
                                                        $Importe = $Opago[pagoreal];
                                                        echo "<input type='hidden' name='cId' value='$cId'>";
                                                    } else {

                                                        $Fecha = $He[fecha];
                                                        $Fechafac = $He[fechafac];
                                                        $Proveedor = $He[proveedor];
                                                        $Concepto = $He[concepto];
                                                        $Importe = $He[importe];
                                                    }

                                                    cInput("Fecha entrada: ", "Text", "12", "Fecha", "right", $Fecha, "12", true, TRUE, "");

                                                    cInput("Concepto:", "Text", "52", "Concepto", "right", $Concepto, "100", true, false, '', "placeholder=''");

                                                    echo "<tr><td align='right' class='content_txt'>Proveedor: &nbsp;</td><td>";
                                                    $PrvA = mysqli_query($link, "SELECT id,nombre FROM prv ORDER BY prv.id");
                                                    echo "&nbsp;<select class='content_txt' name='Proveedor'>";
                                                    while ($Prv = mysqli_fetch_array($PrvA)) {
                                                        echo "<option value='$Prv[id]'>$Prv[id] | $Prv[nombre]</option>";
                                                        if ($Proveedor == $Prv[id]) {
                                                            $Display = $Prv[nombre];
                                                        }
                                                    }
                                                    echo "<option selected value='$Proveedor'>$Display</option>";  //se va
                                                    echo "</select> ";
                                                    echo "</td><tr>";

                                                    cInput("Fecha Factura: ", "Text", "12", "Fechafac", "right", $Fechafac, "12", true, false, "<img src='lib/calendar.png' border='0' onclick=displayCalendar(document.forms[0].Fechafac,'yyyy-mm-dd',this)>");

                                                    cInput("Num. Factura:", "Text", "40", "Factura", "right", $He[factura], "40", true, false, '');

                                                    cInput("Importe factura:", "Text", "40", "Importe", "right", number_format($Importe, 2), "40", true, false, '');

                                                    cInput("Importe entrada:", "Text", "40", "Importe $", "right", number_format($He[costo_entrada], 2), "10", true, true, 'Cnt:' . $He[cantidad]);

                                                    cInput("Responsable:", "Text", "40", "Responsable", "right", $He[responsable], "40", true, TRUE, '');

                                                    echo "<input type='hidden' name='busca' value='$busca'>";

                                                    echo "<tr class='content_txt'><td align='center' colspan='2'>";
                                                    echo "<br>";
                                                    if ($cValVar <> '') {
                                                        if ($He[status] == "Abierta") {
                                                            echo "<input class='numeros_pagina' type='submit' name='Boton' value='Actualizar'>";
                                                            echo "&nbsp;&nbsp;&nbsp;<input class='numeros_pagina' type='submit' name='Boton' value='Cancelar nota'><br><br>";
                                                            if (abs($He[importe] - $He[costo_entrada]) < 2) {

                                                                echo "<div align=right><a href=javascript:confirmar('Deseas&nbsp;cerrar&nbsp;la&nbsp;compra?','$_SERVER[PHP_SELF]?op=cdr'); class='numeros_pagina'>Tu nota se encuentra cuadrada, clic aqui para cerrarla &nbsp;&nbsp;&nbsp;&nbsp;</a></div>";
                                                            }
                                                        } else {
                                                            echo "<br>";
                                                        }
                                                    } else {
                                                        echo "<input class='numeros_pagina' type='submit' name='Boton' value='Agregar'><br><br>";
                                                    }
                                                    echo "</td></tr>";
                                                    ?>                                                

                                                    <?php if ($He[status] == "Cerrada") { ?>
                                                        <tr>
                                                            <td colspan='2' align='left' class='titulo_blanco'> &nbsp; Cancelar entrada</td>

                                                        </tr>
                                                        <tr><td colspan="2"></td></tr>
                                                        <tr>
                                                            <td class="content_txt" align="right">Clave de cancelacion: &nbsp;</td>
                                                            <td>
                                                                <input type="password" name="Cancelacion" placeholder="  Contraseña personal" autocomplete="off">
                                                                <input type="submit" name="Boton" value="Cancelar" class="numeros_pagina">
                                                            </td>
                                                        </tr>
                                                        <tr><td colspan="2" align="center" class="content_txt"><?= $Msj ?></td></tr>
                                                    <?php } ?>

                                                </form>
                                            </table>

                                            <?php regresar($Return) ?>

                                        </td>

                                        <td valign='top' align='center' width="50%">

                                            <table width='100%' valign="top" border='0' cellpadding='2' cellspacing='1' bgcolor='#f1f1f1' style='border-collapse: collapse; border: 1px solid #999;'>

                                                <tr>
                                                    <td colspan='2' align='left' class='titulo_blanco'> &nbsp; Detalle de productos</td>
                                                </tr>  
                                                <tr>
                                                    <td>
                                                        <?php
                                                        $res = mysqli_query($link, $cSql);

                                                        CalculaPaginas();        #--------------------Calcual No.paginas-------------------------

                                                        $sql = $cSql . $cWhe . " ORDER BY " . $orden . " $Sort LIMIT " . $limitInf . "," . $tamPag;
//echo $sql;

                                                        $res = mysqli_query($link, $sql);


                                                        echo "<table width='100%' align='center' border='0'>";
                                                        echo "<tr>";
                                                        echo "<td align='left' height='255' valign='top'>";

                                                        PonEncabezado();         #---------------------Encabezado del browse----------------------

                                                        $Pos = strrpos($_SERVER[PHP_SELF], ".");
                                                        $cLnk = substr($_SERVER[PHP_SELF], 0, $Pos) . 'e.php';     #


                                                        while ($rg = mysqli_fetch_array($res)) {

                                                            if (($nRng % 2) > 0) {
                                                                $Fdo = 'FFFFFF';
                                                            } else {
                                                                $Fdo = $Gfdogrid;
                                                            }    //El resto de la division;


                                                            echo "<tr class='texto_tablas' bgcolor='$Fdo' onMouseOver=this.style.backgroundColor='$Gbarra';this.style.cursor='hand' onMouseOut=this.style.backgroundColor='$Fdo';>";
                                                            Display($aCps, $aDat, $rg);

                                                            if ($He[status] == "Abierta") {
                                                                echo "<td align='center'><a class='seleccionar' href=javascript:confirmar('Deseas&nbsp;eliminar&nbsp;el&nbsp;$rg[id_nvo]?','$_SERVER[PHP_SELF]?cId=$rg[idnvo]&op=Si');>eliminar</a></td>";
                                                            } else {
                                                                echo "<td align='center'>-</td>";
                                                            }
                                                            echo "</tr>";

                                                            $nRng++;
                                                        }
                                                        echo "</td></tr></table>"; //Aqui termina el encabezado

                                                        PonPaginacion(false);

                                                        echo "</td></tr></table>";
                                                        ?>

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan='2' align='left' class='titulo_blanco'> &nbsp; Agregar equipo</td>
                                                </tr>  
                                                <tr>
                                                    <td>
                                                        <form name="form2" method="post" action="">
                                                            <table width="90%">
                                                                <tr><td>

                                                                        <?php
                                                                        //if($cVarVal <> 'NUEVO'){
                                                                        //cInput("Nombre:", "Text", "40", "Nombre", "right", "", "40", true, false, '', "required");

                                                                        echo "<tr><td align='right' class='content_txt'>Grupo: &nbsp;</td><td>";
                                                                        $GrA = mysqli_query($link, "SELECT id,nombre FROM grupos WHERE id > 1 ORDER BY grupos.id");
                                                                        echo "&nbsp;<select class='content_txt' name='Grupo'>";
                                                                        while ($Gr = mysqli_fetch_array($GrA)) {
                                                                            echo "<option value='$Gr[id]'>$Gr[id] | $Gr[nombre]</option>";
                                                                        }
                                                                        echo "</select> ";
                                                                        echo "</td><tr>";

                                                                        cInput("Marca:", "Text", "40", "Marca", "right", "", "40", true, false, '', "");

                                                                        cInput("Modelo y/o caracteristicas:", "Text", "40", "Modelo", "right", "", "40", true, false, "", "");

                                                                        cInput("Numero de serie:", "Text", "40", "Serie", "right", "", "40", true, false, '', "required");

                                                                        cInput("Costo:", "Text", "20", "Costo", "right", "0", "40", true, false, '', "required id='Costo'");

                                                                        cInput("Precio sugerido:", "Text", "20", "Precio", "right", "", "40", true, false, '', "");

                                                                        echo "<tr><td colspan='2' align='right'><input type='submit' class='numeros_pagina' value='Agregar equipo' name='Boton'> &nbsp; &nbsp; </td></tr>";

                                                                        //}
                                                                        ?>
                                                                    </td></tr>
                                                            </table>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="content_txt">
                                                        <?php
                                                        if ($He[status] == "Abierta") {

                                                            if ($Total > abs($He[importe] - 1) and $Total < abs($He[importe] + 1)) {
                                                                echo "<div align=right><a href=javascript:confirmar('Deseas&nbsp;cerrar&nbsp;tu&nbsp;nota?','$_SERVER[PHP_SELF]?op=cdr'); class='numeros_pagina'>Tu nota se encuentra cuadrada, clic aqui para cerrarla &nbsp;&nbsp;&nbsp;&nbsp;</a></div>";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    </table>

                    <?php
                    BordeSuperiorCerrar();


                    echo "</body>";

                    echo "</html>";

                    function TotalizaMov($busca) { //busca es idnvo de medt y cVarVal es id de la entrada;
                        $link = conectarse();

                        $cSql = "SELECT count(*) cantidad,sum(costo) as costo FROM need WHERE id='$busca'";
                        $SqlA = mysqli_query($link, $cSql);

                        if (!$SqlA) {
                            echo "<div align='center'>$cSql</div>";
                            $Archivo = 'NEED ';
                            die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysql_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
                        }

                        $Ddd = mysqli_fetch_array($SqlA);

                        if ($Ddd[0] == 0) {
                            $Cnt = 0;
                            $Costo = 0;
                        } else {
                            $Cnt = $Ddd[0];
                            $Costo = $Ddd[1];
                        }

                        $cSql = "UPDATE nee SET cantidad='$Cnt',costo_entrada = '$Costo' WHERE id='$busca'";

                        if (!mysqli_query($link, $cSql)) {
                            echo "<div align='center'>$cSql</div>";
                            $Archivo = 'NEE ';
                            die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysql_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
                        }
                    }
                    ?>
                
