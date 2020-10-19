<?php

include_once ("data/ListaDAO.php");
include_once ("data/ListaValoresDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();
$Return = "listas.php?";
$Return2 = "listasd.php?";

$objectDAO = new ListaDAO();
$objectDDAO = new ListaValoresDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectVO = new ListaVO();
    $objectVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($objectVO->getId())) {
        $objectVO = $objectDAO->retrieve($objectVO->getId());
    }

    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));
    $objectVO->setDefault($sanitize->sanitizeString("Default"));
    $objectVO->setTipo_dato($sanitize->sanitizeString("Tipo_dato"));
    $objectVO->setLongitud($sanitize->sanitizeInt("Longitud"));
    $objectVO->setEstado($sanitize->sanitizeInt("Estado"));
    $objectVO->setMayus($sanitize->sanitizeInt("Mayus"));
    $objectVO->setMin($sanitize->sanitizeInt("Min"));
    $objectVO->setMax($sanitize->sanitizeInt("Max"));


    //error_log(print_r($objectVO, TRUE));
    try {

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

$nameSession = "configListasD";
$cVarVal = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectVO = new ListaValoresVO();
    $objectVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($objectVO->getId())) {
        $objectVO = $objectDDAO->retrieve($objectVO->getId());
    }

    $objectVO->setId_lista($cVarVal);
    $objectVO->setLlave($sanitize->sanitizeString("Llave"));
    $objectVO->setValor($sanitize->sanitizeString("Valor"));
    $objectVO->setEstado($sanitize->sanitizeString("Estado"));

    try {
        //error_log(print_r($objectVO, TRUE));
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {
            if (!$objectDDAO->existsKey($objectVO)) {
                if (($id = $objectDDAO->create($objectVO)) > 0) {
                    $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = str_replace("?", utils\Messages::REGISTER_DUPLICATE, $objectVO->getLlave());
            }
        } elseif ($request->getAttribute("BotonD") === utils\Messages::OP_UPDATE) {
            if (!$objectDDAO->existsKey($objectVO)) {
                if (($objectDDAO->update($objectVO))) {
                    $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = str_replace("?", utils\Messages::REGISTER_DUPLICATE, $objectVO->getLlave());
            }
        }
        $Return2 .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return2)) {
            header("Location: $Return2");
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
