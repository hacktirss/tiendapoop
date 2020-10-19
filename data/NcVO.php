<?php

/**
 * Description of NcVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class NcVO {

    private $id;
    private $cia;
    private $folio;
    private $serie;
    private $factura;
    private $rfc;
    private $fecha;
    private $fechatimbrado;
    private $cliente;
    private $observaciones = "";
    private $cantidad = 0;
    private $importe = 0;
    private $iva = 0;
    private $ieps = 0;
    private $isr = 0;
    private $status = "Abierta";
    private $total = 0;
    private $uuid = "-----";
    private $retencioniva = 0;
    private $pagos = 1;
    private $cndpago = "";
    private $tipo = 1;
    private $hospedaje = 0;
    private $moneda = "Pesos";
    private $concepto = "";
    private $formadepago = "01";
    private $metododepago = "PUE";
    private $usocfdi = "G02";
    private $tiporelacion = "01";
    private $relacioncfdi;
    private $fcId;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getFolio() {
        return $this->folio;
    }

    function getSerie() {
        return $this->serie;
    }

    function getFactura() {
        return $this->factura;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getFechatimbrado() {
        return $this->fechatimbrado;
    }

    function getCliente() {
        return $this->cliente;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getImporte() {
        return $this->importe;
    }

    function getIva() {
        return $this->iva;
    }

    function getIeps() {
        return $this->ieps;
    }

    function getIsr() {
        return $this->isr;
    }

    function getStatus() {
        return $this->status;
    }

    function getTotal() {
        return $this->total;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getRetencioniva() {
        return $this->retencioniva;
    }

    function getPagos() {
        return $this->pagos;
    }

    function getCndpago() {
        return $this->cndpago;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getHospedaje() {
        return $this->hospedaje;
    }

    function getMoneda() {
        return $this->moneda;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getFormadepago() {
        return $this->formadepago;
    }

    function getMetododepago() {
        return $this->metododepago;
    }

    function getUsocfdi() {
        return $this->usocfdi;
    }

    function getTiporelacion() {
        return $this->tiporelacion;
    }

    function getRelacioncfdi() {
        return $this->relacioncfdi;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setFactura($factura) {
        $this->factura = $factura;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setFechatimbrado($fechatimbrado) {
        $this->fechatimbrado = $fechatimbrado;
    }

    function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setIeps($ieps) {
        $this->ieps = $ieps;
    }

    function setIsr($isr) {
        $this->isr = $isr;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setRetencioniva($retencioniva) {
        $this->retencioniva = $retencioniva;
    }

    function setPagos($pagos) {
        $this->pagos = $pagos;
    }

    function setCndpago($cndpago) {
        $this->cndpago = $cndpago;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setHospedaje($hospedaje) {
        $this->hospedaje = $hospedaje;
    }

    function setMoneda($moneda) {
        $this->moneda = $moneda;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setFormadepago($formadepago) {
        $this->formadepago = $formadepago;
    }

    function setMetododepago($metododepago) {
        $this->metododepago = $metododepago;
    }

    function setUsocfdi($usocfdi) {
        $this->usocfdi = $usocfdi;
    }

    function setTiporelacion($tiporelacion) {
        $this->tiporelacion = $tiporelacion;
    }

    function setRelacioncfdi($relacioncfdi) {
        $this->relacioncfdi = $relacioncfdi;
    }
    
    function getFcId() {
        return $this->fcId;
    }

    function setFcId($fcId) {
        $this->fcId = $fcId;
    }
    
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

}
