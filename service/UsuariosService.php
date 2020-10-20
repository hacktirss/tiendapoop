<?php

#Librerias
include_once ("data/UsuarioDAO.php");
include_once ("data/SubmenuDAO.php");
include_once ("data/MenuDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$usuarioSesion = getSessionUsuario();
$Return = "usuarios.php?";

$usuarios = new Usuarios();
$usuarioPerfilDAO = new UsuarioPerfilDAO();
$menuDAO = new MenuDAO();
$submenuDAO = new SubmenuDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $usuarioVO = new UsuarioVO();
    $usuarioVO->setCia($usuarioSesion->getCia());
    $usuarioVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($usuarioVO->getId())) {        
        $usuarioVO = $usuarios->getUser($usuarioVO->getId());
    }

    //error_log(print_r($usuarioVO, TRUE));
    try {

        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {

            $usuarioVO->setNombre($sanitize->sanitizeString("Name"));
            $usuarioVO->setUsername($sanitize->sanitizeString("Uname"));
            $usuarioVO->setMail($sanitize->sanitizeEmail("Mail"));
            $usuarioVO->setPassword($sanitize->sanitizeString("Passwd"));
            //$usuarioVO->setLevel($sanitize->sanitizeInt("Level"));
            $usuarioVO->setStatus($sanitize->sanitizeString("Status"));
            $usuarioVO->setCreation(date("Y-m-d", strtotime(date("Y-m-d"))));
            //$usuarioVO->setLastlogin("0000-00-00 00:00:00");
            $usuarioVO->setCount(0);

            $response = $usuarios->addUser($usuarioVO);
            if ($response === Usuarios::RESPONSE_VALID) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "CREACION USUARIO: " . $usuarioVO->getNombre());
            } else {
                $Msj = $response;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            $usuarioVO->setNombre($sanitize->sanitizeString("Name"));
            $usuarioVO->setUsername($sanitize->sanitizeString("Uname"));
            $usuarioVO->setMail($sanitize->sanitizeEmail("Mail"));
            $usuarioVO->setTeam($sanitize->sanitizeString("Team"));
            $usuarioVO->setStatus($sanitize->sanitizeString("Status"));
            $usuarioVO->setLevel($sanitize->sanitizeInt("Level"));

            $response = $usuarios->updateUser($usuarioVO);
            if ($response === Usuarios::RESPONSE_VALID) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "ACTUALIZACION USUARIO: " . $usuarioVO->getNombre());
            } else {
                $Msj = $response;
            }
        } elseif ($request->getAttribute("Boton") === "Cambiar") {
            $usuarioVO->setPassword($sanitize->sanitizeString("Passwd"));

            $response = $usuarios->changePasswordUser($usuarioVO);
            if ($response === Usuarios::RESPONSE_VALID) {
                $Msj = "Se ha cambiado la contraseña con EXITO!";
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "CAMBIO DE CONTRASEñA: " . $usuarioVO->getNombre());
            } else {
                $Msj = $response;
            }
        }

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en usuarios: " . $ex);
    } finally {
        header("Location: $Return");
    }
}

if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $usuarioVO = new UsuarioVO();
    $usuarioVO->setId($sanitize->sanitizeInt("busca"));
    if (is_numeric($usuarioVO->getId())) {       
        $usuarioVO =  $usuarios->getUser($usuarioVO->getId());
    }

    try {
        if ($request->getAttribute("BotonD") === utils\Messages::OP_UPDATE) {
            $selectAll = "SELECT * FROM menus ORDER BY id;";
            $menusVO = $menuDAO->getAll($selectAll);
            foreach ($menusVO as $menuVO) {
                $usuarioPerfilVO = new UsuarioPerfilVO();
                $usuarioPerfilVO->setIdUsuario($usuarioVO->getId());
                $usuarioPerfilVO->setIdMenu($menuVO->getId());
                $usuarioPerfilVO->setPermisos(returnSelected($menuVO->getId()));
                $usuarioPerfilVO->setEditable(returnSelected($menuVO->getId(),"_0"));

                if($usuarioPerfilDAO->create($usuarioPerfilVO) > 0){
                    $Msj = utils\Messages::MESSAGE_DEFAULT;
                }else{
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            }

            
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en usuarios: " . $ex);
    } finally {
        header("Location: $Return");
    }
}
if ($request->hasAttribute("Cambiar") && $request->getAttribute("Cambiar") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $Return = "cambio_pass.php?";

    $usuarioVO = new UsuarioVO();
    $usuarioVO->setId($usuarioSesion->getId());
    if (is_numeric($usuarioVO->getId())) {        
        $usuarioVO = $usuarios->getUser($usuarioVO->getId());
    }
    //error_log(print_r($usuarioVO, TRUE));
    try {

        if ($request->getAttribute("Cambiar") === "Cambiar contraseña") {
            $currentPassword = utils\HTTPUtils::getCookieValueValue(Usuarios::SESSION_PASSWORD);

            if ($request->getAttribute("actual") === $currentPassword) {
                $usuarioVO->setPassword($request->getAttribute("nuevo"));

                $response = $usuarios->changePasswordUser($usuarioVO);
                if ($response === Usuarios::RESPONSE_VALID) {
                    BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "CAMBIO DE CONTRASEñA: " . $usuarioSesion->getUsername());
                    $Return = "index.php?";
                    $Msj = 4;
                } else {
                    $Msj = $response;
                }
            } else {
                $Msj = "Credenciales invalidas, la contraseña actual ingresada no es correcta.";
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "INTENTO DE CAMBIO DE CONTRASEñA: " . $usuarioSesion->getUsername());
            }
        }

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en usuarios: " . $ex);
    } finally {
        header("Location: $Return");
    }
}

if ($request->hasAttribute("op")) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $cId = $sanitize->sanitizeInt("cId");
    $usuarioVO = $usuarios->getUser($cId);
    //error_log(print_r($usuarioVO, TRUE));
    try {
        if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
            $usuarioVO->setLevel(UsuarioDAO::LEVEL_DISABLE);
            $usuarioVO->setStatus(StatusUsuario::INACTIVO);
            //error_log(print_r($usuarioVO, TRUE));
            $response = $usuarios->updateUser($usuarioVO);
            if ($response === Usuarios::RESPONSE_VALID) {
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "ELIMINACION DE USUARIO: " . $cId);
                $Msj = utils\Messages::RESPONSE_VALID_DELETE;
            } else {
                $Msj = $response;
            }
        } elseif ($request->getAttribute("op") === "unlock") {
            $usuarioVO->setLocked(0);
            $usuarioVO->setAlive(0);
            $response = $usuarios->updateUser($usuarioVO);
            if ($response === Usuarios::RESPONSE_VALID) {
                BitacoraDAO::getInstance()->saveLog($usuarioSesion, "ADM", "DESBLOQUEO DE USUARIO: " . $cId);
                $Msj = "Registro desbloqueado con Exito!";
            } else {
                $Msj = $response;
            }
        }

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error en usuarios: " . $ex);
    } finally {
        header("Location: $Return");
    }
}

/**
 * 
 * @global utils\IConnection $mysqli
 * @global utils\HTTPUtils $request
 * @param int $idSubmenu
 * @return int
 */
function returnSelected($idSubmenu, $extra = "") {
    global $mysqli, $request;
    $i = 0;
    $arreglo = $var = null;

    $selectSubmenu = "
            SELECT menus.nombre,submenus.submenu,submenus.id 
            FROM submenus 
            LEFT JOIN menus on menus.id=submenus.menu 
            WHERE menus.id = $idSubmenu 
            ORDER BY submenus.posicion";
    $sesult = $mysqli->query($selectSubmenu);

    while ($rg = $sesult->fetch_array()) {
        $arreglo[$i] = 0;
        $submenu = str_replace(array(" ",".","-"), "", $rg[submenu]) . $rg[id] . $extra;
        if ($request->hasAttribute($submenu)) {
            $arreglo[$i] = 1;
        } 
        $var .= $arreglo[$i];
        $i++;
    }

    $len = 25 - (int) strlen($var);
    for ($i = 1; $i <= $len; $i++) {
        $var .= 0;
    }

    return $var;
}
