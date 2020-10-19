<?php

/**
 * Description of ActividadDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("ActividadVO.php");

class ActividadDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "act";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \ActividadVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "fecha, "
                . "descripcion, "
                . "tipo, "
                . "periodo, "
                . "lapso, "
                . "observaciones "
                . ") "
                . "VALUES(?,CURRENT_DATE(),?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("issiis",
                    $objectVO->getCia(),
                    $objectVO->getDescripcion(),
                    $objectVO->getTipo(),
                    $objectVO->getPeriodo(),
                    $objectVO->getLapso(),
                    $objectVO->getObservaciones()
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
     * @return \ActividadVO
     */
    public function fillObject($rs) {
        $objectVO = new ActividadVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setDescripcion($rs["descripcion"]);
            $objectVO->setPeriodo($rs["periodo"]);
            $objectVO->setTipo($rs["tipo"]);
            $objectVO->setLapso($rs["lapso"]);
            $objectVO->setObservaciones($rs["observaciones"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \ActividadVO
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
     * @return \ActividadVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new ActividadVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "'";
        if (!empty($cia) && is_numeric($cia)) {
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
     * @param \ActividadVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha = ?, "
                . "descripcion = ?, "
                . "tipo = ?, "
                . "periodo = ?, "
                . "lapso = ?, "
                . "observaciones = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssiisi",
                    $objectVO->getFecha(),
                    $objectVO->getDescripcion(),
                    $objectVO->getTipo(),
                    $objectVO->getPeriodo(),
                    $objectVO->getLapso(),
                    $objectVO->getObservaciones(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}


abstract class TipoPeriodo extends BasicEnum {

    const DAILY = 1;
    const WEEKLY = 2;
    const FORTNIGHTLY = 3;
    const MONTHLY = 4;
    const YEARLY = 5;
}
