<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//namespace com\detisa\omicrom;

/**
 * Description of BitacoraVO
 *
 * @author lino
 */
class BitacoraVO {
    
    private $idBitacora;
    private $cia;
    private $fechaEvento;
    private $horaEvento;
    private $usuario;
    private $tipoEvento;
    private $descripcionEvento;
    private $queryStr;
    private $numeroAlarma;
    
    function __construct() {
        
    }

    
    function getIdBitacora() {
        return $this->idBitacora;
    }

    function getFechaEvento() {
        return $this->fechaEvento;
    }

    function getHoraEvento() {
        return $this->horaEvento;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getTipoEvento() {
        return $this->tipoEvento;
    }

    function getDescripcionEvento() {
        return $this->descripcionEvento;
    }

    function setIdBitacora($idBitacora) {
        $this->idBitacora = $idBitacora;
    }

    function setFechaEvento($fechaEvento) {
        $this->fechaEvento = $fechaEvento;
    }

    function setHoraEvento($horaEvento) {
        $this->horaEvento = $horaEvento;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setTipoEvento($tipoEvento) {
        $this->tipoEvento = $tipoEvento;
    }

    function setDescripcionEvento($descripcionEvento) {
        $this->descripcionEvento = $descripcionEvento;
    }
    
    function getQueryStr() {
        return $this->queryStr;
    }

    function getNumeroAlarma() {
        return $this->numeroAlarma;
    }

    function setQueryStr($queryStr) {
        $this->queryStr = $queryStr;
    }

    function setNumeroAlarma($numeroAlarma) {
        $this->numeroAlarma = $numeroAlarma;
    }

    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }
}
