<?php

/**
 * Description of CertificadoVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class CertificadoVO {

    private $id;
    private $cia;
    private $rfc;
    private $clave;
    private $regimen = "601";
    private $certificado = NULL;
    private $llave = NULL;
    private $certificado_pfx = NULL;
    private $logo = NULL;
    private $habilitado = 1;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getCia() {
        return $this->cia;
    }

    function getRfc() {
        return $this->rfc;
    }

    function getClave() {
        return $this->clave;
    }

    function getRegimen() {
        return $this->regimen;
    }

    function getCertificado() {
        return $this->certificado;
    }

    function getLlave() {
        return $this->llave;
    }

    function getCertificado_pfx() {
        return $this->certificado_pfx;
    }

    function getHabilitado() {
        return $this->habilitado;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setRegimen($regimen) {
        $this->regimen = $regimen;
    }

    function setCertificado($certificado) {
        $this->certificado = $certificado;
    }

    function setLlave($llave) {
        $this->llave = $llave;
    }

    function setCertificado_pfx($certificado_pfx) {
        $this->certificado_pfx = $certificado_pfx;
    }

    function setHabilitado($habilitado) {
        $this->habilitado = $habilitado;
    }

    function getLogo() {
        return $this->logo;
    }

    function setLogo($logo) {
        $this->logo = $logo;
    }

}
