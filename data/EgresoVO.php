<?php

/**
 * Description of EgresoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class EgresoVO {

    private $id;
    private $cia;
    private $creacion;
    private $fecha;
    private $ordendepago = 0;
    private $banco = 1;
    private $formadepago = "01";
    private $entradaid = 0;
    private $observaciones = "";
    private $pagoreal = 0;
    private $otropago = 0;
    private $banco_nombre = "";
    private $proveedor_nombre = "";
    private $orden_fecha = "";
    private $orden_concepto = "";
    private $orden_importe = 0;
    private $orden_proveedor = 0;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCreacion() {
        return $this->creacion;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getOrdendepago() {
        return $this->ordendepago;
    }

    function getBanco() {
        return $this->banco;
    }

    function getFormadepago() {
        return $this->formadepago;
    }

    function getEntradaid() {
        return $this->entradaid;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getPagoreal() {
        return $this->pagoreal;
    }

    function getOtropago() {
        return $this->otropago;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCreacion($creacion) {
        $this->creacion = $creacion;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setOrdendepago($ordendepago) {
        $this->ordendepago = $ordendepago;
    }

    function setBanco($banco) {
        $this->banco = $banco;
    }

    function setFormadepago($formadepago) {
        $this->formadepago = $formadepago;
    }

    function setEntradaid($entradaid) {
        $this->entradaid = $entradaid;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setPagoreal($pagoreal) {
        $this->pagoreal = $pagoreal;
    }

    function setOtropago($otropago) {
        $this->otropago = $otropago;
    }

    function getBanco_nombre() {
        return $this->banco_nombre;
    }

    function getProveedor_nombre() {
        return $this->proveedor_nombre;
    }

    function getOrden_fecha() {
        return $this->orden_fecha;
    }

    function getOrden_concepto() {
        return $this->orden_concepto;
    }

    function getOrden_importe() {
        return $this->orden_importe;
    }

    function setBanco_nombre($banco_nombre) {
        $this->banco_nombre = $banco_nombre;
    }

    function setProveedor_nombre($proveedor_nombre) {
        $this->proveedor_nombre = $proveedor_nombre;
    }

    function setOrden_fecha($orden_fecha) {
        $this->orden_fecha = $orden_fecha;
    }

    function setOrden_concepto($orden_concepto) {
        $this->orden_concepto = $orden_concepto;
    }

    function setOrden_importe($orden_importe) {
        $this->orden_importe = $orden_importe;
    }
    
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }
    
    function getOrden_proveedor() {
        return $this->orden_proveedor;
    }

    function setOrden_proveedor($orden_proveedor) {
        $this->orden_proveedor = $orden_proveedor;
    }

}
