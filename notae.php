<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

$link = conectarse();
$busca = $_REQUEST[busca];
$Fecha = date('Y-m-d H:m');

#Variables comunes;
$Titulo = "Nota de entrada";

$Filtro = $_REQUEST[Filtro];
if ($Filtro == 'Si') {
    $cWhere = ' existencia <> 0';
} else {
    $cWhere = " id <> '' ";
}

$CpoA = mysqli_query($link, "SELECT * FROM ned WHERE id=$busca");
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_reports.php"; ?>
        <title><?= $Gcia ?></title> 

        <style>
            html{
            }
            body{
                height: 98%;
                width: 98%;
                min-width: 900px;
            }

            #contenido{
                height: 38%;
                margin: 10px;
                margin-bottom: -10px;
                padding: 0px;
                position: inherit;
                max-height: available;
                /*border: 1px solid gray;*/
            }

            #acuse{
                height: 9%;
                margin: 10px;
                padding: 0px;
                /*border: 1px solid gray;*/
            }
            #acuse table{
                height: 100%;
                width: 98%;
                border-spacing: 1px;
                border: 0px solid;
                margin: auto;
                border-spacing: 2px;
            }
            #acuse table th{
                background-color: #C1C1C1;
                height: 25px;
            }
        </style>
    </head>

    <body>
        <div><?php contenido() ?></div>
        <div><?php contenido() ?></div>
        <div style="text-align: center" class="oculto">
            <form method="" action="" onsubmit="print()">
                <input type="submit" value="Imprimir">
                <input type="hidden" name="busca" value="<?= $busca ?>">
            </form>
        </div>
    </body>

</html>
<?php

function contenido() {
    global $Titulo, $link, $busca, $Gfdogrid, $Fecha;
    Encabezado($Titulo);
    $sql = "SELECT ne.factura,ne.fecha_entra,IFNULL(authuser.uname,'Compras') responsable,LPAD(IF(ne.entrada = 0,ne.id,ne.entrada),5,0) folio,
            prv.nombre,prv.rfc,ne.cantidad,ne.fechafac 
            FROM prv,ne left join authuser on ne.responsable=authuser.id 
            WHERE ne.id='$busca' AND ne.proveedor=prv.id";
    $HeA = mysqli_query($link, $sql) or die(mysql_error());
    $He = mysqli_fetch_array($HeA);

    $CpoA = mysqli_query($link, "SELECT ned.*,inv.descripcion FROM ned,inv WHERE ned.producto = inv.id AND  ned.id=$busca");
    ?>
    <div id="contenido">

        <table align='center' width='95%' border='1px' cellspacing='0' cellpadding='1' class='texto_tablas'>
            <tr class='texto_tablas'>
                <td align='left'>Factura:  <b><?= $He[factura] ?></td>
                <td>&nbsp;</td>
                <td align='right'>Fecha: <b><?= $He[fecha_entra] ?></td>
            </tr>
            <tr class='texto_tablas'>
                <td>Folio:  <b><?= $He[folio] ?></td>
                <td>Fecha de Factura: <b><?= $He[fechafac] ?></td>
                <td>Responsable: <b><?= $He[responsable] ?></td>
            </tr>
            <tr>
                <td>Importe: <b> <?= $He[cantidad] ?></td>
                <td colspan='2'>Provedor: <b><?php echo "$He[nombre] | $He[rfc]" ?></td>
            </tr>

        </table><br>


        <table align='center' width='95%'  border='0' cellspacing='2' cellpadding='1' class='texto_tablas'>

            <tr>
                <th align='center'>Clave</th>
                <th align='center'>Producto</th>
                <th align='center' width='20'>Cantidad</th>
                <th align='center'>Precio</th>
                <th align='center'>Total</th>
            </tr>

            <tr><td colspan='5'><hr></td></tr>
            <?php
            while ($registro = mysqli_fetch_array($CpoA)) {

                if (($nRng % 2) > 0) {
                    $Fdo = 'FFFFFF';
                } else {
                    $Fdo = $Gfdogrid;
                }    //El resto de la division;


                echo "<tr class='texto_tablas' bgcolor='$Fdo' onMouseOver=this.style.backgroundColor='$Gbarra';this.style.cursor='hand' onMouseOut=this.style.backgroundColor='$Fdo';>";
                echo "<td align='center'> $registro[producto]</td>";
                echo "<td> $registro[descripcion]</td>";
                echo "<td align='right'> $registro[cantidad]</td>";
                echo "<td align='right'> " . number_format($registro[precio], '4') . "</td>";
                echo "<td align='right'> " . number_format($registro[precio] * $registro[cantidad], '4') . "</td>";
                $Total += $registro[precio] * $registro[cantidad];
                echo "</tr>";
                $nRng++;
            }
            ?>
        </table>
        <table align='center' width='95%' border='0' cellspacing='0' cellpadding='1' class='texto_tablas'>
            <tr class='texto_tablas' height='25' background='lib/bartit2.gif' align='right'>
                <td colspan='2'></td>
                <td><b>Total ---></td>
                <td><b><?= number_format($Total, '2') ?></td>
            </tr>
        </table>
    </div>
    <div id="acuse">
        <?php acuse() ?>
    </div>
    <?php
}

function acuse() {
    ?>
    <table>
        <tr align='center' class='texto_tablas' height='20'>
            <th>Observaciones</th>
            <th>Nombre y Firma</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?php
}
?>
