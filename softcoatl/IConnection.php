<?php
/**
 * IConnection
 *  Database connection using mysqli library
 * XXXXXXXXXX®
 * © 2019, Softcoatl
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jan 2017
 * 
 * Factory de la conexión a base de datos usando la librería mysqli.
 * Crea una conexión a base de datos Aa partir de lo definido en el objeto Configuration.
 */
namespace com\softcoatl\utils;

class IConnection {

    /**
     * getConnection Gets a new data base connection object
     * @param type $schemaName Schema Name
     * @param type $hostName Host URL
     * @param type $user Database user
     * @param type $password Database password
     * @return \mysqli Data Base connection object
     * @throws Exception
     */
    static function getConnection() {

        $dbc = Configuration::get();

        $dbConn = new \mysqli($dbc->host, $dbc->username, $dbc->pass, $dbc->database);

        if ($dbConn->connect_errno>0) {
            if ($dbConn->connect_errno) {
                throw new \Exception("Error conectando con base de datos <br/>" . utf8_encode($dbConn->error));
            }
        }
        if (!$dbConn->query("SET lc_time_names = 'es_MX'")) {
            if ($dbConn->error) {
                throw new  \Exception("Error configurando base de datos <br/>" . utf8_encode($dbConn->error));
            }
        }
        if (array_key_exists("charset", $dbc) && !$dbConn->set_charset($dbc->charset)) {
            if ($dbConn->error) {
                throw new \Exception("Error configurando base de datos <br/>" . utf8_encode($dbConn->error));
            }
        }
        return $dbConn;
    }//getConnection
}

