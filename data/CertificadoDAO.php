<?php

/**
 * Description of CertificadoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("CertificadoVO.php");

class CertificadoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "cias_cert";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \CertificadoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia,"
                . "rfc, "
                . "clave, "
                . "regimen, "
                . "certificado, "
                . "llave, "
                . "certificado_pfx, "
                . "habilitado "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssbbbi",
                    $objectVO->getCia(),
                    $objectVO->getRfc(),
                    $objectVO->getClave(),
                    $objectVO->getRegimen(),
                    $objectVO->getCertificado(),
                    $objectVO->getLlave(),
                    $objectVO->getCertificado_pfx(),
                    $objectVO->getHabilitado()
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
     * @return \CertificadoVO
     */
    public function fillObject($rs) {
        $objectVO = new CertificadoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setClave($rs["clave"]);
            $objectVO->setRegimen($rs["regimen"]);
            $objectVO->setCertificado($rs["certificado"]);
            $objectVO->setLlave($rs["llave"]);
            $objectVO->setCertificado_pfx($rs["certificado_pfx"]);
            $objectVO->setLogo($rs["logo"]);
            $objectVO->setHabilitado($rs["habilitado"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \CertificadoVO
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
     * @param \CertificadoVO $objectVO
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
     * @return \CertificadoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new CertificadoVO();
        $sql = "SELECT id, cia, rfc, clave, regimen, habilitado, CONVERT(certificado USING 'UTF8') certificado,
                CONVERT(llave USING 'UTF8') llave, certificado_pfx, logo
                FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "' AND habilitado = 1";
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
     * @param \CertificadoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "rfc = ?,"
                . "clave = ?, "
                . "regimen = ?, "
                . "certificado = ?, "
                . "llave = ?, "
                . "certificado_pfx = ?, "
                . "habilitado = ?, "
                . "WHERE id = ? AND cia = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssbbbiii",
                    $objectVO->getRfc(),
                    $objectVO->getClave(),
                    $objectVO->getRegimen(),
                    $objectVO->getCertificado(),
                    $objectVO->getLlave(),
                    $objectVO->getCertificado_pfx(),
                    $objectVO->getHabilitado(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
