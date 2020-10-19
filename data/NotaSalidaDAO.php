<?php

/**
 * Description of NotaSalidaDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("NotaSalidaVO.php");

class NotaSalidaDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "ns";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \NotaSalidaVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "fecha, "
                . "concepto, "
                . "factura, "
                . "responsable, "
                . "status, "
                . "observaciones, "
                . "cliente "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssissi",
                    $objectVO->getCia(),
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getStatus(),
                    $objectVO->getObservaciones(),
                    $objectVO->getCliente()
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
     * @return \NotaSalidaVO
     */
    public function fillObject($rs) {
        $objectVO = new NotaSalidaVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setFactura($rs["factura"]);
            $objectVO->setResponsable($rs["responsable"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setCliente($rs["cliente"]);
            $objectVO->setDetalle($rs["sumDetalle"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \NotaSalidaVO
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
     * @return \NotaSalidaVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new NotaSalidaVO();
        $sql = "SELECT  " . self::TABLA . ".*, IFNULL(SUM(nsd.cantidad * nsd.costo), 0) sumDetalle FROM " . self::TABLA . " 
                LEFT JOIN nsd ON ns.id = nsd.id 
                WHERE " . self::TABLA . "." . $field . " = '" . $idObjectVO . "'";
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
     * @param \NotaSalidaVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha = ?, "
                . "concepto = ?, "
                . "factura = ?, "
                . "responsable = ?, "
                . "status = ?, "
                . "observaciones = ?, "
                . "cliente = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssissiii",
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getStatus(),
                    $objectVO->getObservaciones(),
                    $objectVO->getCliente(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}

abstract class StatusNotaSalida extends BasicEnum {

    const CERRADA = "Cerrada";
    const ABIERTA = "Abierta";
    const CANCELADA = "Cancelada";

}

abstract class TipoNotaSalida extends BasicEnum {

    const PRODUCTO = 1;
    const EQUIPO = 2;

}
