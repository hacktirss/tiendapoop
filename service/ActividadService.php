<?php

include_once ("data/ActividadDAO.php");
include_once ("data/ActividadDDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$usuarioSesion = getSessionUsuario();

$Return = "actividades.php?";

$objectDAO = new ActividadDAO();
$objectDDAO = new ActividadDDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectVO = new ActividadVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    //error_log(print_r($objectVO, TRUE));
    try {
        $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));
        $objectVO->setTipo($sanitize->sanitizeString("Tipo"));
        $objectVO->setPeriodo($sanitize->sanitizeInt("Periodo"));
        $objectVO->setLapso($sanitize->sanitizeInt("Lapso"));
        $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));

        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            if (($objectDAO->update($objectVO))) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return)) {
            header("Location: $Return");
        }
    }
}

$nameSession = "CatalogoActividadesD";
$cVarVal = utils\HTTPUtils::getSessionBiValue($nameSession, "cValVar");

if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Return = "actividadesd.php?";
    
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectDVO = new ActividadDVO();
    $objectDVO->setIdnvo($sanitize->sanitizeInt("busca"));
    if (is_numeric($objectDVO->getIdnvo())) {
        $objectDVO = $objectDDAO->retrieve($objectDVO->getIdnvo());
    }

    try {
        $objectDVO->setActividad($cVarVal);
        $objectDVO->setFecha($sanitize->sanitizeString("Fecha"));
        $objectDVO->setConcepto($sanitize->sanitizeString("Concepto"));
        $objectDVO->setObservaciones($sanitize->sanitizeString("Observaciones"));

        //error_log(print_r($objectDVO, TRUE));
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {
            if (($id = $objectDDAO->create($objectDVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("BotonD") === utils\Messages::OP_UPDATE) {
            if (($objectDDAO->update($objectDVO))) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return)) {
            header("Location: $Return");
        }
    }
}

if ($request->hasAttribute("op")) {
    $cId = $sanitize->sanitizeInt("cId");
    try {

        if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
            if ($objectDAO->remove($cId)) {
                $Msj = utils\Messages::RESPONSE_VALID_DELETE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return)) {
            header("Location: $Return");
        }
    }
}
