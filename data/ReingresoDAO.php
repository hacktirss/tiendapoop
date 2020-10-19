<?php

/**
 * Description of ReingresoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("ReingresoVO.php");

class ReingresoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "re";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \ReingresoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "fecha_entra, "
                . "concepto, "
                . "responsable, "
                . "proveedor, "
                . "cantidad, "
                . "importe, "
                . "status, "
                . "referencia, "
                . "ordpago "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iisssssssss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getFecha_entra(),
                    $objectVO->getConcepto(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getCantidad(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getReferencia(),
                    $objectVO->getOrdpago()
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
     * @return \ReingresoVO
     */
    public function fillObject($rs) {
        $objectVO = new ReingresoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha_entra($rs["fecha_entra"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setResponsable($rs["responsable"]);
            $objectVO->setProveedor($rs["proveedor"]);
            $objectVO->setCantidad($rs["cantidad"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setReferencia($rs["referencia"]);
            $objectVO->setOrdpago($rs["ordpago"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \ReingresoVO
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
     * @param \ReingresoVO $objectVO
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
     * @return \ReingresoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new ReingresoVO();
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
     * @param \ReingresoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha_entra = ?, "
                . "concepto = ?, "
                . "responsable = ?, "
                . "proveedor = ?, "
                . "cantidad = ?, "
                . "importe = ?, "
                . "status = ?, "
                . "referencia = ?, "
                . "ordpago = ? "
                . "WHERE id = ? AND cia = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssssii",
                    $objectVO->getFecha_entra(),
                    $objectVO->getConcepto(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getCantidad(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getReferencia(),
                    $objectVO->getOrdpago(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
