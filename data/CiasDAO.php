<?php

/**
 * Description of CiasDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("CiasVO.php");

class CiasDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "cias";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \CiasVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "nombre, "
                . "rfc, "
                . "password, "
                . "alias, "
                . "direccion, "
                . "numeroext, "
                . "numeroint, "
                . "colonia, "
                . "municipio, "
                . "estado, "
                . "codigo, "
                . "telefono, "
                . "contacto, "
                . "correo, "
                . "observaciones, "
                . "facturacion, "
                . "regimen, "
                . "serie, "
                . "clavesat, "
                . "iva, "
                . "isr, "
                . "retencioninva, "
                . "ieps,"
                . "master "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssssssssssssssssss",
                    $objectVO->getNombre(),
                    $objectVO->getRfc(),
                    $objectVO->getPassword(),
                    $objectVO->getAlias(),
                    $objectVO->getDireccion(),
                    $objectVO->getNumeroext(),
                    $objectVO->getNumeroint(),
                    $objectVO->getColonia(),
                    $objectVO->getMunicipio(),
                    $objectVO->getEstado(),
                    $objectVO->getCodigo(),
                    $objectVO->getTelefono(),
                    $objectVO->getContacto(),
                    $objectVO->getCorreo(),
                    $objectVO->getObservaciones(),
                    $objectVO->getFacturacion(),
                    $objectVO->getRegimen(),
                    $objectVO->getSerie(),
                    $objectVO->getClavesat(),
                    $objectVO->getIva(),
                    $objectVO->getIsr(),
                    $objectVO->getRetencioninva(),
                    $objectVO->getIeps(),
                    $objectVO->getMaster()
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
     * @return \CiasVO
     */
    public function fillObject($rs) {
        $objectVO = new CiasVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setPassword($rs["password"]);
            $objectVO->setAlias($rs["alias"]);
            $objectVO->setDireccion($rs["direccion"]);
            $objectVO->setNumeroext($rs["numeroext"]);
            $objectVO->setNumeroint($rs["numeroint"]);
            $objectVO->setColonia($rs["colonia"]);
            $objectVO->setMunicipio($rs["municipio"]);
            $objectVO->setEstado($rs["estado"]);
            $objectVO->setCodigo($rs["codigo"]);
            $objectVO->setTelefono($rs["telefono"]);
            $objectVO->setContacto($rs["contacto"]);
            $objectVO->setCorreo($rs["correo"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setFacturacion($rs["facturacion"]);
            $objectVO->setRegimen($rs["regimen"]);
            $objectVO->setSerie($rs["serie"]);
            $objectVO->setClavesat($rs["clavesat"]);
            $objectVO->setIva($rs["iva"]);
            $objectVO->setIsr($rs["isr"]);
            $objectVO->setRetencioninva($rs["retencioninva"]);
            $objectVO->setIeps($rs["ieps"]);
            $objectVO->setMaster($rs["master"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \CiasVO
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
     * @return \CiasVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new CiasVO();
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
     * @param \CiasVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "rfc = ?, "
                . "password = ?, "
                . "alias = ?, "
                . "direccion = ?, "
                . "numeroext = ?, "
                . "numeroint = ?, "
                . "colonia = ?, "
                . "municipio = ?, "
                . "estado = ?, "
                . "codigo = ?, "
                . "telefono = ?, "
                . "contacto = ?, "
                . "correo = ?, "
                . "observaciones = ?, "
                . "facturacion = ?, "
                . "regimen = ?, "
                . "serie = ?, "
                . "clavesat = ?, "
                . "iva = ?, "
                . "isr = ?, "
                . "retencioninva = ?, "
                . "ieps = ?,"
                . "master = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssssssssssssssssssi",
                    $objectVO->getNombre(),
                    $objectVO->getRfc(),
                    $objectVO->getPassword(),
                    $objectVO->getAlias(),
                    $objectVO->getDireccion(),
                    $objectVO->getNumeroext(),
                    $objectVO->getNumeroint(),
                    $objectVO->getColonia(),
                    $objectVO->getMunicipio(),
                    $objectVO->getEstado(),
                    $objectVO->getCodigo(),
                    $objectVO->getTelefono(),
                    $objectVO->getContacto(),
                    $objectVO->getCorreo(),
                    $objectVO->getObservaciones(),
                    $objectVO->getFacturacion(),
                    $objectVO->getRegimen(),
                    $objectVO->getSerie(),
                    $objectVO->getClavesat(),
                    $objectVO->getIva(),
                    $objectVO->getIsr(),
                    $objectVO->getRetencioninva(),
                    $objectVO->getIeps(),
                    $objectVO->getMaster(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
