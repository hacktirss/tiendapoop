<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set("America/Mexico_City");
set_time_limit(720);
define("PROJECT_NAME", "POOPCORN");
define("SESSION_USUARIO", "USUARIO");
define("SESSION_CIA", "CIA");
define("DETALLE", "cVarVal");
define("CIA_PARAM", "CIA_PARAM");

include_once ("softcoatl/SoftcoatlHTTP.php");

include_once ("data/BitacoraDAO.php");
include_once ("data/AlarmasDAO.php");
include_once ("data/CiaDAO.php");
include_once ("data/CiasDAO.php");
include_once ("service/Usuarios.php");
include_once ("paginador/Paginador.php");
include_once ("paginador/PaginadorSession.php");

/**
 * 
 * @return Session
 */
function initSessionProject() {
    return Session::getInstance(PROJECT_NAME);
}

/**
 * 
 * @global Session $sessionProject
 */
function destroySessionProject() {
    global $sessionProject;
    return $sessionProject->destroy();
}

use com\softcoatl\utils as utils;

/**
 * 
 * @return mysqli
 */
function conectarse() {
    date_default_timezone_set("America/Mexico_City");
    return utils\IConnection::getConnection();
}

function BordeSuperior() {

    global $Titulo, $Id;

    $UsuarioSesion = getSessionUsuario();
    $CiaSesion = getSessionCia();

    $nMes = (int) date("m");

    $aMes = array("-", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $cFecha = date("d") . " de " . $aMes[$nMes] . " del " . date("Y");
    ?>
    <div id="Header">
        <table>
            <tbody>
                <tr>
                    <td style="text-align: left"> 
                        <img src="lib/logoPoop.png" class="logo" onclick="location.reload();">
                    </td>  
                    <td style="text-align: center;">
                        <div class="titulo"><?= $CiaSesion->getNombre() ?></div>
                        <div><?= $CiaSesion->getDireccion() . " #" . $CiaSesion->getNumeroext() . " Col. " . $CiaSesion->getColonia() ?></div>
                        <div><?= $CiaSesion->getMunicipio() . ", " . $CiaSesion->getEstado() . " C.P. " . $CiaSesion->getCodigo() ?></div>                    
                    </td>
                    <td style="text-align: right"> 
                        <img src="lib/logoPoop.png" class="logo" onclick="location.reload();">
                    </td>                    
                </tr>
            </tbody>
        </table>
        <div class="barraMenus">
            <div style="width: 70%;float: left;vertical-align: middle;">
                <?php menu(1) ?>
            </div>  
            <div style="float: right;padding-top: 5px;">
                <a href="menu.php"><i class="fa fa-lg fa-home" aria-hidden="true"></i> Inicio</a>
                <a href=javascript:winuni("ayuda.php?Id=<?= $Id ?>")><i class="fa fa-lg fa-question-circle" aria-hidden="true"></i> Ayuda</a>
                <a href="logout.php"><i class="fa fa-lg fa-sign-out" aria-hidden="true"></i> Salir</a>
            </div>
        </div>
    </div>

    <div id="Container">
        <div class="barraTitulo">
            <div style="width: 70%;float: left;"><strong><i class="fa fa-th-large" aria-hidden="true"></i> <?= $Titulo ?></strong></div>
            <div style="float: right;"><i class="fa fa-users" aria-hidden="true"></i> <?= $UsuarioSesion->getNombre() ?></div>
        </div>
        <div style="width: 98%;margin-left: auto;margin-right: auto;">
            <?php
        }

        function BordeSuperiorCerrar() {    //Cierra la ventana principal
            ?>
        </div>
    </div>
    <?php
}

function menu($opcion) {

    $Submenus = utils\HTTPUtils::getSessionValue("SUBMENUS");
    $Permisos = utils\HTTPUtils::getSessionValue("PERMISOS");
    if ($opcion == 1) {
        echo "<div id='menu'>";

        foreach ($Permisos as $permiso) {
            //error_log(print_r($Submenus[$permiso["menu"]]["menu"],true));
            if ($permiso["tipo"] === "0") {
                $existsMenu = number_format($permiso["permisos"]); /* Validará si existe al menos un 1 en la cadena */
                if ($existsMenu > 0) {
                    echo "<ul><li><a href=#>" . $Submenus[$permiso["menu"]]["menu"] . "</a><span> &nbsp;| </span>";
                    echo "<div><ul>";

                    $cadena = $permiso["permisos"];
                    $x = 0;

                    $Menus = $Submenus[$permiso["menu"]];
                    array_shift($Menus);

                    foreach ($Menus as $menu) {
                        $posicion = $cadena[$x++];
                        if ($posicion === "1" && $menu["permisos"] === "1") {
                            echo "<li><a href='" . $menu["url"] . "'>" . $menu["submenu"] . "</a></li>";
                        }
                    }

                    echo "</ul></div>";
                    echo "</li></ul>";
                }
            }
        }
        echo "</div>";
    } elseif ($opcion == 2) {
        echo "<div id='menuLateral'>";
        foreach ($Permisos as $permiso) {
            //error_log(print_r($Submenus[$permiso["menu"]]["menu"],true));
            if ($permiso["tipo"] === "1") {
                $existsMenu = number_format($permiso["permisos"]); /* Validará si existe al menos un 1 en la cadena */
                if ($existsMenu > 0) {
                    $cadena = $permiso["permisos"];
                    $x = 0;

                    $Menus = $Submenus[$permiso["menu"]];
                    array_shift($Menus);

                    foreach ($Menus as $menu) {
                        $posicion = $cadena[$x++];
                        if ($posicion === "1" && $menu["permisos"] === "1") {
                            echo "<div><a href='" . $menu["url"] . "'><i class='fa fa-lg fa-mars'></i> " . $menu["submenu"] . "</a></div>";
                        }
                    }
                }
            }
        }
        echo "</div>";
    }
}

function Encabezado($Titulo) {


    $CiaA = mysqli_query($link, "SELECT cia,direccion,colonia,estacion FROM cia");
    $Cia = mysqli_fetch_array($CiaA);
    ?>

    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="nombre_cliente">
        <tr><td>
                <div align="center"><b>DETI Desarrollo y Transferencia de Informática SA de CV</div>
                <div align="center">Calle Normal de maestro #10 Colonia Tulantongo Texcoco Edo. de Mexico</div>
                <div align="center"><?= $Titulo ?></div>
            </td>
        </tr>
    </table>

    <?php
}

function EncabezadoReportes() {
    global $Titulo;
    $CiaSesion = getSessionCia();
    ?>
    <div id="Header">
        <table width="95%">
            <tbody>
                <tr>
                    <td width="20%">
                        <img src="lib/logoPoop.png" border=0 height=77 onclick="location.reload();">
                    </td>
                    <td style="text-align: left; padding-left: 30px;">
                        <div class="titulo"><?= $CiaSesion->getNombre() ?></div>
                        <div><?= $CiaSesion->getDireccion() . " #" . $CiaSesion->getNumeroext() . " Col. " . $CiaSesion->getColonia() ?></div>
                        <div><?= $CiaSesion->getMunicipio() . ", " . $CiaSesion->getEstado() . " C.P. " . $CiaSesion->getCodigo() ?></div>    
                        <div><strong><?= $Titulo ?></strong></div>
                    </td>                    
                </tr>
            </tbody>
        </table>
    </div>

    <?php
}

function cZeros($Vlr, $nLen) {
    for ($i = strlen($Vlr); $i < $nLen; $i = $i + 1) {
        $Vlr = "0" . $Vlr;
    }
    return $Vlr;
}

function regresar($Return) {
    ?>
<p align="left"><a class="enlaces" href="<?= $Return ?>"><i class="icon fa fa-lg fa-arrow-circle-left"></i> Regresar</a></p>
    <?php
}

/**
 * 
 * @param string $Table
 * @return string
 */
function IncrementaFolio($Table) {
    $connecion = conectarse();
    $ciaSesion = getSessionCia();

    $selectFolio = "SELECT LPAD(IFNULL(MAX(folio) + 1,1), 5, 0) folionuevo FROM $Table WHERE TRUE AND cia = " . $ciaSesion->getId() . ";";
    $Fol = utils\ConnectionUtils::getRowsFromQuery($selectFolio, $connecion);

    return $Fol[0]["folionuevo"];
}

/**
 * 
 * @param string $Table
 * @param string $field
 * @return string
 */
function IncrementaId($Table, $field = "id") {
    $connecion = conectarse();
    $ciaSesion = getSessionCia();

    $selectId = "SELECT IFNULL(MAX($field) + 1,1) idnuevo FROM $Table WHERE TRUE AND cia = " . $ciaSesion->getId() . ";";
    $Fol = utils\ConnectionUtils::getRowsFromQuery($selectId, $connecion);

    return $Fol[0]["idnuevo"];
}

/**
 * 
 * @param int $busca
 * @param string $Tabla
 * @param string $Tablad
 */
function Totaliza($busca, $Tabla, $Tablad) {
    $mysqli = conectarse();
    $Cnt = $Importe = $Total = $Iva = $Ieps = $Isr = $Retencioniva = 0;
    $selectSum = "
                    SELECT 
                        SUM( cantidad ) cnt, 
                        SUM( ROUND( cantidad * ieps, 2 ) ) ieps, 
                        SUM( ROUND( precio * cantidad * iva, 2 ) ) iva, 
                        SUM( ROUND( precio * cantidad, 2 ) ) importe,
                        SUM( ROUND( precio * cantidad * isr, 2 ) ) isr,
                        SUM( ROUND( precio * cantidad * retencioniva, 2 ) ) retencioniva              
                    FROM $Tablad
                    WHERE id = $busca";
    $rows = utils\ConnectionUtils::getRowsFromQuery($selectSum, $mysqli);
    $Ddd = $rows[0];

    if ($Ddd[0] > 0) {
        $Cnt = $Ddd['cnt'];
        $Importe = $Ddd['importe'];
        $Iva = $Ddd['iva'];
        $Ieps = $Ddd['ieps'];
        $Isr = $Ddd['isr'];
        $Retencioniva = $Ddd['retencioniva'];
        $Total = $Importe + $Iva;
    }

    $updateTabla = " 
                UPDATE $Tabla SET 
                cantidad     = $Cnt, 
                importe      = $Importe, 
                iva          = $Iva, 
                ieps         = $Ieps, 
                isr          = $Isr, 
                retencioniva = $Retencioniva, 
                total        = $Total
                WHERE id = $busca";
    if (!$mysqli->query($updateTabla)) {
        error_log($mysqli->error);
    }
}
