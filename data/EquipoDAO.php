<?php

/**
 * Description of EquipoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("EquipoVO.php");

class EquipoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "equipos";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \EquipoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "marca, "
                . "descripcion, "
                . "grupo, "
                . "numero_serie, "
                . "modelo, "
                . "costo, "
                . "precio, "
                . "numero_entrada "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iissssssss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getMarca(),
                    $objectVO->getDescripcion(),
                    $objectVO->getGrupo(),
                    $objectVO->getNumero_serie(),
                    $objectVO->getModelo(),
                    $objectVO->getCosto(),
                    $objectVO->getPrecio(),
                    $objectVO->getNumero_entrada()
            );
            if ($ps->execute()) {
                $id = $objectVO->getId();
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
     * @return \EquipoVO
     */
    public function fillObject($rs) {
        $objectVO = new EquipoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setMarca($rs["marca"]);
            $objectVO->setDescripcion($rs["descripcion"]);
            $objectVO->setGrupo($rs["grupo"]);
            $objectVO->setNumero_serie($rs["numero_serie"]);
            $objectVO->setModelo($rs["modelo"]);
            $objectVO->setCosto($rs["costo"]);
            $objectVO->setPrecio($rs["precio"]);
            $objectVO->setNumero_entrada($rs["numero_entrada"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \EquipoVO
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
     * @param \EquipoVO $objectVO
     * @param string $field Nombre del campo para borrar
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function remove($objectVO, $field = "id") {
        $sql = "DELETE FROM " . self::TABLA . " WHERE " . $field . " = ? AND cia = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ii", $objectVO->getId(), $objectVO->getCia()
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param int $idObjectVO Llave primaria o identificador 
     * @param string $field Nombre del campo a buscar
     * @param int $cia Llave compuesta
     * @return \EquipoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new EquipoVO();
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
     * @param \EquipoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "marca = ?, "
                . "descripcion = ?, "
                . "grupo = ?, "
                . "numero_serie = ?, "
                . "modelo = ?, "
                . "costo = ?, "
                . "precio = ?, "
                . "numero_entrada = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssii",
                    $objectVO->getMarca(),
                    $objectVO->getDescripcion(),
                    $objectVO->getGrupo(),
                    $objectVO->getNumero_serie(),
                    $objectVO->getModelo(),
                    $objectVO->getCosto(),
                    $objectVO->getPrecio(),
                    $objectVO->getNumero_entrada(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
