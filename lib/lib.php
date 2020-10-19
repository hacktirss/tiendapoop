<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set("America/Mexico_City");
set_time_limit(720);
define("PROJECT_NAME", "TIENDAPOOP");
define("SESSION_USUARIO", "USUARIO");
define("DETALLE", "cVarVal");

include_once ("softcoatl/config.php");
include_once ("softcoatl/Utilerias.php");
include_once ("softcoatl/SanitizeUtil.php");
include_once ("softcoatl/Messages.php");
include_once ("softcoatl/ConnectionUtils.php");
include_once ("softcoatl/Session.php");
include_once ("softcoatl/SoftcoatlHTTP.php");


/**
 * 
 * @return Session
 */
function initSessionProject() {
    return Session::getInstance(PROJECT_NAME);
}

/**
 * 
 * @global Session $sessionProject
 */
function destroySessionProject() {
    global $sessionProject;
    return $sessionProject->destroy();
}

use com\softcoatl\utils as utils;

/**
 * 
 * @return mysqli
 */
function conectarse() {
    date_default_timezone_set("America/Mexico_City");
    return utils\IConnection::getConnection();
}
