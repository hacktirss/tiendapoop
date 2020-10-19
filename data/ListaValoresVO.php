<?php

/**
 * Description of ListaValoresVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ListaValoresVO {
    private $id;
    private $llave;
    private $valor;
    private $alarma = 0;
    private $id_lista;
    private $estado = 1;
    
    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getLlave() {
        return $this->llave;
    }

    function getValor() {
        return $this->valor;
    }

    function getAlarma() {
        return $this->alarma;
    }

    function getId_lista() {
        return $this->id_lista;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setLlave($llave) {
        $this->llave = $llave;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setAlarma($alarma) {
        $this->alarma = $alarma;
    }

    function setId_lista($id_lista) {
        $this->id_lista = $id_lista;
    }

    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }
}
