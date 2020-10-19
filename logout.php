<?php
session_start();
include_once ("lib/lib.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$usuarioSesion = getSessionUsuario();
$usuarioDAO = new UsuarioDAO();
$Msj = 0;

if($request->hasAttribute("timeout")){
    $Msj = 3;
    if($usuarioSesion != null){
        $usuarioDAO->freeAlive($usuarioSesion);
        BitacoraDAO::getInstance()->saveLog($usuarioSesion, 'ACCESSO', 'SESION EXPIRADA');
    }
    $_SESSION = array();
} else {
    $Msj = 5;
    if($usuarioSesion != null){
        $usuarioDAO->freeAlive($usuarioSesion);
        BitacoraDAO::getInstance()->saveLog($usuarioSesion, 'ACCESSO', 'LOGOUT EXITOSO');
    }
    $_SESSION = array();
}
header("Location: index.php?Msj=" . $Msj);
