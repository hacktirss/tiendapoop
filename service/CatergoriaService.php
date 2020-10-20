<?php

#Librerias
include_once ("data/CategoriaDAO.php");
include_once ("data/SubcategoriaDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "categorias.php?";
$usuarioSesion = getSessionUsuario();

$objectDAO = new CategoriaDAO();
$objectDDAO = new SubcategoriaDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new CategoriaVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        //$objectVO->setId(IncrementaId($objectDAO::TABLA));
    }

    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));

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


if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Return = "categoriasd.php?";        
    
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    
    $nameSession = "catalogoCategoriasD";
    $cVarVal = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

    $objectVO = new SubcategoriaVO();
    $objectVO->setCategoria($cVarVal);
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        //$objectVO->setId(IncrementaId($objectDAO::TABLA));
    }

    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));

    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {
            if (($id = $objectDDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("BotonD") === utils\Messages::OP_UPDATE) {
            if ($objectDDAO->update($objectVO)) {
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