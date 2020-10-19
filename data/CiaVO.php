<?php

/*
 * CiaVO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

class CiaVO {

    private $id;
    private $nombre;
    private $alias;
    private $direccion;
    private $numeroext;
    private $numeroint;
    private $colonia;
    private $municipio;
    private $ciudad;
    private $estado;
    private $telefono;
    private $iva;
    private $isr;
    private $rfc;
    private $codigo;
    private $serie;
    private $facturacion;
    private $clavesat;
    private $foliofac;
    private $contacto;
    private $observaciones;
    private $tipodepago;
    private $correo;
    private $cuentaban;
    private $folioscom;
    private $folioscon;
    private $regimen;
    private $ventade;
    private $password;
    private $retencioniva;
    private $folioarrend;
    private $foliohonor;
    private $ieps;
    private $gasolinera;
    private $publicidad;
    private $dolar;
    private $master;
    private $entrada;
    private $clave_regimen;
    private $version_cfdi;
    
    /**
     * Overrides toString method
     * @return String
     */
    public function __toString() {
        return "CiaVO={id=".$this->id
                    . ", nombre=".$this->nombre
                    . ", alias=".$this->alias
                    . ", direccion=".$this->direccion
                    . ", numeroext=".$this->numeroext
                    . ", numeroint=".$this->numeroint
                    . ", colonia=".$this->colonia
                    . ", municipio=".$this->municipio
                    . ", ciudad=".$this->ciudad
                    . ", estado=".$this->estado
                    . ", telefono=".$this->telefono
                    . ", iva=".$this->iva
                    . ", isr=".$this->isr
                    . ", rfc=".$this->rfc
                    . ", codigo=".$this->codigo
                    . ", serie=".$this->serie
                    . ", facturacion=".$this->facturacion
                    . ", clavesat=".$this->clavesat
                    . ", foliofac=".$this->foliofac
                    . ", contacto=".$this->contacto
                    . ", observaciones=".$this->observaciones
                    . ", tipodepago=".$this->tipodepago
                    . ", folioenvios=".$this->folenvios
                    . ", correo=".$this->correo
                    . ", cuentaban=".$this->cuentaban
                    . ", folioscom=".$this->folioscom
                    . ", folioscon=".$this->folioscon
                    . ", regimen=".$this->regimen
                    . ", ventade=".$this->ventade
                    . ", password=".$this->password
                    . ", rretencioniva=".$this->retencioniva
                    . ", folioarrend=".$this->folioarrend
                    . ", foliohonor=".$this->foliohonor
                    . ", ieps=".$this->ieps
                    . ", gasolinera=".$this->gasolinera
                    . ", publicidad=".$this->publicidad
                    . ", dolar=".$this->dolar
                    . ", master=".$this->master
                    . ", entrada=".$this->entrada
                    . ", clave_regimen=".$this->clave_regimen
                    . ", version_cfdi=".$this->version_cfdi."}";
    }//toString
    
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
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

    function getCiudad() {
        return $this->ciudad;
    }

    function getEstado() {
        return $this->estado;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getIva() {
        return $this->iva;
    }

    function getIsr() {
        return $this->isr;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getSerie() {
        return $this->serie;
    }

    function getFacturacion() {
        return $this->facturacion;
    }

    function getClavesat() {
        return $this->clavesat;
    }

    function getFoliofac() {
        return $this->foliofac;
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

    function getCorreo() {
        return $this->correo;
    }

    function getCuentaban() {
        return $this->cuentaban;
    }

    function getFolioscom() {
        return $this->folioscom;
    }

    function getFolioscon() {
        return $this->folioscon;
    }

    function getRegimen() {
        return $this->regimen;
    }

    function getVentade() {
        return $this->ventade;
    }

    function getPassword() {
        return $this->password;
    }

    function getRetencioniva() {
        return $this->retencioniva;
    }

    function getFolioarrend() {
        return $this->folioarrend;
    }

    function getFoliohonor() {
        return $this->foliohonor;
    }

    function getIeps() {
        return $this->ieps;
    }

    function getGasolinera() {
        return $this->gasolinera;
    }

    function getPublicidad() {
        return $this->publicidad;
    }

    function getDolar() {
        return $this->dolar;
    }

    function getMaster() {
        return $this->master;
    }

    function getEntrada() {
        return $this->entrada;
    }

    function getClave_regimen() {
        return $this->clave_regimen;
    }

    function getVersion_cfdi() {
        return $this->version_cfdi;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
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

    function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setIva($iva) {
        $this->iva = $iva;
    }

    function setIsr($isr) {
        $this->isr = $isr;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setSerie($serie) {
        $this->serie = $serie;
    }

    function setFacturacion($facturacion) {
        $this->facturacion = $facturacion;
    }

    function setClavesat($clavesat) {
        $this->clavesat = $clavesat;
    }

    function setFoliofac($foliofac) {
        $this->foliofac = $foliofac;
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

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setCuentaban($cuentaban) {
        $this->cuentaban = $cuentaban;
    }

    function setFolioscom($folioscom) {
        $this->folioscom = $folioscom;
    }

    function setFolioscon($folioscon) {
        $this->folioscon = $folioscon;
    }

    function setRegimen($regimen) {
        $this->regimen = $regimen;
    }

    function setVentade($ventade) {
        $this->ventade = $ventade;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setRetencioniva($retencioniva) {
        $this->retencioniva = $retencioniva;
    }

    function setFolioarrend($folioarrend) {
        $this->folioarrend = $folioarrend;
    }

    function setFoliohonor($foliohonor) {
        $this->foliohonor = $foliohonor;
    }

    function setIeps($ieps) {
        $this->ieps = $ieps;
    }

    function setGasolinera($gasolinera) {
        $this->gasolinera = $gasolinera;
    }

    function setPublicidad($publicidad) {
        $this->publicidad = $publicidad;
    }

    function setDolar($dolar) {
        $this->dolar = $dolar;
    }

    function setMaster($master) {
        $this->master = $master;
    }

    function setEntrada($entrada) {
        $this->entrada = $entrada;
    }

    function setClave_regimen($clave_regimen) {
        $this->clave_regimen = $clave_regimen;
    }

    function setVersion_cfdi($version_cfdi) {
        $this->version_cfdi = $version_cfdi;
    }
}//CiaVO
