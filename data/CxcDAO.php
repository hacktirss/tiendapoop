<?php

/**
 * Description of CxcDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("CxcVO.php");

class CxcDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "cxc";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \CxcVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "cuenta, "
                . "fecha, "
                . "referencia, "
                . "tm, "
                . "fechav, "
                . "concepto, "
                . "importe, "
                . "reciboant, "
                . "recibo,"
                . "factura "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("issssssssii",
                    $objectVO->getCia(),
                    $objectVO->getCuenta(),
                    $objectVO->getFecha(),
                    $objectVO->getReferencia(),
                    $objectVO->getTm(),
                    $objectVO->getFechav(),
                    $objectVO->getConcepto(),
                    $objectVO->getImporte(),
                    $objectVO->getReciboant(),
                    $objectVO->getRecibo(),
                    $objectVO->getFactura()
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
     * @return \CxcVO
     */
    public function fillObject($rs) {
        $objectVO = new CxcVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setCuenta($rs["cuenta"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setReferencia($rs["referencia"]);
            $objectVO->setTm($rs["tm"]);
            $objectVO->setFechav($rs["fechav"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setReciboant($rs["reciboant"]);
            $objectVO->setRecibo($rs["recibo"]);
            $objectVO->setFactura($rs["factura"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \CxcVO
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
     * @return \CxcVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new CxcVO();
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
     * @param \CxcVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "cuenta = ?, "
                . "fecha = ?, "
                . "referencia = ?, "
                . "tm = ?, "
                . "fechav = ?, "
                . "concepto = ?, "
                . "importe = ?, "
                . "reciboant = ?, "
                . "recibo = ?, "
                . "factura = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssiii",
                    $objectVO->getCuenta(),
                    $objectVO->getFecha(),
                    $objectVO->getReferencia(),
                    $objectVO->getTm(),
                    $objectVO->getFechav(),
                    $objectVO->getConcepto(),
                    $objectVO->getImporte(),
                    $objectVO->getReciboant(),
                    $objectVO->getRecibo(),
                    $objectVO->getFactura(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
