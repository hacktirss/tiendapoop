<?php

/**
 * Description of EquipoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class EquipoVO {

    private $id;
    private $cia;
    private $marca;
    private $descripcion;
    private $grupo;
    private $numero_serie = "########000";
    private $modelo;
    private $costo = 0;
    private $precio = 0;
    private $numero_entrada = 0;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getMarca() {
        return $this->marca;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getGrupo() {
        return $this->grupo;
    }

    function getNumero_serie() {
        return $this->numero_serie;
    }

    function getModelo() {
        return $this->modelo;
    }

    function getCosto() {
        return $this->costo;
    }

    function getPrecio() {
        return $this->precio;
    }

    function getNumero_entrada() {
        return $this->numero_entrada;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setMarca($marca) {
        $this->marca = $marca;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    function setNumero_serie($numero_serie) {
        $this->numero_serie = $numero_serie;
    }

    function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    function setCosto($costo) {
        $this->costo = $costo;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function setNumero_entrada($numero_entrada) {
        $this->numero_entrada = $numero_entrada;
    }

}
