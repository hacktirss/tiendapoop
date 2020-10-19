<?php

/*
 * FcDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("BasicEnum.php");
include_once ("FcVO.php");

class FcDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "fc";
    const SINTIMBRAR = "-----";
    
    private $conn;

    public function __construct() {
        $this->conn=getConnection();
    }

    public function __destruct() {
        $this->conn->close();
    }
    
    /**
     * 
     * @param FcVO $objectVO
     * @return int|boolean
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " (cia, serie, cliente, fecha, status, rfc, folio, tipo, formadepago) "
                . "VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)";
        if (($ps=$this->conn->prepare($sql))) {
            $ps->bind_param("isssssss", 
                    $objectVO->getCia(),
                    $objectVO->getSerie(),
                    $objectVO->getCliente(),
                    $objectVO->getStatus(),
                    $objectVO->getRfc(),
                    $objectVO->getFolio(),
                    $objectVO->getTipo(),
                    $objectVO->getFormadepago()
                    );
            $id = $ps->execute() ? $ps->insert_id : -1;
            if($id <= 0){
                error_log($this->conn->error);
            }
            $ps->close();
        }
        return $id;
    }
   
    
    /**
     * 
     * @param array $rs
     * @return FcVO
     */
    public function fillObject($rs) {
        $objectVO = new FcVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setFolio($rs["folio"]);
            $objectVO->setSerie($rs["serie"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setFechatimbrado($rs["fechatimbrado"]);
            $objectVO->setCliente($rs["cliente"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setCantidad($rs["cantidad"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setIva($rs["iva"]);
            $objectVO->setIeps($rs["ieps"]);
            $objectVO->setIsr($rs["isr"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setTotal($rs["total"]);
            $objectVO->setUuid($rs["uuid"]);
            $objectVO->setRetencioniva($rs["retencioniva"]);
            $objectVO->setPagos($rs["pagos"]);
            $objectVO->setCndpago($rs["cndpago"]);
            $objectVO->setTipo($rs["tipo"]);
            $objectVO->setHospedaje($rs["hospedaje"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setMoneda($rs["moneda"]);
            $objectVO->setTiporelacion($rs["tiporelacion"]);
            $objectVO->setRelacioncfdi($rs["relacioncfdi"]);
            $objectVO->setFormadepago($rs["formadepago"]);
            $objectVO->setMetododepago($rs["metododepago"]);
            $objectVO->setUsocfdi($rs["usocfdi"]);
        }
        return $objectVO;
    }
    
    /**
     * 
     * @param string $sql
     * @return array \FcVO
     */
    public function getAll($sql) {
        $array = array();
        if(empty($sql)){
            $sql = "SELECT * FROM " . self::TABLA . " WHERE 1 = 1 ";
        }
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
     * @param FcVO $objectVO
     * @return boolean
     */
    public function remove($idObjectVO, $field = "id"){
        $sql = "DELETE FROM " . self::TABLA . " WHERE $field = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $idObjectVO
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param int $idLista
     * @return FcVO
     */
    public function retrieve($idObject, $field = "id", $cia = 0) {
        $objectVO = new FcVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE $field = " . $idObject;
        if(!empty($cia) && is_numeric($cia)){
            $sql .= " AND " . self::TABLA . ".cia = $cia"; 
        }
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
        }
        return $objectVO;
    }

    
    /**
     * 
     * @param FcVO $objectVO
     * @return boolean
     */
    public function update($objectVO) {
        //$objectVO = new FcVO();
        $sql = "UPDATE " . self::TABLA . " SET "
                . "folio = ?, "
                . "serie = ?, "
                . "rfc = ?, "
                . "fecha = ?, "
                . "fechatimbrado = ?, "
                . "cliente = ?, "
                . "observaciones = ?, "
                . "cantidad = ?, "
                . "importe = ?, "
                . "iva = ?, "
                . "ieps = ?, "
                . "isr = ?, "
                . "status = ?, "
                . "total = ?, "
                . "uuid = ?, "
                . "retencioniva = ?, "
                . "pagos = ?, "
                . "cndpago = ?, "
                . "tipo = ?, "
                . "hospedaje = ?, "
                . "concepto = ?, "
                . "moneda = ?, "
                . "tiporelacion = ?, "
                . "relacioncfdi = ?, "
                . "formadepago = ?, "
                . "metododepago = ?, "
                . "usocfdi = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssssssssssssssssssssssi",
                    $objectVO->getFolio(),
                    $objectVO->getSerie(),
                    $objectVO->getRfc(),
                    $objectVO->getFecha(),
                    $objectVO->getFechatimbrado(),
                    $objectVO->getCliente(),
                    $objectVO->getObservaciones(),
                    $objectVO->getCantidad(),
                    $objectVO->getImporte(),
                    $objectVO->getIva(),
                    $objectVO->getIeps(),
                    $objectVO->getIsr(),
                    $objectVO->getStatus(),
                    $objectVO->getTotal(),
                    $objectVO->getUuid(),
                    $objectVO->getRetencioniva(),
                    $objectVO->getPagos(),
                    $objectVO->getCndpago(),
                    $objectVO->getTipo(),
                    $objectVO->getHospedaje(),
                    $objectVO->getConcepto(),
                    $objectVO->getMoneda(),
                    $objectVO->getTiporelacion(),
                    $objectVO->getRelacioncfdi(),
                    $objectVO->getFormadepago(),
                    $objectVO->getMetododepago(),
                    $objectVO->getUsocfdi(),
                    $objectVO->getId()
            );
            return $ps->execute();
        } else {
            error_log($this->conn->error);
        }
    }

    public function setMetodoPago($id, $metodopago) {
        $sql = "UPDATE fc SET metododepago = '" . $metodopago . "' WHERE id = " . $id;
        if ($this->conn->query($sql)) {
            return true;
        }

        if (mysqli_errno($this->conn)) {
            throw new Exception(mysqli_error($this->conn));
        }
        return false;
    }

    public function setFormaPago($id, $formadepago) {
        $sql = "UPDATE fc SET formadepago = '" . $formadepago . "' WHERE id = " . $id;
        if ($this->conn->query($sql)) {
            return true;
        }

        if (mysqli_errno($this->conn)) {
            throw new Exception(mysqli_error($this->conn));
        }
        return false;
    }

    public function setUsoCFDI($id, $usocfdi) {
        $sql = "UPDATE fc SET usocfdi = '" . $usocfdi . "' WHERE id = " . $id;
        if ($this->conn->query($sql)) {
            return true;
        }

        if (mysqli_errno($this->conn)) {
            throw new Exception(mysqli_error($this->conn));
        }
        return false;
    }

    public function setObservaciones($id, $observaciones) {
        $sql = "UPDATE fc SET observaciones = '".$observaciones."' WHERE id=".$id;
        if ($this->conn->query($sql)) {
            return true;
        }

        if (mysqli_errno($this->conn)) {
            throw new Exception(mysqli_error($this->conn));
        }
        return false;
    }

    public function setConcepto($id, $observaciones) {
        $sql = "UPDATE fc SET concepto = '".$observaciones."' WHERE id=".$id;
        if ($this->conn->query($sql)) {
            return true;
        }

        if (mysqli_errno($this->conn)) {
            throw new Exception(mysqli_error($this->conn));
        }
        return false;
    }
}


abstract class StatusFacturas extends BasicEnum {

    const ABIERTA = "Abierta";
    const CERRADA = "Timbrada";
    const CANCELADA = "Cancelada";
    const CANCELADAST = "Cancelada S/T";
}

abstract class TipoFacturas extends BasicEnum {

    const FACTURACION = 1;
    const NOTAS_CREDITO = 4;
}

