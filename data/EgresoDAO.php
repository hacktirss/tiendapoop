<?php

/**
 * Description of EgresoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("BasicEnum.php");
include_once ("EgresoVO.php");

class EgresoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "egresos";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \EgresoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "creacion, "
                . "fecha, "
                . "ordendepago, "
                . "banco, "
                . "formadepago, "
                . "entradaid, "
                . "observaciones, "
                . "pagoreal, "
                . "otropago"
                . ") "
                . "VALUES(?,?,NOW(),?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iissssssss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getFecha(),
                    $objectVO->getOrdendepago(),
                    $objectVO->getBanco(),
                    $objectVO->getFormadepago(),
                    $objectVO->getEntradaid(),
                    $objectVO->getObservaciones(),
                    $objectVO->getPagoreal(),
                    $objectVO->getOtropago()
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
     * @return \EgresoVO
     */
    public function fillObject($rs) {
        $objectVO = new EgresoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setCreacion($rs["creacion"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setOrdendepago($rs["ordendepago"]);
            $objectVO->setBanco($rs["banco"]);
            $objectVO->setFormadepago($rs["formadepago"]);
            $objectVO->setEntradaid($rs["entradaid"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setPagoreal($rs["pagoreal"]);
            $objectVO->setOtropago($rs["otropago"]);
            $objectVO->setBanco_nombre($rs["banco_nombre"]);
            $objectVO->setProveedor_nombre($rs["proveedor_nombre"]);
            $objectVO->setOrden_fecha($rs["orden_fecha"]);
            $objectVO->setOrden_concepto($rs["orden_concepto"]);
            $objectVO->setOrden_importe($rs["orden_importe"]);
            $objectVO->setOrden_proveedor($rs["proveedor"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \EgresoVO
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
     * @param \EgresoVO $objectVO
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
     * @return \EgresoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new EgresoVO();
        $sql = "SELECT " . self::TABLA . ".*,
                bancos.nombre banco_nombre, prv.nombre proveedor_nombre, prv.id proveedor,
                ordpagos.importe orden_importe, ordpagos.concepto orden_concepto, ordpagos.fecha orden_fecha
                FROM " . self::TABLA . " 
                LEFT JOIN bancos ON egresos.banco = bancos.id
                LEFT JOIN ordpagos ON egresos.ordendepago = ordpagos.id
                LEFT JOIN prv ON ordpagos.proveedor = prv.id
                WHERE 1 = 1 AND " . self::TABLA . "." . $field . " = '" . $idObjectVO . "'";
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
     * @param \EgresoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO = EgresoVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha = ?, "
                . "ordendepago = ?, "
                . "banco = ?, "
                . "formadepago = ?, "
                . "entradaid = ?, "
                . "observaciones = ?, "
                . "pagoreal = ?, "
                . "otropago = ? "
                . "WHERE id = ? AND cia = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssii",
                    $objectVO->getFecha(),
                    $objectVO->getOrdendepago(),
                    $objectVO->getBanco(),
                    $objectVO->getFormadepago(),
                    $objectVO->getEntradaid(),
                    $objectVO->getObservaciones(),
                    $objectVO->getPagoreal(),
                    $objectVO->getOtropago(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
