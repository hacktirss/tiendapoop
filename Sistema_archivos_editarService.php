<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FilesFileDAO.php");
include_once ("ficheros/class.upload.php");

use com\softcoatl\utils as utils;

$mysqli = conectarse();

#Variables comunes;
$sessionVars = "SystemFiles";
$Folder = $_SESSION[$sessionVars]['FolderSession'];

$Titulo = "Editar archivo";
$request = utils\HTTPUtils::getRequest();
$File = $request->getAttribute("idFile");
$usuarioSesion = getSessionUsuario();
$filesFileVO = new FilesFileVO();
if (!empty($File)) {

    $filesFileDAO = new FilesFileDAO();
    $filesFileVO = $filesFileDAO->retrieve($File);
    if ($filesFileVO->getIs_folder()) {
        $Titulo = "Editar carpeta";
    }

    $Msj = Messages::MESSAGE_NO_OPERATION;
    if (!empty(filter_input(INPUT_POST, 'Boton', FILTER_SANITIZE_STRING))) {

        $is_public = $request->getAttribute("is_public") ? 1 : 0;
        $user_id = $_SESSION["ID_USUARIO"];

        $filesFileVO->setDescription($request->getAttribute("description"));
        $filesFileVO->setIs_public($is_public);

        if ($filesFileVO->getIs_folder()) {
            $filesFileVO->setFilename($request->getAttribute("filename"));
            //error_log(print_r($filesFileVO, TRUE));
            if (!$filesFileDAO->existsFolder($filesFileVO)) {
                if ($filesFileDAO->update($filesFileVO)) {
                    BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ARCHIVOS", "ACTUALIZACION DE CARPETA [" . $request->getAttribute("filename") . "]", "", 0);
                    $Msj = Messages::MESSAGE_UPDATE_OK;
                } else {
                    $Msj = Messages::MESSAGE_ERROR;
                }
            } else {
                $Msj = Messages::MESSAGE_FOLDER_EXISTS;
            }
        } else {
            $url = "ficheros/data/" . $user_id;
            unlink($url . "/" . $filesFileVO->getFilename());

            $handle = new Upload($_FILES['filename']);
            if ($handle->uploaded) {
                $handle->Process($url);
                if ($handle->processed) {
                    $filesFileVO->setFilename($handle->file_dst_name);
                    $filesFileVO->setSize($handle->file_src_size);
                    //error_log(print_r($filesFileVO, TRUE));

                    if ($filesFileDAO->update($filesFileVO)) {
                        BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ARCHIVOS", "ACTUALIZACION DE CARPETA [" . $handle->file_dst_name . "]", "", 0);
                        $Msj = $Msj = Messages::MESSAGE_UPDATE_OK;
                    } else {
                        $Msj = Messages::MESSAGE_ERROR;
                    }
                } else {
                    error_log($handle->error);
                }
                error_log($handle->log);
            }
        }

        header("Location: sistema_archivos.php?Msj=" . urlencode($Msj));
    }
}

$array_path = array();

if (!empty($Folder)) {
    //error_log("Folder: " . $Folder);
    $recursive_sql = "SELECT getfiles('" . $Folder . "');";
    $recursive_folders = FetchQuery(Query($mysqli, $recursive_sql, TRUE));
    //error_log(print_r($recursive_folders, TRUE));

    $explode = explode("//", $recursive_folders[0]);
    //error_log(print_r($explode, TRUE));
    foreach ($explode as $Key) {
        $explode_key = explode("|", $Key);
        $id_file = $explode_key[0];
        $array_path[$id_file] = $explode_key[1];
    }
    //error_log(print_r($array_path, TRUE));
}

    

