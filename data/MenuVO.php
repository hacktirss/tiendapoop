<?php

class MenuVO {

    private $id;
    private $nombre;
    private $orden;
    private $tipo;
    private $descripcion;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getOrden() {
        return $this->orden;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setOrden($orden) {
        $this->orden = $orden;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

}
