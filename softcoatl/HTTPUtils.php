<?php
/**
 * HTTPUtils
 * PHPUtils®
 * © 2019, Softcoatl
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.5
 * @since may 2019
 * 
 * 
 * Funciones genéricas para proyectos WEB. 
 * Facilita el establecimiento y la recuperación de variables de sesión, cookies,
 * requests de tipo GET y POST, así como variables de contexto.
 */

namespace com\softcoatl\utils;

class HTTPUtils {

    /*********************************************************** TODO create session object ***********************************************************/
    public static function startSession($sessionName = NULL) {
        error_log("Getting session " . $sessionName);
        session_name($sessionName);
        return session_start();
    }

    public static function isSessionValid($sessionName = NULL) {
        self::startSession($sessionName);
        return session_id() !== "" && isset($_SESSION);
    }

    public static function sessionInvalidate($sessionName = NULL) {
        self::startSession($sessionName);
        if (session_status() == PHP_SESSION_NONE) {
            utils\HTTPUtils::startSession("omicrom");
        }
        $_SESSION = array();
        $sessionName = session_name();
        if (self::cookieSetted($sessionName)) {
            $params = session_get_cookie_params();
            setcookie($sessionName, '', 1, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
        }
        session_destroy();
        error_log("Session " . $sessionName . " destroyed");
    }

    public static function sessionCreate($sessionName = NULL) {

        if (session_status() == PHP_SESSION_NONE) {
            error_log("Creating session " . $sessionName);
            self::startSession($sessionName);
        }
        if (!self::sessionSetted("CREATED")) {
            session_regenerate_id();
            $_SESSION["CREATED"] = time();
        }
    }

    public static function sessionSetted($key) {
        return isset($_SESSION[$key]);
    }

    public static function hasSessionValue($key) {
        return array_key_exists($key, $_SESSION);
    }

    public static function hasSessionArrayValue($name, $key) {
        return array_key_exists($name, $_SESSION)
                && array_key_exists($key, $_SESSION[$name]);
    }

    public static function getSessionArrayValue($name, $key) {
        return self::hasSessionArrayValue($name, $key) ? $_SESSION[$name][$key] : "";
    }

    public static function getSessionValue($key) {
        return self::hasSessionValue($key) ? $_SESSION[$key] : "";
    }

    public static function getSessionObject($key) {
        return self::hasSessionValue($key) ? unserialize($_SESSION[$key]) : null;
    }

    public static function setSessionArrayValue($name, $key, $value) {
        $_SESSION[$name][$key] = $value;
    }

    public static function setSessionValue($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function setSessionObject($key, $object) {
        $_SESSION[$key] = serialize($object);
    }

    public static function sessionStatus() {
        $status = session_status();
        switch ($status) {
        case PHP_SESSION_DISABLED: return "Sesiones inactivas";
        case PHP_SESSION_NONE: return "No existe sesión activa";
        case PHP_SESSION_ACTIVE: return "Sesión activa";
        default: return "Información no disponible";
        }
    }    
    /*********************************************************** TODO create session object ***********************************************************/

    public static function cookieSetted($key) {
        return self::getCookies()->hasAttribute($key);
    }

    public static function getCookieValue($key) {
        return self::getCookies()->getAttribute($key);
    }

    public static function getCookieObject($key) {
        $serialized = self::getCookies()->getAttribute($key);
        return unserialize($serialized);
    }

    public static function setCookieValue($key, $value) {
        setcookie($key, $value);
    }

    public static function setCookieObject($key, $object) {
        setcookie($key, serialize($object));
    }

    /**
     * 
     * @return QueryParameters
     */
    public static function getCookies() {
        return self::getMethod(INPUT_COOKIE);
    }

    /**
     * 
     * @return QueryParameters
     */
    public static function getEnvironment() {
        return self::getMethod(INPUT_SERVER);
    }

    /**
     * getRequest Returns request values
     * @return QueryParameters
     */
    public static function getRequest() {
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        return self::getMethod($method==="GET" ?  INPUT_GET : INPUT_POST);
    }

    public static function hasFile($file) {
        return array_key_exists($file, $_FILES)
                && $_FILES[$file]['size']>0;
    }

    /**
     * getRequest Returns request values
     * @return QueryParameters
     */
    public static function getFiles() {
        return $_FILES;
    }

    public static function getFile($key) {
        return $_FILES[$key];
    }

    public static function getMethod($method = INPUT_GET) {
        $array = filter_input_array($method);
        return new QueryParameters($array); 
    }

    public static function self() {
        return self::getEnvironment()->getAttribute("PHP_SELF");
    }

    public static function getContextPath() {
        return self::getEnvironment()->getAttribute("DOCUMENT_ROOT");
    }

    /**
     * @deprecated 
     * @see setSessionArrayValue
     */
    public static function setSessionBiValue($nameSession, $key, $value) {
        $_SESSION[$nameSession][$key] = $value;
    }    

    /**
     * @deprecated
     * @see getSessionArrayValue
     */
    public static function getSessionBiValue($nameSession, $key) {
        return $_SESSION[$nameSession][$key];
    }
    
}
