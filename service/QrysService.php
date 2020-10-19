<?php

include_once ("data/QrysDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "querys.php?";

$objectDAO = new QrysDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new QrysVO();
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"));
    }

    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setCampos($sanitize->sanitizeString("Campos"));
    $objectVO->setFroms($request->getAttribute("Froms"));
    $objectVO->setEdi($request->getAttribute("Edi"));
    $objectVO->setTampag($sanitize->sanitizeInt("Tampag"));
    $objectVO->setAyuda($sanitize->sanitizeInt("Ayuda"));
    $objectVO->setJoins($sanitize->sanitizeString("Joins"));

    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;               
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            if ($objectDAO->update($objectVO)) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } 
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}
