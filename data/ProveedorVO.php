<?php

/**
 * Description of ProveedorVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ProveedorVO {

    private $id;
    private $cia;
    private $nombre;
    private $direccion;
    private $colonia;
    private $municipio;
    private $alias;
    private $telefono;
    private $activo = "Si";
    private $contacto;
    private $observaciones;
    private $tipodepago = "Contado";
    private $limite = 0;
    private $codigo;
    private $rfc = "XAXX010101000";
    private $correo;
    private $numeroint;
    private $numeroext;
    private $enviarcorreo = "No";
    private $cuentaban = 0;
    private $proveedorde = "Productos";
    private $estado = "";

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

    function getDireccion() {
        return $this->direccion;
    }

    function getColonia() {
        return $this->colonia;
    }

    function getMunicipio() {
        return $this->municipio;
    }

    function getAlias() {
        return $this->alias;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getActivo() {
        return $this->activo;
    }

    function getContacto() {
        return $this->contacto;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getTipodepago() {
        return $this->tipodepago;
    }

    function getLimite() {
        return $this->limite;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getNumeroint() {
        return $this->numeroint;
    }

    function getNumeroext() {
        return $this->numeroext;
    }

    function getEnviarcorreo() {
        return $this->enviarcorreo;
    }

    function getCuentaban() {
        return $this->cuentaban;
    }

    function getProveedorde() {
        return $this->proveedorde;
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

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setColonia($colonia) {
        $this->colonia = $colonia;
    }

    function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    function setAlias($alias) {
        $this->alias = $alias;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setActivo($activo) {
        $this->activo = $activo;
    }

    function setContacto($contacto) {
        $this->contacto = $contacto;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setTipodepago($tipodepago) {
        $this->tipodepago = $tipodepago;
    }

    function setLimite($limite) {
        $this->limite = $limite;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setNumeroint($numeroint) {
        $this->numeroint = $numeroint;
    }

    function setNumeroext($numeroext) {
        $this->numeroext = $numeroext;
    }

    function setEnviarcorreo($enviarcorreo) {
        $this->enviarcorreo = $enviarcorreo;
    }

    function setCuentaban($cuentaban) {
        $this->cuentaban = $cuentaban;
    }

    function setProveedorde($proveedorde) {
        $this->proveedorde = $proveedorde;
    }

    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

}
