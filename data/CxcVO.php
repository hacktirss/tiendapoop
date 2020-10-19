<?php

/**
 * Description of CxcVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class CxcVO {

    private $id;
    private $cia;
    private $cuenta;
    private $fecha;
    private $referencia;
    private $tm = "C";
    private $fechav;
    private $concepto;
    private $importe = 0;
    private $reciboant = 0;
    private $recibo = 0;
    private $factura = 0;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getCuenta() {
        return $this->cuenta;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getReferencia() {
        return $this->referencia;
    }

    function getTm() {
        return $this->tm;
    }

    function getFechav() {
        return $this->fechav;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getImporte() {
        return $this->importe;
    }

    function getReciboant() {
        return $this->reciboant;
    }

    function getRecibo() {
        return $this->recibo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setCuenta($cuenta) {
        $this->cuenta = $cuenta;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    function setTm($tm) {
        $this->tm = $tm;
    }

    function setFechav($fechav) {
        $this->fechav = $fechav;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setReciboant($reciboant) {
        $this->reciboant = $reciboant;
    }

    function setRecibo($recibo) {
        $this->recibo = $recibo;
    }
    
    function getFactura() {
        return $this->factura;
    }

    function setFactura($factura) {
        $this->factura = $factura;
    }

}
