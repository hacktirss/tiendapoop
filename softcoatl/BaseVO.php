<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace com\softcoatl\utils;

/**
 * Description of BaseVO
 *
 * @author rolando
 */
abstract class BaseVO {
    public function uempty($value, $default = "") {
        return Utils::uempty($value, $default);
    }
    public abstract static function parse($array);

    public function __toString() {
        return print_r($this, true);
    }//toString
}
