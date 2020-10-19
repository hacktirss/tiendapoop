<?php

/*
 * FacturaDetisa
 * Detisa®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since ene 2018
 */

namespace com\detisa\detisa;
 
require_once ('cfdi33/Comprobante.php');
require_once ('DocumentoCFDIDetisa.php');
require_once ('ReciboPagoDAO.php');
require_once ('mysqlUtils.php');

class ReciboElectronicoPagoDetisa implements DocumentoCFDIDetisa {

    /* @var $reciboDAO ReciboPagoDAO */
    private $reciboDAO;
    /* @var $comprobante \cfdi33\Comprobante */
    private $comprobante;
    /* @var $comprobanteTimbrado \cfdi33\Comprobante */
    private $comprobanteTimbrado;

    private $representacionImpresa;

    private $xml;

    private $xmlTimbrado;

    function __construct($idFactura, $idCia) {

        $this->reciboDAO = new ReciboPagoDAO($idFactura, $idCia);
        $this->comprobante = $this->reciboDAO->getComprobante();
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
        $this->reciboDAO->updateIng($id, $this->comprobanteTimbrado->getTimbreFiscalDigital()->getUUID());
    }

    function save($id, $clavePAC) {
        $this->reciboDAO->insertFactura($id, $this->comprobanteTimbrado, $this->xmlTimbrado, $clavePAC);
    }
    
    function cancel($id) {
        $this->reciboDAO->cancel($id);
    }

    public function acuse($uuid, $acuse) {
        error_log("Cancelando ". $uuid);
        $this->reciboDAO->guardaAcuse($uuid, $acuse);
    }

}//FacturaDetisa
