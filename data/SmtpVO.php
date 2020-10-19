<?php
/*
 * SmtpVO
 * GlobalFAE®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since feb 2018
 */

include_once ('softcoatl/SoftcoatlHTTP.php');

use com\softcoatl\utils as utils;

class SmtpVO extends utils\BaseVO {
    
    private $id;
    private $server;
    private $port;
    private $sender;
    private $auth;
    private $authtype;
    private $loginuser;
    private $loginpass;
    private $status;
    
    function getId($default = "") {
        return parent::uempty($this->id, $default);
    }

    function getServer($default = "") {
        return parent::uempty($this->server, $default);
    }

    function getPort($default = "") {
        return parent::uempty($this->port, $default);
    }

    function getSender($default = "") {
        return parent::uempty($this->sender, $default);
    }

    function getAuth($default = "") {
        return parent::uempty($this->auth, $default);
    }

    function getAuthtype($default = "") {
        return parent::uempty($this->authtype, $default);
    }

    function getLoginuser($default = "") {
        return parent::uempty($this->loginuser, $default);
    }

    function getLoginpass($default = "") {
        return parent::uempty($this->loginpass, $default);
    }

    function getStatus($default = "") {
        return parent::uempty($this->status, $default);
    }

    function setId($id) {
        $this->id = $id;
    }

    function setServer($server) {
        $this->server = $server;
    }

    function setPort($port) {
        $this->port = $port;
    }

    function setSender($sender) {
        $this->sender = $sender;
    }

    function setAuthtype($authtype) {
        $this->authtype = $authtype;
    }

    function setAuth($auth) {
        $this->auth = $auth;
    }

    function setLoginuser($loginuser) {
        $this->loginuser = $loginuser;
    }

    function setLoginpass($loginpass) {
        $this->loginpass = $loginpass;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    public static function parse($array) {
        $smtp = new SmtpVO();
        if (!empty($array)) {
            $smtp->setId(parent::uempty($array['id']));
            $smtp->setServer(parent::uempty($array['smtpname']));
            $smtp->setSender(parent::uempty($array['smtpsender']));
            $smtp->setPort(parent::uempty($array['smtpport']));
            $smtp->setAuth(parent::uempty($array['smtpauth']));
            $smtp->setAuthtype(parent::uempty($array['smtpauthtype']));
            $smtp->setLoginuser(parent::uempty($array['smtploginuser']));
            $smtp->setLoginpass(parent::uempty($array['smtploginpass']));
            $smtp->setStatus(parent::uempty($array['smtpvalido']));
        }
        return $smtp;
    }

}//SmtpVO
