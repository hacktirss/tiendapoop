<?php

/**
 * Description of NotaSalidaVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class NotaSalidaVO {

    private $id;
    private $cia;
    private $fecha;
    private $concepto;
    private $factura = "";
    private $responsable = 0;
    private $status = "Abierta";
    private $observaciones = "";
    private $cliente = 0;
    private $detalle;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getFactura() {
        return $this->factura;
    }

    function getResponsable() {
        return $this->responsable;
    }

    function getStatus() {
        return $this->status;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getCliente() {
        return $this->cliente;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setFactura($factura) {
        $this->factura = $factura;
    }

    function setResponsable($responsable) {
        $this->responsable = $responsable;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    function getDetalle() {
        return $this->detalle;
    }

    function setDetalle($detalle) {
        $this->detalle = $detalle;
    }

}
