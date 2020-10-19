<?php

/**
 * Description of MenuDAO
 * omicrom®
 * © 2020, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anata
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("MenuVO.php");

class MenuDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "menus";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \MenuVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "nombre, "
                . "orden, "
                . "tipo, "
                . "descripcion "
                . ") "
                . "VALUES(?, ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssss",
                    $objectVO->getNombre(),
                    $objectVO->getOrden(),
                    $objectVO->getTipo(),
                    $objectVO->getDescripcion()
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
     * @return \MenuVO
     */
    public function fillObject($rs) {
        $objectVO = new MenuVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setOrden($rs["orden"]);
            $objectVO->setTipo($rs["tipo"]);
            $objectVO->setDescripcion($rs["descripcion"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \MenuVO
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
     * @return \MenuVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new MenuVO();
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
     * @param \MenuVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "orden = ?, "
                . "tipo = ?, "
                . "descripcion = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssi",
                   $objectVO->getNombre(),
                    $objectVO->getOrden(),
                    $objectVO->getTipo(),
                    $objectVO->getDescripcion(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
