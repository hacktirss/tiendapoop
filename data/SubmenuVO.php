<?php

class SubmenuVO {

    private $id;
    private $menu;
    private $submenu;
    private $url;
    private $permisos;
    private $posicion;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getMenu() {
        return $this->menu;
    }

    function getSubmenu() {
        return $this->submenu;
    }

    function getUrl() {
        return $this->url;
    }

    function getPermisos() {
        return $this->permisos;
    }

    function getPosicion() {
        return $this->posicion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMenu($menu) {
        $this->menu = $menu;
    }

    function setSubmenu($submenu) {
        $this->submenu = $submenu;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setPermisos($permisos) {
        $this->permisos = $permisos;
    }

    function setPosicion($posicion) {
        $this->posicion = $posicion;
    }

}
