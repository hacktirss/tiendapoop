<?php

/*
 * RelacionesCfdiVO
 * omicrom�
 * � 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

class RelacionesCfdiVO {
    private $id;
    private $origen;
    private $uuid;
    private $uuid_relacionado;
    private $tipo_relacion;

    function getId() {
        return $this->id;
    }

    function getOrigen() {
        return $this->origen;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getUuid_relacionado() {
        return $this->uuid_relacionado;
    }

    function getTipo_relacion() {
        return $this->tipo_relacion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setOrigen($origen) {
        $this->origen = $origen;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setUuid_relacionado($uuid_relacionado) {
        $this->uuid_relacionado = $uuid_relacionado;
    }

    function setTipo_relacion($tipo_relacion) {
        $this->tipo_relacion = $tipo_relacion;
    }
}
