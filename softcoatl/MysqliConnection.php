<?php

namespace com\softcoatl\utils;

include_once 'MysqliDb.php';
include_once 'config.php';

/**
 * Description of MysqliConnection
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class MysqliBdConnection {

    const RESPONSE_VALID_CREATE = "Registro creado con EXITO!";
    const RESPONSE_VALID_UDAPTE = "Registro actualizado con EXITO!";
    const RESPONSE_VALID_DELETE = "Registro borrado con EXITO!";
    const RESPONSE_VALID_CHANGE_PWD = "Se cambio la contraseña con EXITO!";
    const RESPONSE_VALID_ERROR = "Ocurrio un error en el proceso, notificar a soporte!";
    /**
     * getConnection Gets a new data base connection object
     * @param type $schemaName Schema Name
     * @param type $hostName Host URL
     * @param type $user Database user
     * @param type $password Database password
     * @return \mysqli Data base object
     * @throws Exception
     */
    static function getConnection() {

        $dbc = Configuration::get();

        $dbConn = new \MysqliDb(Array(
            'host' => $dbc->host,
            'username' => $dbc->username,
            'password' => $dbc->pass,
            'db' => $dbc->database,
            'port' => $dbc->port,
            'prefix' => 'my_',
            'charset' => $dbc->charset));

        $dbConn->autoReconnect = false;

        return $dbConn;
    }

//getConnection
}
