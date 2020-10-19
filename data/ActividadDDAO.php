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
include_once ("ActividadDVO.php");

class ActividadDDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "actd";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \ActividadDVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "fecha, "
                . "concepto, "
                . "observaciones "
                . ") "
                . "VALUES(?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isss",
                    $objectVO->getActividad(),
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
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
     * @return \ActividadDVO
     */
    public function fillObject($rs) {
        $objectVO = new ActividadDVO();
        if (is_array($rs)) {
            $objectVO->setActividad($rs["id"]);
            $objectVO->setIdnvo($rs["idnvo"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setObservaciones($rs["observaciones"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \ActividadDVO
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
     * @return \ActividadDVO
     */
    public function retrieve($idObjectVO, $field = "idnvo", $cia = 0) {
        $objectVO = new ActividadDVO();
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
     * @param \ActividadDVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "id = ?, "
                . "fecha = ?, "
                . "concepto = ?, "
                . "observaciones = ? "
                . "WHERE idnvo = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssi",
                    $objectVO->getActividad(),
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
                    $objectVO->getObservaciones(),
                    $objectVO->getIdnvo()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
