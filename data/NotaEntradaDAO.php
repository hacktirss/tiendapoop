<?php

/**
 * Description of NotaEntradaDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("NotaEntradaVO.php");

class NotaEntradaDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "ne";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \NotaEntradaVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "fecha_entra, "
                . "concepto, "
                . "fechafac, "
                . "factura, "
                . "responsable, "
                . "proveedor, "
                . "cantidad, "
                . "importe, "
                . "status, "
                . "egreso, "
                . "ordpago "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssssssssii",
                    $objectVO->getCia(),
                    $objectVO->getFecha_entra(),
                    $objectVO->getConcepto(),
                    $objectVO->getFechafac(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getCantidad(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getEgreso(),
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
     * @return \NotaEntradaVO
     */
    public function fillObject($rs) {
        $objectVO = new NotaEntradaVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha_entra($rs["fecha_entra"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setFechafac($rs["fechafac"]);
            $objectVO->setFactura($rs["factura"]);
            $objectVO->setResponsable($rs["responsable"]);
            $objectVO->setProveedor($rs["proveedor"]);
            $objectVO->setCantidad($rs["cantidad"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setEgreso($rs["egreso"]);
            $objectVO->setOrdpago($rs["ordpago"]);
            $objectVO->setDetalle($rs["sumDetalle"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \NotaEntradaVO
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
     * @return \NotaEntradaVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new NotaEntradaVO();
        $sql = "SELECT  " . self::TABLA . ".*, IFNULL(SUM(ned.total), 0) sumDetalle FROM " . self::TABLA . " 
                LEFT JOIN ned ON ne.id = ned.id 
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
     * @param \NotaEntradaVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha_entra = ?, "
                . "concepto = ?, "
                . "fechafac = ?, "
                . "factura = ?, "
                . "responsable = ?, "
                . "proveedor = ?, "
                . "cantidad = ?, "
                . "importe = ?, "
                . "status = ?, "
                . "egreso = ?, "
                . "ordpago = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssssssi",
                    $objectVO->getFecha_entra(),
                    $objectVO->getConcepto(),
                    $objectVO->getFechafac(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getCantidad(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getEgreso(),
                    $objectVO->getOrdpago(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}

abstract class StatusNotaEntrada extends BasicEnum {

    const CERRADA = "Cerrada";
    const ABIERTA = "Abierta";
    const CANCELADA = "Cancelada";

}
