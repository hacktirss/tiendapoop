<?php

/**
 * Description of PagoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class PagoVO {

    private $id;
    private $cia;
    private $fecha;
    private $fechap;
    private $cuenta;
    private $concepto;
    private $importe;
    private $rubro = "";
    private $aplicado = 0;
    private $referencia = 0;
    private $status = "Abierta";
    private $banco = 1;
    private $formapago = "01";
    private $numoperacion = "";
    private $uuid = "-----";
    private $statusCFDI = "Abierto";
    private $fechar;
    private $cliente = "";
    private $folio = 0;
                
    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getFechap() {
        return $this->fechap;
    }

    function getCuenta() {
        return $this->cuenta;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getImporte() {
        return $this->importe;
    }

    function getRubro() {
        return $this->rubro;
    }

    function getAplicado() {
        return $this->aplicado;
    }

    function getReferencia() {
        return $this->referencia;
    }

    function getStatus() {
        return $this->status;
    }

    function getBanco() {
        return $this->banco;
    }

    function getFormapago() {
        return $this->formapago;
    }

    function getNumoperacion() {
        return $this->numoperacion;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getStatusCFDI() {
        return $this->statusCFDI;
    }

    function getFechar() {
        return $this->fechar;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setFechap($fechap) {
        $this->fechap = $fechap;
    }

    function setCuenta($cuenta) {
        $this->cuenta = $cuenta;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setRubro($rubro) {
        $this->rubro = $rubro;
    }

    function setAplicado($aplicado) {
        $this->aplicado = $aplicado;
    }

    function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setBanco($banco) {
        $this->banco = $banco;
    }

    function setFormapago($formapago) {
        $this->formapago = $formapago;
    }

    function setNumoperacion($numoperacion) {
        $this->numoperacion = $numoperacion;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setStatusCFDI($statusCFDI) {
        $this->statusCFDI = $statusCFDI;
    }

    function setFechar($fechar) {
        $this->fechar = $fechar;
    }
    
    function getCliente() {
        return $this->cliente;
    }

    function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }
    
    function getFolio() {
        return $this->folio;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }


}
