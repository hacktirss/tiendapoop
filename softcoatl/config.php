<?php

namespace com\softcoatl\utils;

class Configuration {
    
    private static $dbc = array(
            "driver" => "mysql",
            "charset" => "utf8mb4",
            "host" => "localhost", //repositorio.dyndns.org
            "username" => "poopcorn",
            "pass" => "det15a",
            "database" => "poopcorn_bd"
        );

    public static function get() {
        return (object) Configuration::$dbc;
    }
   
}
