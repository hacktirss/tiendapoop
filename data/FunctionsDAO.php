<?php

/**
 * Description of FunctionsDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */

interface FunctionsDAO {
    /**
     * Retrive first result by name field
     * @param int $idObjectVO Primary key or indentifier
     * @param string $field Name of table field
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0);
    
    /**
     * Add one object to DB
     * @param object $objectVO
     */
    public function create($objectVO);
    
    /**
     * Update one object to DB
     * @param object $objectVO
     */
    public function update($objectVO);
    
    /**
     * Delete one object to DB
     * @param object $objectVO
     * @param string $field Name of table field
     */
    public function remove($objectVO, $field = "id");
    
    /**
     * Fill object with array
     * @param array() $rs
     */
    public function fillObject($rs);
    
    /**
     * Retrive all rows by query
     * @param string $sql
     */
    public function getAll($sql);
}
