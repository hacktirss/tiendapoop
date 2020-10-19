<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

include_once ("data/FilesFileDAO.php");

use com\softcoatl\utils as utils;

$mysqli = conectarse();

#Variables comunes;
$sessionVars = "SystemFiles";
$Folder = $_SESSION[$sessionVars]['FolderSession'];
$Titulo = "Nuevo archivo";
$request = utils\HTTPUtils::getRequest();
$type = $request->getAttribute("type");

if (!empty(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING))) {
    $filesFileDAO = new FilesFileDAO();  
    if ($type === "1") {
        $Titulo = "Nueva carpeta";
    }
}

$array_path = array();

if (!empty($Folder)) {
    
    $recursive_sql = "SELECT getfiles('" . $Folder . "');";
    $recursive_folders = FetchQuery(Query($mysqli, $recursive_sql, TRUE));
    
    $explode = explode("//", $recursive_folders[0]);

    foreach ($explode as $Key) {
        $explode_key = explode("|", $Key);
        $id_file = $explode_key[0];
        $array_path[$id_file] = $explode_key[1];
    }
}

    

