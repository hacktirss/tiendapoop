<?php

/**
 * Description of ActividadDVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ActividadDVO {

    private $actividad;
    private $idnvo;
    private $fecha;
    private $concepto;
    private $observaciones;

    function __construct() {
        
    }

    function getActividad() {
        return $this->actividad;
    }

    function getIdnvo() {
        return $this->idnvo;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function setActividad($actividad) {
        $this->actividad = $actividad;
    }

    function setIdnvo($idnvo) {
        $this->idnvo = $idnvo;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

}
