<?php

/**
 * Description of SubcategoriaDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("SubcategoriaVO.php");

class SubcategoriaDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "subcategorias";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \SubcategoriaVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "categoria, "
                . "nombre, "
                . "descripcion "
                . ") "
                . "VALUES(?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iss",
                    $objectVO->getCategoria(),
                    $objectVO->getNombre(),
                    $objectVO->getDescripcion()
            );
            if ($ps->execute()) {
                $id = $this->conn->insert_id;
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
     * @return \SubcategoriaVO
     */
    public function fillObject($rs) {
        $objectVO = new SubcategoriaVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCategoria($rs["categoria"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setDescripcion($rs["descripcion"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \SubcategoriaVO
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
     * @param \SubcategoriaVO $objectVO
     * @param string $field Nombre del campo para borrar
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function remove($objectVO, $field = "id") {
        $sql = "DELETE FROM " . self::TABLA . " WHERE " . $field . " = ?  LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("i", $objectVO->getId());
            return $ps->execute();
        }
    }

    /**
     * 
     * @param int $idObjectVO Llave primaria o identificador 
     * @param string $field Nombre del campo a buscar
     * @param int $cia Llave compuesta
     * @return \SubcategoriaVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new SubcategoriaVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "'";
        if(!empty($cia) && is_numeric($cia)){
            $sql .= " AND " . self::TABLA . ".cia = $cia"; 
        }
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
     * @param \SubcategoriaVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "descripcion = ? "
                . "WHERE id = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssii",
                    $objectVO->getNombre(),
                    $objectVO->getDescripcion(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
