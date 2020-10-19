<?php

/**
 * Description of OrdenPagoDAO
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
include_once ("OrdenPagoVO.php");

class OrdenPagoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "ordpagos";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \OrdenPagoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "fecha, "
                . "proveedor, "
                . "rubro, "
                . "concepto, "
                . "solicito, "
                . "cotizacion, "
                . "importe, "
                . "iva, "
                . "iva_ret, "
                . "isr, "
                . "hospedaje, "
                . "total, "
                . "observaciones, "
                . "pagonumero, "
                . "status"
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iisisssdddddddsis",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getFecha(),
                    $objectVO->getProveedor(),
                    $objectVO->getRubro(),
                    $objectVO->getConcepto(),
                    $objectVO->getSolicito(),
                    $objectVO->getCotizacion(),
                    $objectVO->getImporte(),
                    $objectVO->getIva(),
                    $objectVO->getIva_ret(),
                    $objectVO->getIsr(),
                    $objectVO->getHospedaje(),
                    $objectVO->getTotal(),
                    $objectVO->getObservaciones(),
                    $objectVO->getPagonumero(),
                    $objectVO->getStatus()
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
     * @return \OrdenPagoVO
     */
    public function fillObject($rs) {
        $objectVO = new OrdenPagoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setProveedor($rs["proveedor"]);
            $objectVO->setRubro($rs["rubro"]);
            $objectVO->setAlias($rs["alias"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setSolicito($rs["solicito"]);
            $objectVO->setCotizacion($rs["cotizacion"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setIsr($rs["isr"]);
            $objectVO->setIva_ret($rs["iva_ret"]);
            $objectVO->setIva($rs["iva"]);
            $objectVO->setHospedaje($rs["hospedaje"]);
            $objectVO->setTotal($rs["total"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setPagonumero($rs["pagonumero"]);
            $objectVO->setStatus($rs["status"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \OrdenPagoVO
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
     * @param \OrdenPagoVO $objectVO
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
     * @return \OrdenPagoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new OrdenPagoVO();
        $sql = "SELECT " . self::TABLA . ".*, IFNULL(prv.nombre,'') alias 
                FROM " . self::TABLA . " 
                LEFT JOIN prv ON " . self::TABLA . ".proveedor = prv.id
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
     * @param \OrdenPagoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fecha = ?, "
                . "proveedor = ?, "
                . "rubro = ?, "
                . "concepto = ?, "
                . "solicito = ?, "
                . "cotizacion = ?, "
                . "importe = ?, "
                . "iva = ?, "
                . "iva_ret = ?, "
                . "isr = ?, "
                . "hospedaje = ?, "
                . "total = ?, "
                . "observaciones = ?, "
                . "pagonumero = ?, "
                . "status = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sisssdddddddsisii",
                    $objectVO->getFecha(),
                    $objectVO->getProveedor(),
                    $objectVO->getRubro(),
                    $objectVO->getConcepto(),
                    $objectVO->getSolicito(),
                    $objectVO->getCotizacion(),
                    $objectVO->getImporte(),
                    $objectVO->getIva(),
                    $objectVO->getIva_ret(),
                    $objectVO->getIsr(),
                    $objectVO->getHospedaje(),
                    $objectVO->getTotal(),
                    $objectVO->getObservaciones(),
                    $objectVO->getPagonumero(),
                    $objectVO->getStatus(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}

abstract class StatusOrdenPago extends BasicEnum {

    const CERRADA = "Cerrada";
    const ABIERTA = "Abierta";
    const CANCELADA = "Cancelada";

}
