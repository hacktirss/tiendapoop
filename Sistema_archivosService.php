<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

const SEPARATOR = "/";
use com\softcoatl\utils as utils;

$mysqli = conectarse();

/* Inicio arreglo(0 => busca,1 => pagina,2 =>orden,3 =>Sort,4 => Return) */
$array = array("", 0, "id_file", "Asc", "menu.php");
$otherFilter = array("FolderSession" => "", "FileSession" => "");
$sessionVars = "SystemFiles";
variablesCatalogos($array, 89, FALSE, $sessionVars, $otherFilter); /* Array, Id de Qrys */
$request = utils\HTTPUtils::getRequest();

if($request->getAttribute("busca") === "ini"){
    $usuarioSesion = getSessionUsuario();
    BitacoraDAO::getInstance()->saveLog($usuarioSesion, 'ARCHIVOS', 'ACCESO A SISTEMA DE ARCHIVOS', "", 0);
}

#Variables comunes;
$Folder = $_SESSION[$sessionVars]['FolderSession'];
$File = $_SESSION[$sessionVars]['FileSession'];
$user_id = $_SESSION["ID_USUARIO"];

$aCps = explode(",", $Qry[campos]);  // Es necesario para hacer el order by  desde lib;
$aIzq = array(" ", "-", "-");        //Arreglo donde se meten los encabezados; Izquierdos
$aDat = explode(",", $Qry[edi]);   //Arreglo donde llena el grid de datos        
$aDer = array(" ", "-", "-");     //Arreglo donde se meten los encabezados; Derechos;
$tamPag = $Qry[tampag];

$title_file = "Mis archivos";
$array_path = array();

if (!empty($Folder)) {
    $query = "SELECT * FROM " . $Qry['froms'] . " WHERE id_file = '" . $Folder . "'";
    //error_log($query);
    $files_file = FetchQuery(Query($mysqli, $query, TRUE));

    $title_file = $files_file['filename_file'];

    if (!empty($files_file['folder_id_file'])) {

        $recursive_sql = "SELECT getfiles('" . $Folder . "');";
        $recursive_folders = FetchQuery(Query($mysqli, $recursive_sql, TRUE));

        $explode = explode("//", $recursive_folders[0]);
        foreach ($explode as $Key) {
            $explode_key = explode("|", $Key);
            $id_file = $explode_key[0];
            $array_path[$id_file] = $explode_key[1];
        }
    } else {
        $array_path[$files_file['id_file']] = $files_file['filename_file'];
    }
}

$cSql = "SELECT $Qry[campos],id_file,is_folder_file 
         FROM $Qry[froms] WHERE 1=1 AND user_id_file = '" . $user_id . "'";
if (!empty($Folder)) {
    $cSql .= " AND folder_id_file = '" . $Folder . "'";
} else {
    $cSql .= " AND folder_id_file IS NULL ";
}

$numeroRegistros = Query($mysqli, $cSql, TRUE, FALSE, TRUE);

CalculaLimitesPaginacion($numeroRegistros);

$sql = $cSql . $cWhe . " ORDER BY " . $OrdenDef . " $Sort LIMIT " . $limitInf . "," . $tamPag;
//error_log($sql);
$resultDataGrid = Query($mysqli, $sql, TRUE);
