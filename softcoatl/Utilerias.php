<?php

use com\softcoatl\utils as utils;

function getOS() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $os_platform = "Unknown OS Platform";

    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    $os_platform = strtoupper(PHP_OS);
    return $os_platform;
}

function getBrowser() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $browser = "Unknown Browser";

    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    return $browser;
}

function ejecutarShell($cmd, $sleep = TRUE, $getOutput = false) {
    $output = "";
    if (substr(php_uname(), 0, 7) == "Windows") {
        $output = exec("c:\\windows\\system32\\cmd.exe /c $cmd");
    } else {
        $output = exec("sudo " . $cmd, $output, $code);
        if ($sleep) {
            exec("sleep 1s");
        }
    }

    if ($code == 0) {
        if ($getOutput) {
            return $output;
        } else {
            return "Executed command \"" . $output . "\" Code: Success";
        }
    } else {
        return "Executed command \"" . $output . "\" Code: Error";
    }
}

/**
 * @param type $connection Coneccion de tipo mysqli
 * @param type $query Cadena que será ejecutada
 * @param type $fetch TRUE si desea devolver el arrar de datos. Si no solo devuelve un BOOLEAN.
 * @param type $param Array con los parametros en orden para pasarlos a la consulta preparada.
 * @param type $numRows Si TRUE retorna el numero de registros
 */
function Query($connection, $query, $fetch = FALSE, $params = FALSE, $numRows = FALSE) {

    $result = TRUE;
    if (!$fetch) {
        if (!mysqli_query($connection, $query)) {
            error_log(mysqli_error($connection));
            $result = FALSE;
        }
    } else {
        if ($numRows) {
            $result = mysqli_query($connection, $query);
            if (!$result) {
                error_log(mysqli_error($connection));
                $result = FALSE;
            }
            return mysqli_num_rows($result);
        } else {
            if (is_array($params)) {
                //$result = mysqli_prepared_query($connection, $query, "s", $params);
            } else {
                $result = mysqli_query($connection, $query);
                if (!$result) {
                    error_log(mysqli_error($connection));
                    $result = FALSE;
                }
            }
        }
    }
    return $result;
}

function FetchQuery($result) {
    if (!empty($result) && $result != NULL) {
        return mysqli_fetch_array($result);
    } else {
        return FALSE;
    }
}

function CalculaLimitesPaginacion($numRegistros) {
    global $limitInf, $pagina, $tamPag, $numPags, $numeroRegistros;

    $numeroRegistros = $numRegistros;
    $numPags = ceil($numeroRegistros / $tamPag);

    if (!isset($pagina) or $pagina <= 0 or $pagina > $numPags) {
        $pagina = $numPags;
    }

    $limitInf = 0;
    if ($numPags > 1) {
        if ($pagina == $numPags) {
            $limitInf = $numeroRegistros - $tamPag;
        } else {
            $limitInf = ($pagina - 1) * $tamPag;
        }
    }
}

/**
 * 
 * @return UsuarioVO
 */
function getSessionUsuario(){
    $usuarioLogin = unserialize(utils\HTTPUtils::getSessionValue(SESSION_USUARIO));
    return $usuarioLogin;
}

/**
 * 
 * @return CiasVO
 */
function getSessionCia(){
    $ciaLogin = unserialize(utils\HTTPUtils::getSessionValue(SESSION_CIA));
    return $ciaLogin;
}

function variablesCatalogos($array, $idQry, $detail = FALSE, $nameVariableSession = "SessionInitial", $otherFilter = null) {
    global $busca, $pagina, $OrdenDef, $Sort, $RetSelec, $mysqli, $Qry, $BusInt, $op, $Msj, $cLink, $cLinkd, $orden, $Titulo;
    
    $request = utils\HTTPUtils::getRequest();
    if (!empty(filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING))) {
        if ($request->getAttribute("busca") === "ini") {
            $_SESSION[$nameVariableSession] = $array;
            if (is_array($otherFilter)) {
                foreach ($otherFilter as $key => $value) {
                    $_SESSION[$nameVariableSession][$key] = $value;
                }
            }
        } 
    } elseif ($detail) {
        $_SESSION[$nameVariableSession] = $array;
    }

    if ($request->hasAttribute("busca") && $request->getAttribute("busca") !== "ini") {
        $_SESSION[$nameVariableSession][0] = $request->getAttribute("busca");
    }
    if ($request->hasAttribute("pagina")) {
        $_SESSION[$nameVariableSession][1] = $request->getAttribute("pagina");
    }
    if ($request->hasAttribute("orden")) {
        $_SESSION[$nameVariableSession][2] = $request->getAttribute("orden");
    }
    if ($request->hasAttribute("Sort")) {
        $_SESSION[$nameVariableSession][3] = $request->getAttribute("Sort");
    }
    if ($request->hasAttribute("Ret")) {
        $_SESSION[$nameVariableSession][4] = $request->getAttribute("Ret");
    }
    if (is_array($otherFilter)) {
        foreach ($otherFilter as $key => $value) {
            if (!empty(filter_input(INPUT_GET, $key, FILTER_SANITIZE_STRING))) {
                $_SESSION[$nameVariableSession][$key] = $request->getAttribute($key);
            }
        }
    }
    //error_log(print_r($_SESSION[$nameVariableSession], TRUE));

    /* Obtengo los valores de las sesiones. */
    $busca = $_SESSION[$nameVariableSession][0];
    $pagina = $_SESSION[$nameVariableSession][1];
    $OrdenDef = $_SESSION[$nameVariableSession][2];
    $orden = $_SESSION[$nameVariableSession][2];
    $Sort = $_SESSION[$nameVariableSession][3];
    $RetSelec = $_SESSION[$nameVariableSession][4];

    #Tomo los datos principales campos a editar, tablas y filtros;
    $query = "SELECT * FROM qrys WHERE id='$idQry'";
    $Qry = FetchQuery(Query($mysqli, $query, TRUE));
    
    $Titulo = $Qry['nombre'];

    $Palabras = explode(" ", trim($busca));
    if (count($Palabras) > 1) {
        for ($i = 0; $i < count($Palabras); $i++) {
            $P = str_replace("\"", "", $Palabras[$i]);
            if (!isset($BusInt)) {
                $BusInt = " $OrdenDef LIKE  '%$P%' ";
            } else {
                $BusInt = $BusInt . " AND $OrdenDef like '%$P%' ";
            }
        }
    } else {
        $BusInt = $OrdenDef . " LIKE '%" . str_replace("\"", "", $busca) . "%'";
    }

    $op = $request->getAttribute("op");
    $Msj = utf8_encode($request->getAttribute("Msj"));

    $Pos = strrpos(utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF"), ".");
    $cLink = substr(utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF"), 0, $Pos) . 'e.php';
    $cLinkd = substr(utils\HTTPUtils::getEnvironment()->getAttribute("PHP_SELF"), 0, $Pos) . 'd.php';
}

function leer_archivos($ruta) {
    // comprobamos si lo que nos pasan es un direcotrio
    if (is_dir($ruta)) {
        // Abrimos el directorio y comprobamos que hay
        if ($aux = opendir($ruta)) {
            while (($archivo = readdir($aux)) !== false) {
                $jar = substr($archivo, -3, 3);
                if ($jar == 'jar') {
                    break;
                }
            }
            closedir($aux);
        }
        return $archivo;
    }
}

/**
 * Get real user ip
 *
 * Usage sample:
 * GetRealUserIp();
 * GetRealUserIp('ERROR',FILTER_FLAG_NO_RES_RANGE);
 * 
 * @param string $default default return value if no valid ip found
 * @param int    $filter_options filter options. default is FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
 *
 * @return string real user ip
 */
function GetRealUserIp($default = NULL, $filter_options = 12582912) {
    $HTTP_X_FORWARDED_FOR = isset($_SERVER) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : getenv('HTTP_X_FORWARDED_FOR');
    $HTTP_CLIENT_IP = isset($_SERVER) ? $_SERVER["HTTP_CLIENT_IP"] : getenv('HTTP_CLIENT_IP');
    $HTTP_CF_CONNECTING_IP = isset($_SERVER) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : getenv('HTTP_CF_CONNECTING_IP');
    $REMOTE_ADDR = isset($_SERVER) ? $_SERVER["REMOTE_ADDR"] : getenv('REMOTE_ADDR');

    $all_ips = explode(",", "$HTTP_X_FORWARDED_FOR,$HTTP_CLIENT_IP,$HTTP_CF_CONNECTING_IP,$REMOTE_ADDR");
    foreach ($all_ips as $ip) {
        if ($ip = filter_var($ip, FILTER_VALIDATE_IP, $filter_options))
            break;
    }
    return $ip ? $ip : $default;
}
