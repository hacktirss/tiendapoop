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
$Msj = Messages::MESSAGE_NO_OPERATION;

if (!empty($filesFileVO->getId())) {

    if (!$filesFileVO->getIs_folder()) {
        unlink($url . $filesFileVO->getFilename());
        if ($filesFileDAO->remove($request->getAttribute("idFile"))) {
            BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ARCHIVOS", "BORRADO DE ARCHIVO [" . $filesFileVO->getFilename() . "]", "", 0);
            $Msj = Messages::MESSAGE_DELETE_OK;
        } else {
            $Msj = Messages::MESSAGE_ERROR;
        }
    } else {
        $arrayFile = $filesFileDAO->getAllByFolder($request->getAttribute("idFile"));
        foreach ($arrayFile as $fileVO) {
            unlink($url . $fileVO->getFilename());
        }
        if ($filesFileDAO->removeByFolder($request->getAttribute("idFile"))) {
            if ($filesFileDAO->remove($request->getAttribute("idFile"))) {
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ARCHIVOS", "BORRADO DE CARPETA [" . $filesFileVO->getFilename() . "]", "", 0);
                $Msj = Messages::MESSAGE_DELETE_OK;
            } else {
                $Msj = Messages::MESSAGE_ERROR;
            }
        } else {
            $Msj = Messages::MESSAGE_ERROR;
        }
    }
}

header("Location: sistema_archivos.php?Msj=" . $Msj);
