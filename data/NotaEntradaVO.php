<?php

/**
 * Description of NotaEntradaVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class NotaEntradaVO {

    private $id;
    private $cia;
    private $fecha_entra;
    private $concepto = "";
    private $fechafac;
    private $factura = "";
    private $responsable = "";
    private $proveedor = 1;
    private $cantidad = 0;
    private $importe = 0;
    private $status = "Abierta";
    private $egreso = 0;
    private $ordpago = 0;
    private $detalle = 0;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getFecha_entra() {
        return $this->fecha_entra;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getFechafac() {
        return $this->fechafac;
    }

    function getFactura() {
        return $this->factura;
    }

    function getResponsable() {
        return $this->responsable;
    }

    function getProveedor() {
        return $this->proveedor;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getImporte() {
        return $this->importe;
    }

    function getStatus() {
        return $this->status;
    }

    function getEgreso() {
        return $this->egreso;
    }

    function getOrdpago() {
        return $this->ordpago;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setFecha_entra($fecha_entra) {
        $this->fecha_entra = $fecha_entra;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setFechafac($fechafac) {
        $this->fechafac = $fechafac;
    }

    function setFactura($factura) {
        $this->factura = $factura;
    }

    function setResponsable($responsable) {
        $this->responsable = $responsable;
    }

    function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setEgreso($egreso) {
        $this->egreso = $egreso;
    }

    function setOrdpago($ordpago) {
        $this->ordpago = $ordpago;
    }
    
    function getDetalle() {
        return $this->detalle;
    }

    function setDetalle($detalle) {
        $this->detalle = $detalle;
    }

}
