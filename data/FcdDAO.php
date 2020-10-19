<?php

/*
 * FcdDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('mysqlUtils.php');
include_once ('FcdVO.php');

class FcdDAO {
    private $conn;
    
    public function __construct() {
        $this->conn=getConnection();
    }

    
    public function __destruct() {
        $this->conn->close();
    }
}
