<?php

/**
 * Description of UsuarioVO
 *
 * @author Tirso
 */
class UsuarioVO {

    private $cia = 0;
    private $idUsuario;
    private $nombre;
    private $username;
    private $password;
    private $team = 0;
    private $level = 9;
    private $status;
    private $lastlogin;
    private $lastactivity;
    private $count = 0;
    private $creation;
    private $locked = 0;
    private $alive = 0;
    private $mail;

    function __construct() {
        
    }

    function getId() {
        return $this->idUsuario;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getTeam() {
        return $this->team;
    }

    function getLevel() {
        return $this->level;
    }

    function getStatus() {
        return $this->status;
    }

    function getLastlogin() {
        return $this->lastlogin;
    }

    function getCount() {
        return $this->count;
    }

    function getCreation() {
        return $this->creation;
    }

    function getLocked() {
        return $this->locked;
    }

    function getAlive() {
        return $this->alive;
    }

    function getMail() {
        return $this->mail;
    }

    function setId($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setTeam($team) {
        $this->team = $team;
    }

    function setLevel($level) {
        $this->level = $level;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setLastlogin($lastlogin) {
        $this->lastlogin = $lastlogin;
    }

    function setCount($count) {
        $this->count = $count;
    }

    function setCreation($creation) {
        $this->creation = $creation;
    }

    function setLocked($locked) {
        $this->locked = $locked;
    }

    function setAlive($alive) {
        $this->alive = $alive;
    }

    function setMail($mail) {
        $this->mail = $mail;
    }
    
    function getLastactivity() {
        return $this->lastactivity;
    }

    function setLastactivity($lastactivity) {
        $this->lastactivity = $lastactivity;
    }

    function getCia() {
        return $this->cia;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function __toString() {
        $objectClass = "{idUser = " . $this->idUsuario . ", uname = " . $this->nombre . ", lastlogin = " . $this->lastlogin . "}";
        return $objectClass;
    }

}
