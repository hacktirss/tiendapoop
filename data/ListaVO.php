<?php

/**
 * Description of ListaVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ListaVO {

    private $id;
    private $nombre;
    private $descripcion;
    private $default;
    private $tipo_dato;
    private $longitud = 1;
    private $estado = 1;
    private $mayus = 0;
    private $min = 0;
    private $max = 999;
            
    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getDefault() {
        return $this->default;
    }

    function getTipo_dato() {
        return $this->tipo_dato;
    }

    function getLongitud() {
        return $this->longitud;
    }

    function getEstado() {
        return $this->estado;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setDefault($default) {
        $this->default = $default;
    }

    function setTipo_dato($tipo_dato) {
        $this->tipo_dato = $tipo_dato;
    }

    function setLongitud($longitud) {
        $this->longitud = $longitud;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function isMayus() {
        return $this->mayus;
    }

    function getMin() {
        return $this->min;
    }

    function getMax() {
        return $this->max;
    }

    function setMayus($mayus) {
        $this->mayus = $mayus;
    }

    function setMin($min) {
        $this->min = $min;
    }

    function setMax($max) {
        $this->max = $max;
    }


}