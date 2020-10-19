<?php

/**
 * Description of SubmenuDAO
 * omicrom®
 * © 2020, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ('mysqlUtils.php');
include_once ('FunctionsDAO.php');
include_once ('SubmenuVO.php');

class SubmenuDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "submenus";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \SubmenuVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "menu, "
                . "submenu, "
                . "url, "
                . "permisos, "
                . "posicion "
                . ") "
                . "VALUES(?, ?, ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssii",
                    $objectVO->getMenu(),
                    $objectVO->getSubmenu(),
                    $objectVO->getUrl(),
                    $objectVO->getPermisos(),
                    $objectVO->getPosicion()
            );
            if ($ps->execute()) {
                $id = $ps->insert_id;
                $ps->close();
                return $id;
            } else {
                error_log($this->conn->error);
            }
            $ps->close();
        } else {
            error_log($this->conn->error);
        }
        return $id;
    }

    /**
     * 
     * @param array() $rs
     * @return \SubmenuVO
     */
    public function fillObject($rs) {
        $objectVO = new SubmenuVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setMenu($rs["menu"]);
            $objectVO->setSubmenu($rs["submenu"]);
            $objectVO->setUrl($rs["url"]);
            $objectVO->setPermisos($rs["permisos"]);
            $objectVO->setPosicion($rs["posicion"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \SubmenuVO
     */
    public function getAll($sql) {
        $array = array();
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $objectVO = $this->fillObject($rs);
                array_push($array, $objectVO);
            }
        } else {
            error_log($this->conn->error);
        }
        return $array;
    }

    /**
     * 
     * @param int $idObjectVO Llave primaria o identificador 
     * @param string $field Nombre del campo para borrar
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function remove($idObjectVO, $field = "id") {
        $sql = "DELETE FROM " . self::TABLA . " WHERE " . $field . " = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $idObjectVO
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param int $idObjectVO Llave primaria o identificador 
     * @param string $field Nombre del campo a buscar
     * @return \SubmenuVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new SubmenuVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "'";
        //error_log($sql);
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
            return $objectVO;
        } else {
            error_log($this->conn->error);
        }
        return $objectVO;
    }

    /**
     * 
     * @param \SubmenuVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "menu = ?, "
                . "submenu = ?, "
                . "url = ?, "
                . "permisos = ?, "
                . "posicion = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssiii",
                    $objectVO->getMenu(),
                    $objectVO->getSubmenu(),
                    $objectVO->getUrl(),
                    $objectVO->getPermisos(),
                    $objectVO->getPosicion(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
