<?php

/**
 * Description of FilesFileVO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class FilesFileVO {

    private $id;
    private $cia;
    private $filename;
    private $description;
    private $is_public;
    private $is_folder;
    private $created_at;
    private $size;
    private $user_id;
    private $folder_id;

    function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getFilename() {
        return $this->filename;
    }

    function getDescription() {
        return $this->description;
    }

    function getIs_public() {
        return $this->is_public;
    }

    function getIs_folder() {
        return $this->is_folder;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function getSize() {
        return $this->size;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function getFolder_id() {
        return $this->folder_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFilename($filename) {
        $this->filename = $filename;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setIs_public($is_public) {
        $this->is_public = $is_public;
    }

    function setIs_folder($is_folder) {
        $this->is_folder = $is_folder;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function setSize($size) {
        $this->size = $size;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setFolder_id($folder_id) {
        $this->folder_id = $folder_id;
    }

    public function __toString() {
        
    }
    
    function getCia() {
        return $this->cia;
    }

    function setCia($cia) {
        $this->cia = $cia;
    }



}
