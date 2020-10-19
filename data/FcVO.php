<?php

/*
 * FcVO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

class FcVO {
    private $id;
    private $cia;
    private $folio;
    private $serie = "";
    private $rfc;
    private $fecha;
    private $fechatimbrado;
    private $cliente;
    private $observaciones = "";
    private $cantidad = 0;
    private $importe = 0;
    private $iva = 0;
    private $ieps = 0;
    private $isr  =0;
    private $status = "Abierta";
    private $total = 0;
    private $uuid = "-----";
    private $retencioniva = 0;
    private $pagos = 1;
    private $cndpago = "";
    private $tipo = 1; //1 = Facturacion, 2 = Notas de crédito
    private $hospedaje = 0;
    private $concepto = "";
    private $moneda = "Pesos";
    private $tiporelacion;
    private $relacioncfdi;
    private $formadepago = "01";
    private $metododepago = "PUE";
    private $usocfdi = "G03";

    function getId() {
        return $this->id;
    }

    function getFolio() {
        return $this->folio;
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

    function getObservaciones() {
        return $this->observaciones;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getHospedaje() {
        return $this->hospedaje;
    }
    function getConcepto() {
        return $this->concepto;
    }

    function getMoneda() {
        return $this->moneda;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFolio($folio) {
        $this->folio = $folio;
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

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setHospedaje($hospedaje) {
        $this->hospedaje = $hospedaje;
    }
    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setMoneda($moneda) {
        $this->moneda = $moneda;
    }
    
    function getSerie() {
        return $this->serie;
    }

    function getTiporelacion() {
        return $this->tiporelacion;
    }

    function getRelacioncfdi() {
        return $this->relacioncfdi;
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

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setTiporelacion($tiporelacion) {
        $this->tiporelacion = $tiporelacion;
    }

    function setRelacioncfdi($relacioncfdi) {
        $this->relacioncfdi = $relacioncfdi;
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
    
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }
        
    public function __toString() {
        return "FcVO={id=".$this->id
            . ", folio=".$this->folio
            . ", rfc=".$this->rfc
            . ", fecha=".$this->fecha
            . ", fechatimbrado=".$this->fechatimbrado
            . ", cliente=".$this->cliente
            . ", cantidad=".$this->cantidad
            . ", importe=".$this->importe
            . ", iva=".$this->iva
            . ", ieps=".$this->ieps
            . ", isr=".$this->isr
            . ". status=".$this->status
            . ". total=".$this->total
            . ", uuid=".$this->uuid
            . ", retencioniva=".$this->retencioniva
            . ", pagos=".$this->pagos
            . ", cndpago=".$this->cndpago
            . ", observaciones=".$this->observaciones
            . ", tipo=".$this->tipo
            . ", hospedaje=".$this->hospedaje
            . ", concepto=".$this->concepto
            . ", moneda=".$this->moneda."}";
    }
}
