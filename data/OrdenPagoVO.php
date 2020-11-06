<?php

/**
 * Description of OrdenPagoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class OrdenPagoVO {

    private $id;
    private $cia;
    private $fecha;
    private $proveedor;
    private $rubro = "Otros";
    private $alias = "";
    private $concepto = "";
    private $solicito = "";
    private $cotizacion = 0;
    private $importe = 0;
    private $iva = 0;
    private $iva_ret = 0;
    private $isr = 0;
    private $hospedaje = 0;
    private $total;
    private $observaciones = "";
    private $pagonumero = 0;
    private $status = "Cerrada";

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getProveedor() {
        return $this->proveedor;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getSolicito() {
        return $this->solicito;
    }

    function getCotizacion() {
        return $this->cotizacion;
    }

    function getImporte() {
        return $this->importe;
    }

    function getIva() {
        return $this->iva;
    }

    function getTotal() {
        return $this->total;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getPagonumero() {
        return $this->pagonumero;
    }

    function getStatus() {
        return $this->status;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setSolicito($solicito) {
        $this->solicito = $solicito;
    }

    function setCotizacion($cotizacion) {
        $this->cotizacion = $cotizacion;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setPagonumero($pagonumero) {
        $this->pagonumero = $pagonumero;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getAlias() {
        return $this->alias;
    }

    function setAlias($alias) {
        $this->alias = $alias;
    }

    function getRubro() {
        return $this->rubro;
    }

    function getIva_ret() {
        return $this->iva_ret;
    }

    function getIsr() {
        return $this->isr;
    }

    function getHospedaje() {
        return $this->hospedaje;
    }

    function setRubro($rubro) {
        $this->rubro = $rubro;
    }

    function setIva_ret($iva_ret) {
        $this->iva_ret = $iva_ret;
    }

    function setIsr($isr) {
        $this->isr = $isr;
    }

    function setHospedaje($hospedaje) {
        $this->hospedaje = $hospedaje;
    }

    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

}
