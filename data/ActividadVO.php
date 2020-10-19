<?php

/**
 * Description of ActividadVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ActividadVO {

    private $id;
    private $cia;
    private $fecha;
    private $descripcion;
    private $tipo = "Actividad";
    private $periodo = 2;
    private $lapso = 1;
    private $observaciones;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getLapso() {
        return $this->lapso;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setLapso($lapso) {
        $this->lapso = $lapso;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }
    
    function getPeriodo() {
        return $this->periodo;
    }

    function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }
        
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }


}
