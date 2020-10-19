<?php

/**
 * Description of PagoDAO
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
include_once ("PagoVO.php");

class PagoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const SIN_TIMBRAR = "-----";
    const TABLA = "ing";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \PagoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "folio, "
                . "fecha, "
                . "fechap, "
                . "cuenta, "
                . "concepto, "
                . "importe, "
                . "rubro, "
                . "aplicado, "
                . "referencia, "
                . "status, "
                . "banco, "
                . "formapago, "
                . "numoperacion, "
                . "uuid, "
                . "statusCFDI, "
                . "fechar"
                . ") "
                . "VALUES(?,?,NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("issssdsississsss",
                    $objectVO->getCia(),
                    $objectVO->getFolio(),
                    $objectVO->getFechap(),
                    $objectVO->getCuenta(),
                    $objectVO->getConcepto(),
                    $objectVO->getImporte(),
                    $objectVO->getRubro(),
                    $objectVO->getAplicado(),
                    $objectVO->getReferencia(),
                    $objectVO->getStatus(),
                    $objectVO->getBanco(),
                    $objectVO->getFormapago(),
                    $objectVO->getNumoperacion(),
                    $objectVO->getUuid(),
                    $objectVO->getStatuscfdi(),
                    $objectVO->getFechar()
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
     * @return \PagoVO
     */
    public function fillObject($rs) {
        $objectVO = new PagoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setFolio($rs["folio"]);
            $objectVO->setFecha($rs["fecha"]);
            $objectVO->setFechap($rs["fechap"]);
            $objectVO->setCuenta($rs["cuenta"]);
            $objectVO->setConcepto($rs["concepto"]);
            $objectVO->setImporte($rs["importe"]);
            $objectVO->setRubro($rs["rubro"]);
            $objectVO->setAplicado($rs["aplicado"]);
            $objectVO->setReferencia($rs["referencia"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setBanco($rs["banco"]);
            $objectVO->setFormapago($rs["formapago"]);
            $objectVO->setNumoperacion($rs["numoperacion"]);
            $objectVO->setUuid($rs["uuid"]);
            $objectVO->setStatuscfdi($rs["statusCFDI"]);
            $objectVO->setFechar($rs["fechar"]);
            $objectVO->setCliente($rs["cliente"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \PagoVO
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
     * @return \PagoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new PagoVO();
        $sql = "SELECT " . self::TABLA . ".*, CONCAT(cli.id, ' | ', cli.nombre,'') cliente
                FROM " . self::TABLA . " 
                LEFT JOIN cli ON " . self::TABLA . ".cuenta = cli.id 
                WHERE 1 = 1 AND " . self::TABLA . "." . $field . " = '" . $idObjectVO . "'";
        //error_log($sql);
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
            return $objectVO;
        } else {
            error_log($this->conn->error);
            error_log($sql);
        }
        return $objectVO;
    }

    /**
     * 
     * @param \PagoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "fechap = ?, "
                . "cuenta = ?, "
                . "concepto = ?, "
                . "importe = ?, "
                . "rubro = ?, "
                . "aplicado = ?, "
                . "referencia = ?, "
                . "status = ?, "
                . "banco = ?, "
                . "formapago = ?, "
                . "numoperacion = ?, "
                . "uuid = ?, "
                . "statusCFDI = ?, "
                . "fechar = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssdsississsssii",
                    $objectVO->getFechap(),
                    $objectVO->getCuenta(),
                    $objectVO->getConcepto(),
                    $objectVO->getImporte(),
                    $objectVO->getRubro(),
                    $objectVO->getAplicado(),
                    $objectVO->getReferencia(),
                    $objectVO->getStatus(),
                    $objectVO->getBanco(),
                    $objectVO->getFormapago(),
                    $objectVO->getNumoperacion(),
                    $objectVO->getUuid(),
                    $objectVO->getStatuscfdi(),
                    $objectVO->getFechar(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}

abstract class StatusPago extends BasicEnum {

    const CERRADA = "Cerrada";
    const ABIERTA = "Abierta";
    const CANCELADA = "Cancelada";

}
