<?php

/**
 * Description of CliVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class CliVO {

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
    private $rfc = "XAXX010101000";
    private $codigo;
    private $correo;
    private $numeroext;
    private $numeroint;
    private $enviarcorreo = "No";
    private $cuentaban = 0;
    private $estado;
    private $formadepago = "01";
    private $atencion;
    private $dominio = "e00000.omicrom.dyndns.org";
    private $poliza = 0;
    private $tipodepago = "PUE";
    private $programaac;
    private $usuarioac;
    private $passwordac;
    private $webkey;
    private $mac;
    private $tipodecliente = "Gasolinera";
    private $contacto1;
    private $contacto2;
    private $contacto3;
    private $contacto4;
    private $contacto5;
    private $telefono1;
    private $telefono2;
    private $telefono3;
    private $telefono4;
    private $telefono5;
    private $correo1;
    private $correo2;
    private $correo3;
    private $correo4;
    private $correo5;
    private $puesto1;
    private $puesto2;
    private $puesto3;
    private $puesto4;
    private $puesto5;
    private $fechainstal;
    private $nestacion;
    private $tipointerfaz = "EPSILON";
    private $marcaserver;
    private $nserieserver;
    private $marcaimpre;
    private $serieimpre;
    private $dirip = "192.168.1.200";
    private $facturacion = "Omicrom";
    private $direccion_exp;
    private $colonia_exp;
    private $municipio_exp;
    private $estado_exp;
    private $numeroint_exp;
    private $numeroext_exp;
    private $codigo_exp;
    private $marcadispensario;
    private $marcasensort;
    private $siic;
    private $siic_contrasena;
    private $status = "Activo";

    function __construct() {
        
    }

    function getId() {
        return $this->id;
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

    function getRfc() {
        return $this->rfc;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getNumeroext() {
        return $this->numeroext;
    }

    function getNumeroint() {
        return $this->numeroint;
    }

    function getEnviarcorreo() {
        return $this->enviarcorreo;
    }

    function getCuentaban() {
        return $this->cuentaban;
    }

    function getEstado() {
        return $this->estado;
    }

    function getFormadepago() {
        return $this->formadepago;
    }

    function getAtencion() {
        return $this->atencion;
    }

    function getDominio() {
        return $this->dominio;
    }

    function getPoliza() {
        return $this->poliza;
    }

    function getTipodepago() {
        return $this->tipodepago;
    }

    function getProgramaac() {
        return $this->programaac;
    }

    function getUsuarioac() {
        return $this->usuarioac;
    }

    function getPasswordac() {
        return $this->passwordac;
    }

    function getWebkey() {
        return $this->webkey;
    }

    function getMac() {
        return $this->mac;
    }

    function getTipodecliente() {
        return $this->tipodecliente;
    }

    function getContacto1() {
        return $this->contacto1;
    }

    function getContacto2() {
        return $this->contacto2;
    }

    function getContacto3() {
        return $this->contacto3;
    }

    function getContacto4() {
        return $this->contacto4;
    }

    function getContacto5() {
        return $this->contacto5;
    }

    function getTelefono1() {
        return $this->telefono1;
    }

    function getTelefono2() {
        return $this->telefono2;
    }

    function getTelefono3() {
        return $this->telefono3;
    }

    function getTelefono4() {
        return $this->telefono4;
    }

    function getTelefono5() {
        return $this->telefono5;
    }

    function getCorreo1() {
        return $this->correo1;
    }

    function getCorreo2() {
        return $this->correo2;
    }

    function getCorreo3() {
        return $this->correo3;
    }

    function getCorreo4() {
        return $this->correo4;
    }

    function getCorreo5() {
        return $this->correo5;
    }

    function getPuesto1() {
        return $this->puesto1;
    }

    function getPuesto2() {
        return $this->puesto2;
    }

    function getPuesto3() {
        return $this->puesto3;
    }

    function getPuesto4() {
        return $this->puesto4;
    }

    function getPuesto5() {
        return $this->puesto5;
    }

    function getFechainstal() {
        return !empty($this->fechainstal) ? $this->fechainstal : date("Y-m-d");
    }

    function getNestacion() {
        return $this->nestacion;
    }

    function getTipointerfaz() {
        return $this->tipointerfaz;
    }

    function getMarcaserver() {
        return $this->marcaserver;
    }

    function getNserieserver() {
        return $this->nserieserver;
    }

    function getMarcaimpre() {
        return $this->marcaimpre;
    }

    function getSerieimpre() {
        return $this->serieimpre;
    }

    function getDirip() {
        return $this->dirip;
    }

    function getFacturacion() {
        return $this->facturacion;
    }

    function getDireccion_exp() {
        return $this->direccion_exp;
    }

    function getColonia_exp() {
        return $this->colonia_exp;
    }

    function getMunicipio_exp() {
        return $this->municipio_exp;
    }

    function getEstado_exp() {
        return $this->estado_exp;
    }

    function getNumeroint_exp() {
        return $this->numeroint_exp;
    }

    function getNumeroext_exp() {
        return $this->numeroext_exp;
    }

    function getCodigo_exp() {
        return $this->codigo_exp;
    }

    function getMarcadispensario() {
        return $this->marcadispensario;
    }

    function getMarcasensort() {
        return $this->marcasensort;
    }

    function getSiic() {
        return $this->siic;
    }

    function getSiic_contrasena() {
        return $this->siic_contrasena;
    }

    function getStatus() {
        return $this->status;
    }

    function setId($id) {
        $this->id = $id;
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

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setNumeroext($numeroext) {
        $this->numeroext = $numeroext;
    }

    function setNumeroint($numeroint) {
        $this->numeroint = $numeroint;
    }

    function setEnviarcorreo($enviarcorreo) {
        $this->enviarcorreo = $enviarcorreo;
    }

    function setCuentaban($cuentaban) {
        $this->cuentaban = $cuentaban;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setFormadepago($formadepago) {
        $this->formadepago = $formadepago;
    }

    function setAtencion($atencion) {
        $this->atencion = $atencion;
    }

    function setDominio($dominio) {
        $this->dominio = $dominio;
    }

    function setPoliza($poliza) {
        $this->poliza = $poliza;
    }

    function setTipodepago($tipodepago) {
        $this->tipodepago = $tipodepago;
    }

    function setProgramaac($programaac) {
        $this->programaac = $programaac;
    }

    function setUsuarioac($usuarioac) {
        $this->usuarioac = $usuarioac;
    }

    function setPasswordac($passwordac) {
        $this->passwordac = $passwordac;
    }

    function setWebkey($webkey) {
        $this->webkey = $webkey;
    }

    function setMac($mac) {
        $this->mac = $mac;
    }

    function setTipodecliente($tipodecliente) {
        $this->tipodecliente = $tipodecliente;
    }

    function setContacto1($contacto1) {
        $this->contacto1 = $contacto1;
    }

    function setContacto2($contacto2) {
        $this->contacto2 = $contacto2;
    }

    function setContacto3($contacto3) {
        $this->contacto3 = $contacto3;
    }

    function setContacto4($contacto4) {
        $this->contacto4 = $contacto4;
    }

    function setContacto5($contacto5) {
        $this->contacto5 = $contacto5;
    }

    function setTelefono1($telefono1) {
        $this->telefono1 = $telefono1;
    }

    function setTelefono2($telefono2) {
        $this->telefono2 = $telefono2;
    }

    function setTelefono3($telefono3) {
        $this->telefono3 = $telefono3;
    }

    function setTelefono4($telefono4) {
        $this->telefono4 = $telefono4;
    }

    function setTelefono5($telefono5) {
        $this->telefono5 = $telefono5;
    }

    function setCorreo1($correo1) {
        $this->correo1 = $correo1;
    }

    function setCorreo2($correo2) {
        $this->correo2 = $correo2;
    }

    function setCorreo3($correo3) {
        $this->correo3 = $correo3;
    }

    function setCorreo4($correo4) {
        $this->correo4 = $correo4;
    }

    function setCorreo5($correo5) {
        $this->correo5 = $correo5;
    }

    function setPuesto1($puesto1) {
        $this->puesto1 = $puesto1;
    }

    function setPuesto2($puesto2) {
        $this->puesto2 = $puesto2;
    }

    function setPuesto3($puesto3) {
        $this->puesto3 = $puesto3;
    }

    function setPuesto4($puesto4) {
        $this->puesto4 = $puesto4;
    }

    function setPuesto5($puesto5) {
        $this->puesto5 = $puesto5;
    }

    function setFechainstal($fechainstal) {
        $this->fechainstal = $fechainstal;
    }

    function setNestacion($nestacion) {
        $this->nestacion = $nestacion;
    }

    function setTipointerfaz($tipointerfaz) {
        $this->tipointerfaz = $tipointerfaz;
    }

    function setMarcaserver($marcaserver) {
        $this->marcaserver = $marcaserver;
    }

    function setNserieserver($nserieserver) {
        $this->nserieserver = $nserieserver;
    }

    function setMarcaimpre($marcaimpre) {
        $this->marcaimpre = $marcaimpre;
    }

    function setSerieimpre($serieimpre) {
        $this->serieimpre = $serieimpre;
    }

    function setDirip($dirip) {
        $this->dirip = $dirip;
    }

    function setFacturacion($facturacion) {
        $this->facturacion = $facturacion;
    }

    function setDireccion_exp($direccion_exp) {
        $this->direccion_exp = $direccion_exp;
    }

    function setColonia_exp($colonia_exp) {
        $this->colonia_exp = $colonia_exp;
    }

    function setMunicipio_exp($municipio_exp) {
        $this->municipio_exp = $municipio_exp;
    }

    function setEstado_exp($estado_exp) {
        $this->estado_exp = $estado_exp;
    }

    function setNumeroint_exp($numeroint_exp) {
        $this->numeroint_exp = $numeroint_exp;
    }

    function setNumeroext_exp($numeroext_exp) {
        $this->numeroext_exp = $numeroext_exp;
    }

    function setCodigo_exp($codigo_exp) {
        $this->codigo_exp = $codigo_exp;
    }

    function setMarcadispensario($marcadispensario) {
        $this->marcadispensario = $marcadispensario;
    }

    function setMarcasensort($marcasensort) {
        $this->marcasensort = $marcasensort;
    }

    function setSiic($siic) {
        $this->siic = $siic;
    }

    function setSiic_contrasena($siic_contrasena) {
        $this->siic_contrasena = $siic_contrasena;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function isGasolinera() {
        return $this->tipodecliente === "Gasolinera";
    }
    
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

}
