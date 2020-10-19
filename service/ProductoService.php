<?php

#Librerias
include_once ("data/ProductoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "productos.php?";
$usuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$objectDAO = new ProductoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new ProductoVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $objectVO->setRfc($CiaSesion->getRfc());
    $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));
    $objectVO->setUmedida($sanitize->sanitizeString("Umedida"));
    $objectVO->setPrecio($sanitize->sanitizeString("Precio"));
    $objectVO->setCosto($sanitize->sanitizeString("Costo"));
    $objectVO->setIva($sanitize->sanitizeInt("Iva"));
    $objectVO->setCostopromedio($sanitize->sanitizeString("Costopromedio"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setExistencia($sanitize->sanitizeString("Existencia"));
    $objectVO->setDlls($sanitize->sanitizeString("Dlls"));
    $objectVO->setGrupo($sanitize->sanitizeString("Grupo"));
    $objectVO->setActivo($sanitize->sanitizeString("Activo"));
    $objectVO->setInv_cunidad($sanitize->sanitizeString("Inv_cunidad"));
    $objectVO->setInv_cproducto($sanitize->sanitizeString("Inv_cproducto"));


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

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    }


    header("Location: $Return");
}