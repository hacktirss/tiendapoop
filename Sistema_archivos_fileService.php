<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FilesFileDAO.php");

use com\softcoatl\utils as utils;

$mysqli = conectarse();
$request = utils\HTTPUtils::getRequest();

#Variables comunes;
$Titulo = "Detalle del archivo";
$sessionVars = "SystemFiles";
$Folder = $_SESSION[$sessionVars]['FolderSession'];
if (!empty(filter_input(INPUT_GET, 'FileSession', FILTER_SANITIZE_STRING))) {
    $_SESSION[$sessionVars]['FileSession'] = $request->getAttribute("FileSession");
}elseif(!empty(filter_input(INPUT_GET, 'idFile', FILTER_SANITIZE_STRING))) {
    $_SESSION[$sessionVars]['FileSession'] = $request->getAttribute("idFile");
}
$File = $_SESSION[$sessionVars]['FileSession'];

$filesFileDAO = new FilesFileDAO();
$filesFileVO = $filesFileDAO->retrieve($File);

$title_file = $files_file['filename_file'];

$array_path = array();
if (!empty($filesFileVO->getFolder_id())) {

    $recursive_sql = "SELECT getfiles('" . $filesFileVO->getFolder_id() . "');";
    $recursive_folders = FetchQuery(Query($mysqli, $recursive_sql, TRUE));

    $explode = explode("//", $recursive_folders[0]);
    foreach ($explode as $Key) {
        $explode_key = explode("|", $Key);
        $id_file = $explode_key[0];
        $array_path[$id_file] = $explode_key[1];
    }
} 

$filesCommentsArray = array();



