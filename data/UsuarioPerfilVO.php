<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//namespace com\detisa\omicrom;

/**
 * Description of UsuarioPerfilVO
 *
 * @author 3PX89LA_RS5
 */
class UsuarioPerfilVO {

    private $id;
    private $idUsuario;
    private $idMenu;
    private $permisos;
    private $editable;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getIdMenu() {
        return $this->idMenu;
    }

    function getPermisos() {
        return $this->permisos;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setIdMenu($idMenu) {
        $this->idMenu = $idMenu;
    }

    function setPermisos($permisos) {
        $this->permisos = $permisos;
    }
    
    function getEditable() {
        return $this->editable;
    }

    function setEditable($editable) {
        $this->editable = $editable;
    }

}
