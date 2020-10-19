<?php

/*
 * PacDAO
 * GlobalFAE®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since feb 2018
 */

require_once('softcoatl/config.php');
require_once('softcoatl/SoftcoatlHTTP.php');
require_once('SmtpVO.php');

use com\softcoatl\utils as utils;

class SmtpDAO {
    
    static function active() {

        $sql = "SELECT * FROM smtp WHERE smtpvalido = 1";
        $mysql = utils\IConnection::getConnection();
        if (($query = $mysql->query($sql))) {
            if (($rs = $query->fetch_assoc())) {
                return SmtpVO::parse($rs);
            }
        }
        return FALSE;
    }
}
