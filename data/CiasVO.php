<?php

/**
 * Description of CiasVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class CiasVO {

    private $id;
    private $nombre;
    private $rfc;
    private $password;
    private $alias = "";
    private $direccion = "";
    private $numeroext = "";
    private $numeroint = "";
    private $colonia = "";
    private $municipio = "";
    private $estado = "";
    private $codigo = "";
    private $telefono = "";
    private $contacto = "";
    private $correo = "";
    private $observaciones = "";
    private $facturacion = 1;
    private $regimen = "601";
    private $serie = "";
    private $clavesat = "";
    private $iva = 0;
    private $isr = 0;
    private $retencioninva = 0;
    private $ieps = 0;
    private $master = "1234";

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getPassword() {
        return $this->password;
    }

    function getAlias() {
        return $this->alias;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getNumeroext() {
        return $this->numeroext;
    }

    function getNumeroint() {
        return $this->numeroint;
    }

    function getColonia() {
        return $this->colonia;
    }

    function getMunicipio() {
        return $this->municipio;
    }

    function getEstado() {
        return $this->estado;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getContacto() {
        return $this->contacto;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getFacturacion() {
        return $this->facturacion;
    }

    function getRegimen() {
        return $this->regimen;
    }

    function getSerie() {
        return $this->serie;
    }

    function getClavesat() {
        return $this->clavesat;
    }

    function getIva() {
        return $this->iva;
    }

    function getIsr() {
        return $this->isr;
    }

    function getRetencioninva() {
        return $this->retencioninva;
    }

    function getIeps() {
        return $this->ieps;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setAlias($alias) {
        $this->alias = $alias;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setNumeroext($numeroext) {
        $this->numeroext = $numeroext;
    }

    function setNumeroint($numeroint) {
        $this->numeroint = $numeroint;
    }

    function setColonia($colonia) {
        $this->colonia = $colonia;
    }

    function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setContacto($contacto) {
        $this->contacto = $contacto;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setFacturacion($facturacion) {
        $this->facturacion = $facturacion;
    }

    function setRegimen($regimen) {
        $this->regimen = $regimen;
    }

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setClavesat($clavesat) {
        $this->clavesat = $clavesat;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setIsr($isr) {
        $this->isr = $isr;
    }

    function setRetencioninva($retencioninva) {
        $this->retencioninva = $retencioninva;
    }

    function setIeps($ieps) {
        $this->ieps = $ieps;
    }

    function getMaster() {
        return $this->master;
    }

    function setMaster($master) {
        $this->master = $master;
    }

}
