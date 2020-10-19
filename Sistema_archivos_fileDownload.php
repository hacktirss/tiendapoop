<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FilesFileDAO.php");
include_once ("ficheros/class.upload.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
error_log(print_r($request, TRUE));


$filesFileDAO = new FilesFileDAO();
$filesFileVO = $filesFileDAO->retrieve($request->getAttribute("idFile"));
$usuarioSesion = getSessionUsuario();
$url = "ficheros/data/" . $filesFileVO->getUser_id() . "/";
$filename = $filesFileVO->getFilename();

if (!$filesFileVO->getIs_folder()) {
    BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ARCHIVOS", "DESCARGA DE ARCHIVO [" . $filename . "]", "", 0);
    $fullurl = $url . $filename;
    header("Content-Disposition: attachment; filename=$filename");
    readfile($fullurl); // or echo file_get_contents($filename);
}
