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
require_once ('DocumentoCFDIDetisa.php');
require_once ('mysqlUtils.php');

class NotaDeCreditoDetisa implements DocumentoCFDIDetisa {

    /* @var $notaCreditoDAO NotaCreditoDAO */
    private $notaCreditoDAO;
    /* @var $comprobante \cfdi33\Comprobante */
    private $comprobante;
    /* @var $comprobanteTimbrado \cfdi33\Comprobante */
    private $comprobanteTimbrado;

    private $representacionImpresa;

    private $xml;

    private $xmlTimbrado;

    function __construct($idNotaDeCredito, $idCia = 0) {

        $this->notaCreditoDAO = new NotaCreditoDAO($idNotaDeCredito,$idCia);
        $this->comprobante = $this->notaCreditoDAO->getComprobante();
    }//constructor

    /**
     * 
     * @return \cfdi33\Comprobante
     */
    function getComprobante() {
        return $this->comprobante;
    }

    function setComprobante($comprobante) {
        $this->comprobante = $comprobante;
    }

    function setComprobanteTimbrado($comprobanteTimbrado) {
        $this->comprobanteTimbrado = $comprobanteTimbrado;
    }

    function setRepresentacionImpresa($representacionImpresa) {
        $this->representacionImpresa = $representacionImpresa;
    }

    function setXml($xml) {
        $this->xml = $xml;
    }

    function setXmlTimbrado($xmlTimbrado) {
        $this->xmlTimbrado = $xmlTimbrado;
    }

    function update($id) {
        $this->notaCreditoDAO->updateNC($id, $this->comprobanteTimbrado->getTimbreFiscalDigital()->getUUID());
        $this->notaCreditoDAO->updateCfdiRelacionado($id, $this->comprobanteTimbrado->getTimbreFiscalDigital()->getUUID());
    }

    function save($id, $clavePAC) {
        $this->notaCreditoDAO->insertFactura($id, $this->comprobanteTimbrado, $this->xmlTimbrado, $clavePAC);
    }
    
    function cancel($id) {
        $this->notaCreditoDAO->cancel($id);
    }

    public function acuse($uuid, $acuse) {
        $this->notaCreditoDAO->guardaAcuse($uuid, $acuse);
    }

}//NotaDeCreditoDetisa
