<?php

#Librerias
include_once ("lib/lib.php");

use com\softcoatl\utils as utils;

$sanitize = SanitizeUtil::getInstance();

$username = $sanitize->sanitizeString("username");
$password = $sanitize->sanitizeString("password");

$jsonString = Array();
$jsonString["success"] = false;
$jsonString["message"] = "";
$jsonString["count"] = 0;
$jsonString["redirect"] = "vAuthenticate.php";

try {
    $sessionProject = initSessionProject();
    
    $usuarioDAO = new UsuarioDAO();
    $usuarioVO_U = $usuarioDAO->findByUname($username);

    if ($usuarioVO_U != null) {
        $usuarioVO = $usuarioDAO->finfByUnameAndPassword($username, $password);
        if ($usuarioVO != null && $usuarioVO->getStatus() === StatusUsuario::ACTIVO && $usuarioVO->getLocked() < Usuarios::MAX_INTENTS_LOGIN) {
            $jsonString["success"] = true;
            if (Usuarios::IS_ALIVE) {
                $fecha_hora_10 = date("Y-m-d H:i:s", strtotime("-" . Usuarios::WAIT_MINUTES . " minute", strtotime(date("Y-m-d H:i:s"))));

                if ($usuarioVO->getAlive() == StatusSesion::ALIVE && $fecha_hora_10 < $usuarioVO->getLastactivity()) {
                    $jsonString["success"] = false;
                    $Msj = str_replace("?", $usuarioVO->getUsername(), utils\Messages::RESPONSE_USER_ALIVE);
                }
            }
            utils\HTTPUtils::setSessionValue(Usuarios::SESSION_USERNAME, $username);
            utils\HTTPUtils::setSessionValue(Usuarios::SESSION_PASSWORD, $password);
        } else {
            $usuarioDAO->updateLocked($usuarioVO_U);
            BitacoraDAO::getInstance()->saveLog($usuarioVO_U, "ACCESSO", "LOGIN FALLIDO", "", AlarmasDAO::VAL20);
            $jsonString["count"] = $usuarioVO_U->getLocked();
            if ($usuarioVO_U->getLocked() < Usuarios::MAX_INTENTS_LOGIN) {
                $Msj = utils\Messages::RESPONSE_USER_DATA_INVALID;
            } else {
                $Msj = utils\Messages::RESPONSE_USER_MAX_INTENTS;
            }
        }
    } else {
        $usuarioVO = new UsuarioVO();
        $usuarioVO->setNombre($username);
        BitacoraDAO::getInstance()->saveLog($usuarioVO, "ACCESSO", "LOGIN FALLIDO", "", AlarmasDAO::VAL20);
        $Msj = utils\Messages::RESPONSE_USER_DATA_INVALID;
    }
} catch (Exception $exc) {
    $Msj = "A ocurrido un error";
} finally {
    $jsonString["message"] = $Msj;
    echo json_encode($jsonString);
}


