<?php

/**
 * Description of BancoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class BancoVO {

    private $id;
    private $cia;
    private $nombre;
    private $cuenta;
    private $saldo;
    private $rfc;
    private $razon_social;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getCuenta() {
        return $this->cuenta;
    }

    function getSaldo() {
        return $this->saldo;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getRazon_social() {
        return $this->razon_social;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setCuenta($cuenta) {
        $this->cuenta = $cuenta;
    }

    function setSaldo($saldo) {
        $this->saldo = $saldo;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setRazon_social($razon_social) {
        $this->razon_social = $razon_social;
    }

}
