<?php

/**
 * Description of ReingresoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ReingresoVO {

    private $id;
    private $cia;
    private $fecha_entra;
    private $concepto;
    private $responsable;
    private $proveedor;
    private $cantidad;
    private $importe;
    private $status;
    private $referencia;
    private $ordpago;

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

    function getReferencia() {
        return $this->referencia;
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

    function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    function setOrdpago($ordpago) {
        $this->ordpago = $ordpago;
    }

}
