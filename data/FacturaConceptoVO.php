<?php

/*
 * FacturaConceptoVO
 * omicrom®
 * © 2017; Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña; Softcoatl
 * @version 1.0
 * @since jul 2017
 */

class FacturaConceptoVO {

    private $clave;                     // Clave de producto
    private $descripcion;               // Descripción de producto
    private $cantidad;                  // Cantidad
    private $precio;                    // Precio Unitario

    private $iva;                       // Tasa de IVA aplicada
    private $ieps;                      // Tasa de IEPS
    private $isr;                       // Tasa de ISR
    private $ish;                       // Tasa de ISH
    private $umedida;                   // Unidad de Medida

    private $factoriva;                 // Tasa de IVA aplicada (Formato para CFDI 3.3)
    private $factorieps;                // Tasa de IEPS (Formato para CFDI 3.3)
    private $factorisr;                 // Tasa de ISR (Formato para CFDI 3.3)
    private $factorish;                 // Tasa de ISH (Formato para CFDI 3.3)
    private $inv_cproducto;             // Clave de Producto/Servicio CDFI 3.3
    private $inv_cunidad;               // Clave de Unidad CFDI 3.3

    private $descuento;                 // Descuento
    private $subtotal;                  // Total antes de impuestos y descuentos
    private $base;                      // Base Gravable
    private $impiva;                    // Importe de IVA Trasladado
    private $impieps;                   // Importe de IEPS Trasladado
    private $impisr;                    // Importe de ISR Retenido
    private $impish;                    // Importe de ISH ¿Retenido?
    private $retencioniva;              // Importe de IVA Retenido

    private $total;                     // Total

    function getClave() {
        return $this->clave;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getPrecio() {
        return $this->precio;
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

    function getIsh() {
        return $this->ish;
    }

    function getUmedida() {
        return $this->umedida;
    }

    function getFactoriva() {
        return $this->factoriva;
    }

    function getFactorieps() {
        return $this->factorieps;
    }

    function getFactorisr() {
        return $this->factorisr;
    }

    function getFactorish() {
        return $this->factorish;
    }

    function getInv_cproducto() {
        return $this->inv_cproducto;
    }

    function getInv_cunidad() {
        return $this->inv_cunidad;
    }

    function getSubtotal() {
        return $this->subtotal;
    }

    function getBase() {
        return $this->base;
    }

    function getDescuento() {
        return $this->descuento;
    }

    function getImpiva() {
        return $this->impiva;
    }

    function getImpieps() {
        return $this->impieps;
    }

    function getImpisr() {
        return $this->impisr;
    }

    function getImpish() {
        return $this->impish;
    }

    function getRetencioniva() {
        return $this->retencioniva;
    }

    function getTotal() {
        return $this->getSubtotal() 
                + $this->getImpiva() + $this->getImpieps() + $this->getImpish() 
                - ($this->getRetencioniva() + $this->getIsr()) 
                - ($this->getDescuento());
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
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

    function setIsh($ish) {
        $this->ish = $ish;
    }

    function setUmedida($umedida) {
        $this->umedida = $umedida;
    }

    function setFactoriva($factoriva) {
        $this->factoriva = $factoriva;
    }

    function setFactorieps($factorieps) {
        $this->factorieps = $factorieps;
    }

    function setFactorisr($factorisr) {
        $this->factorisr = $factorisr;
    }

    function setFactorish($factorish) {
        $this->factorish = $factorish;
    }

    function setInv_cproducto($inv_cproducto) {
        $this->inv_cproducto = $inv_cproducto;
    }

    function setInv_cunidad($inv_cunidad) {
        $this->inv_cunidad = $inv_cunidad;
    }

    function setSubtotal($subtotal) {
        $this->subtotal = $subtotal;
    }

    function setBase($base) {
        $this->base = $base;
    }

    function setDescuento($descuento) {
        $this->descuento = $descuento;
    }

    function setImpiva($impiva) {
        $this->impiva = $impiva;
    }

    function setImpieps($impieps) {
        $this->impieps = $impieps;
    }

    function setImpisr($impisr) {
        $this->impisr = $impisr;
    }

    function setImpish($impish) {
        $this->impish = $impish;
    }

    function setRetencioniva($retencioniva) {
        $this->retencioniva = $retencioniva;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    public function __toString() {

        return "FacturaConceptoVO={"
                .   "clave=".$this->clave
                . ", descripcion=".$this->descripcion
                . ", cantidad=".$this->cantidad
                . ", precio=".$this->precio
                . ", iva=".$this->iva
                . ", ieps=".$this->ieps
                . ", isr=".$this->isr
                . ", ish=".$this->ish
                . ", umedida=".$this->umedida
                . ", factoriva=".$this->factoriva
                . ", factorieps=".$this->factorieps
                . ", factorisr=".$this->factorisr
                . ", factorish=".$this->factorish
                . ", inv_cproducto=".$this->inv_cproducto
                . ", inv_cunidad=".$this->inv_cunidad
                . ", subtotal=".$this->subtotal
                . ", base=".$this->base
                . ", descuento=".$this->descuento
                . ", impiva=".$this->impiva
                . ", impieps=".$this->impieps
                . ", impisr=".$this->impisr
                . ", impish=".$this->impish
                . ", retencionivar=".$this->retencioniva
                . ", total=".$this->total."}";        
    }//toString
}//FacturaConceptoVO
