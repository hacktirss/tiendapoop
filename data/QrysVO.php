<?php

/**
 * Description of qrysVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class QrysVO {

    private $id;
    private $nombre;
    private $campos;
    private $froms;
    private $edi;
    private $tampag;
    private $ayuda;
    private $joins;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getCampos() {
        return $this->campos;
    }

    function getFroms() {
        return $this->froms;
    }

    function getEdi() {
        return $this->edi;
    }

    function getTampag() {
        return $this->tampag;
    }

    function getAyuda() {
        return $this->ayuda;
    }

    function getJoins() {
        return $this->joins;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setCampos($campos) {
        $this->campos = $campos;
    }

    function setFroms($froms) {
        $this->froms = $froms;
    }

    function setEdi($edi) {
        $this->edi = $edi;
    }

    function setTampag($tampag) {
        $this->tampag = $tampag;
    }

    function setAyuda($ayuda) {
        $this->ayuda = $ayuda;
    }

    function setJoins($joins) {
        $this->joins = $joins;
    }

}
