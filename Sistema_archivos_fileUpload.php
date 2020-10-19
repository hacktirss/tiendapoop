<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FilesFileDAO.php");
include_once ("ficheros/class.upload.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$UsuarioSesion = getSessionUsuario();

if (!empty(filter_input(INPUT_POST, 'Boton', FILTER_SANITIZE_STRING))) {
    $Msj = "";
    $filesFileDAO = new FilesFileDAO();

    $is_public = $request->getAttribute("is_public") ? 1 : 0;
    $folder_id = $Folder !== "" ? $Folder : NULL;
    $user_id = $UsuarioSesion->getId();
    $description = $request->getAttribute("description");

    $filesFileVO = new FilesFileVO();
    $filesFileVO->setIs_public($is_public);
    $filesFileVO->setIs_folder($request->getAttribute("type"));
    $filesFileVO->setUser_id($user_id);
    $filesFileVO->setDescription($description);
    $filesFileVO->setFolder_id($folder_id);

    if ($request->getAttribute("type") === "0") {
        $handle = new Upload($_FILES['filename']);
        if ($handle->uploaded) {
            $url = "ficheros/data/" . $user_id;
            $handle->Process($url);
            if ($handle->processed) {
                $filesFileVO->setFilename($handle->file_dst_name);
                $filesFileVO->setSize($handle->file_src_size);
                //error_log(print_r($filesFileVO, TRUE));
                $id = $filesFileDAO->create($filesFileVO);
                if ($id > 0) {
                    BitacoraDAO::getInstance()->saveLog($UsuarioSesion, "ARCHIVOS", "CREACION DE ARCHIVO [" . $filesFileVO->getFilename() . "]", "", 0);
                    $Msj = $Msj = Messages::MESSAGE_CREATE_OK;
                } else {
                    $Msj = Messages::MESSAGE_ERROR;
                }
            } else {
                error_log($handle->error);
            }
            error_log($handle->log);
        }
    } else {
        $filesFileVO->setFilename($request->getAttribute("filename"));
        $filesFileVO->setSize(0);
        error_log(print_r($filesFileVO, TRUE));
        if (!$filesFileDAO->existsFolder($filesFileVO)) {
            $id = $filesFileDAO->create($filesFileVO);
            BitacoraDAO::getInstance()->saveLog($UsuarioSesion, "ARCHIVOS", "CREACION DE CARPETA [" . $filesFileVO->getFilename() . "]", "", 0);
            if ($id > 0) {
                $Msj = Messages::MESSAGE_CREATE_OK;
            } else {
                $Msj = Messages::MESSAGE_ERROR;
            }
        } else {
            $Msj = Messages::MESSAGE_FOLDER_EXISTS;
        }
    }
    header("Location: sistema_archivos.php?FolderSession=" . $Folder . "&Msj=" . urlencode($Msj));
}
?>
