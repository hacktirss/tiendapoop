<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

const SEPARATOR = "/";
use com\softcoatl\utils as utils;

$mysqli = conectarse();

/* Inicio arreglo(0 => busca,1 => pagina,2 =>orden,3 =>Sort,4 => Return) */
$array = array("", 0, "id_file", "Asc", "menu.php");
$sessionVars = "SystemFiles";
variablesCatalogos($array, 89, FALSE, $sessionVars); /* Array, Id de Qrys */
$request = utils\HTTPUtils::getRequest();

$user_id = $_SESSION["ID_USUARIO"];

$aCps = explode(",", $Qry[campos]);  // Es necesario para hacer el order by  desde lib;
$aIzq = array("Ruta", "-", "-");        //Arreglo donde se meten los encabezados; Izquierdos
$aDat = explode(",", $Qry[edi]);   //Arreglo donde llena el grid de datos        
$aDer = array(" ", "-", "-");     //Arreglo donde se meten los encabezados; Derechos;
$tamPag = $Qry[tampag];

$title_file = "Mis archivos compartidos";


#Armo el query segun los campos tomados de qrys;
$cSql = "SELECT $Qry[campos],id_file,getpath(id_file) ruta_file
         FROM $Qry[froms] WHERE 1=1 
         AND user_id_file <> " . $user_id . " 
         AND is_folder_file = 0 AND is_public_file = 1 ";


$numeroRegistros = Query($mysqli, $cSql, TRUE, FALSE, TRUE);

CalculaLimitesPaginacion($numeroRegistros);

$sql = $cSql . $cWhe . " ORDER BY " . $OrdenDef . " $Sort LIMIT " . $limitInf . "," . $tamPag;
error_log($sql);
$resultDataGrid = Query($mysqli, $sql, TRUE);
