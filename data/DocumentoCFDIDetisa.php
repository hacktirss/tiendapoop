<?php

/*
 * NotaDeCreditoDetisa
 * detifac®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

namespace com\detisa\detisa;
 
require_once ('cfdi33/Comprobante.php');
require_once ('NotaCreditoDAO.php');
require_once ('mysqlUtils.php');

interface DocumentoCFDIDetisa {

    function getComprobante();
    function setComprobante($comprobante);
    function setComprobanteTimbrado($comprobanteTimbrado);
    function update($id);
    function save($id, $clavePAC);
    function cancel($id);
    function acuse($id, $acuse);
}//NotaDeCreditoDetisa
