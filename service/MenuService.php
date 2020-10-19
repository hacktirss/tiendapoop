<?php

include_once ("data/MenuDAO.php");
include_once ("data/SubmenuDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();
$Return = "menus.php?";
$Return2 = "menusd.php?";

$menuDAO = new MenuDAO();
$submenuDAO = new SubmenuDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $menuVO = new MenuVO();
    $menuVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($menuVO->getId())) {
        $menuVO = $menuDAO->retrieve($menuVO->getId());
    }

    //error_log(print_r($menuVO, TRUE));
    try {
        $menuVO->setNombre($sanitize->sanitizeString("Nombre"));
        $menuVO->setDescripcion($sanitize->sanitizeString("Descripcion"));
        $menuVO->setOrden($sanitize->sanitizeInt("Orden"));
        $menuVO->setTipo($sanitize->sanitizeInt("Tipo"));

        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $menuDAO->create($menuVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            if (($menuDAO->update($menuVO))) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en Menus: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return)) {
            header("Location: $Return");
        }
    }
}

$nameSession = "configMenusD";
$cVarVal = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $submenuVO = new SubmenuVO();
    $submenuVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($submenuVO->getId())) {
        $submenuVO = $submenuDAO->retrieve($submenuVO->getId());
    }

    try {
        $submenuVO->setMenu($cVarVal);
        $submenuVO->setSubmenu($sanitize->sanitizeString("Submenu"));
        $submenuVO->setUrl($sanitize->sanitizeString("Direccion"));
        $submenuVO->setPosicion($sanitize->sanitizeInt("Posicion"));
        $submenuVO->setPermisos($sanitize->sanitizeInt("Permisos"));

        //error_log(print_r($submenuVO, TRUE));
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {
            if (($id = $submenuDAO->create($submenuVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("BotonD") === utils\Messages::OP_UPDATE) {
            if (($submenuDAO->update($submenuVO))) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return2 .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en MenusD: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return2)) {
            header("Location: $Return2");
        }
    }
}

if ($request->hasAttribute("op")){
    $cId = $sanitize->sanitizeInt("cId");
    try {
        
        if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
            if ($menuDAO->remove($cId)) {
                $Msj = utils\Messages::RESPONSE_VALID_DELETE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en Menus: " . $ex);
    } finally {
        if ($mysqli->errno > 0) {
            error_log($mysqli->error);
        }
        if (!is_null($Return)) {
            header("Location: $Return");
        }
    }
}
