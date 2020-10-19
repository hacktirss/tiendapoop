<?php
#Librerias
include_once ("lib/lib.php");
include_once ("auth.php");
include_once ("authconfig.php");

initSessionProject();

use com\softcoatl\utils as utils;

$username = utils\HTTPUtils::getSessionValue(Usuarios::SESSION_USERNAME);
$password = utils\HTTPUtils::getSessionValue(Usuarios::SESSION_PASSWORD);

$auth = new Auth();
$auth->setUsername($username);
$auth->setPassword($password);

$usuarioVO = $auth->authenticate();
$redirect = $success;
//error_log(print_r($usuarioVO,true));
if (Usuarios::CHANGE_PASS) {
    if (empty($usuarioVO->getLastlogin()) || $usuarioVO->getCount() == 0 || $auth->isExpired($usuarioVO)) {
        $redirect = $changepassword . "?user=" . $usuarioVO->getId();
    } 
}

$ip = getRealUserIp("localhost", FILTER_FLAG_NO_RES_RANGE);
BitacoraDAO::getInstance()->saveLog($usuarioVO, "ACCESSO", "LOGIN EXITOSO", "", 0, $ip);

ini_set("session.cookie_lifetime", "0");
utils\HTTPUtils::setSessionValue(SESSION_USUARIO, serialize($usuarioVO));

$ciasDAO = new CiasDAO();
$ciaVO = $ciasDAO->retrieve($usuarioVO->getCia());
utils\HTTPUtils::setSessionValue(SESSION_CIA, serialize($ciaVO));

$selectSubmenus = "
                SELECT menus.nombre,sub.menu,sub.posicion,sub.submenu,sub.url,sub.permisos 
                FROM menus,submenus sub 
                WHERE menus.id = sub.menu
                ORDER BY sub.menu,sub.posicion;";
$rows = utils\ConnectionUtils::getRowsFromQuery($selectSubmenus);
$submenus = array();
foreach ($rows as $value) {
    $submenus[$value[menu]]["menu"] = $value["nombre"];
    $submenus[$value[menu]][$value[posicion]]["submenu"] = $value["submenu"];
    $submenus[$value[menu]][$value[posicion]]["url"] = $value["url"];
    $submenus[$value[menu]][$value[posicion]]["permisos"] = $value["permisos"];
}

$selectPermisos = "
                SELECT auth.id_menu,menus.tipo,auth.permisos,auth.editable
                FROM authuser_conf auth,menus 
                WHERE auth.id_menu = menus.id AND auth.id_user = " . $usuarioVO->getId() . "
                ORDER BY auth.id_menu;";
$rows_ = utils\ConnectionUtils::getRowsFromQuery($selectPermisos);
$permisos = array();
foreach ($rows_ as $value) {
    $permisos[$value[id_menu]]["menu"] = $value["id_menu"];
    $permisos[$value[id_menu]]["permisos"] = $value["permisos"];
    $permisos[$value[id_menu]]["editable"] = $value["editable"];
    $permisos[$value[id_menu]]["tipo"] = $value["tipo"];
}
//error_log(print_r($permisos, TRUE));
utils\HTTPUtils::setSessionValue("SUBMENUS", $submenus);
utils\HTTPUtils::setSessionValue("PERMISOS", $permisos);
?>

<!DOCTYPE html>
<html lang = "es" xml:lang = "es">
    <head>
        <title>Autenticacion</title>
        <script>
            location.replace("<?= $redirect ?>");
        </script>
    </head>
</html>
