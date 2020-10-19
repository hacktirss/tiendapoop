<?php

/**
 * Description of NotaEntradaEquipoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("NotaEntradaEquipoVO.php");

class NotaEntradaEquipoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "nee";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \NotaEntradaEquipoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "fecha, "
                . "concepto, "
                . "fechafac, "
                . "factura, "
                . "responsable, "
                . "proveedor, "
                . "importe, "
                . "status, "
                . "egreso, "
                . "costo_entrada, "
                . "cantidad "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssssssssss",
                    $objectVO->getCia(),
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
                    $objectVO->getFechafac(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getEgreso(),
                    $objectVO->getCosto_entrada(),
                    $objectVO->getCantidad()
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
     * @return \NotaEntradaEquipoVO
     */
    public function fillObject($rs) {
        $objectVO = new NotaEntradaEquipoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setFechafac($rs["fechafac"]);
            $objectVO->setFactura($rs["factura"]);
            $objectVO->setResponsable($rs["responsable"]);
            $objectVO->setProveedor($rs["proveedor"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setEgreso($rs["egreso"]);
            $objectVO->setCosto_entrada($rs["costo_entrada"]);
            $objectVO->setCantidad($rs["cantidad"]);
            $objectVO->setDetalle($rs["sumDetalle"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \NotaEntradaEquipoVO
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
     * @return \NotaEntradaEquipoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new NotaEntradaEquipoVO();
        $sql = "SELECT " . self::TABLA . ".*, IFNULL(SUM(need.cantidad * need.costo), 0) sumDetalle
                FROM " . self::TABLA . " 
                LEFT JOIN need ON " . self::TABLA . ".id = need.id
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
     * @param \NotaEntradaEquipoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha = ?, "
                . "concepto = ?, "
                . "fechafac = ?, "
                . "factura = ?, "
                . "responsable = ?, "
                . "proveedor = ?, "
                . "importe = ?, "
                . "status = ?, "
                . "egreso = ?, "
                . "costo_entrada = ?, "
                . "cantidad = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssssssii",
                    $objectVO->getFecha(),
                    $objectVO->getConcepto(),
                    $objectVO->getFechafac(),
                    $objectVO->getFactura(),
                    $objectVO->getResponsable(),
                    $objectVO->getProveedor(),
                    $objectVO->getImporte(),
                    $objectVO->getStatus(),
                    $objectVO->getEgreso(),
                    $objectVO->getCosto_entrada(),
                    $objectVO->getCantidad(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
