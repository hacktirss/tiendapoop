<?php

/**
 * BaseDAO
 * FactTequitl®
 * © 2020, Softcoatl
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2020
 */

namespace com\softcoatl\utils;

use com\softcoatl\utils\IConnection;

class BaseDAO {

    /** @var \mysqli Objeto de Conexión a BD */
    protected $conn;

    public function __construct() {
        $this->conn = IConnection::getConnection();
    }

    public function __destruct() {
        $this->conn->close();
    }
}
