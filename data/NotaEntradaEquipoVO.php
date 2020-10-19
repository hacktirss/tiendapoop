<?php

/**
 * Description of NotaEntradaEquipoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class NotaEntradaEquipoVO {

    private $id;
    private $cia;
    private $fecha;
    private $concepto;
    private $fechafac;
    private $factura = "";
    private $responsable = "";
    private $proveedor = 0;
    private $importe = 0;
    private $status = "Abierta";
    private $egreso = 0;
    private $costo_entrada = 0;
    private $cantidad = 0;
    private $detalle = 0;

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

    function getImporte() {
        return $this->importe;
    }

    function getStatus() {
        return $this->status;
    }

    function getEgreso() {
        return $this->egreso;
    }

    function getCosto_entrada() {
        return $this->costo_entrada;
    }

    function getCantidad() {
        return $this->cantidad;
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

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setEgreso($egreso) {
        $this->egreso = $egreso;
    }

    function setCosto_entrada($costo_entrada) {
        $this->costo_entrada = $costo_entrada;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }
    
    function getDetalle() {
        return $this->detalle;
    }

    function setDetalle($detalle) {
        $this->detalle = $detalle;
    }

}
