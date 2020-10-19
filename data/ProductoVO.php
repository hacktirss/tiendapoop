<?php

/**
 * Description of ProductoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ProductoVO {

    private $id;
    private $cia;
    private $rfc;
    private $descripcion;
    private $umedida = "Pzas";
    private $precio = 0;
    private $costo = 0;
    private $iva = 1;
    private $isr = 0;
    private $retencioniva = 0;
    private $ieps = 0;
    private $costopromedio = 0;
    private $observaciones;
    private $existencia = 0;
    private $dlls = 0;
    private $grupo;
    private $activo = "Si";
    private $inv_cunidad = "ZZ";
    private $inv_cproducto = "01010101";
    private $tipo_servicio = "";

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getUmedida() {
        return $this->umedida;
    }

    function getPrecio() {
        return $this->precio;
    }

    function getCosto() {
        return $this->costo;
    }

    function getIva() {
        return $this->iva;
    }

    function getCostopromedio() {
        return $this->costopromedio;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getExistencia() {
        return $this->existencia;
    }

    function getDlls() {
        return $this->dlls;
    }

    function getGrupo() {
        return $this->grupo;
    }

    function getActivo() {
        return $this->activo;
    }

    function getInv_cunidad() {
        return $this->inv_cunidad;
    }

    function getInv_cproducto() {
        return $this->inv_cproducto;
    }

    function getTipo_servicio() {
        return $this->tipo_servicio;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setUmedida($umedida) {
        $this->umedida = $umedida;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function setCosto($costo) {
        $this->costo = $costo;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setCostopromedio($costopromedio) {
        $this->costopromedio = $costopromedio;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setExistencia($existencia) {
        $this->existencia = $existencia;
    }

    function setDlls($dlls) {
        $this->dlls = $dlls;
    }

    function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    function setActivo($activo) {
        $this->activo = $activo;
    }

    function setInv_cunidad($inv_cunidad) {
        $this->inv_cunidad = $inv_cunidad;
    }

    function setInv_cproducto($inv_cproducto) {
        $this->inv_cproducto = $inv_cproducto;
    }

    function setTipo_servicio($tipo_servicio) {
        $this->tipo_servicio = $tipo_servicio;
    }
    
    function getIsr() {
        return $this->isr;
    }

    function getRetencioniva() {
        return $this->retencioniva;
    }

    function getIeps() {
        return $this->ieps;
    }

    function setIsr($isr) {
        $this->isr = $isr;
    }

    function setRetencioniva($retencioniva) {
        $this->retencioniva = $retencioniva;
    }

    function setIeps($ieps) {
        $this->ieps = $ieps;
    }

}
