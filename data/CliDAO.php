<?php

/**
 * Description of CliDAO
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
include_once ("CliVO.php");

use com\softcoatl\utils\BaseDAO;

class CliDAO extends BaseDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "cli";

    /**
     * 
     * @param \CliVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "nombre, "
                . "direccion, "
                . "colonia, "
                . "municipio, "
                . "alias, "
                . "telefono, "
                . "activo, "
                . "contacto, "
                . "observaciones, "
                . "rfc, "
                . "codigo, "
                . "correo, "
                . "numeroext, "
                . "numeroint, "
                . "enviarcorreo, "
                . "cuentaban, "
                . "estado, "
                . "poliza, "
                . "status"
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iisssssssssssssssssss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getNombre(),
                    $objectVO->getDireccion(),
                    $objectVO->getColonia(),
                    $objectVO->getMunicipio(),
                    $objectVO->getAlias(),
                    $objectVO->getTelefono(),
                    $objectVO->getActivo(),
                    $objectVO->getContacto(),
                    $objectVO->getObservaciones(),
                    $objectVO->getRfc(),
                    $objectVO->getCodigo(),
                    $objectVO->getCorreo(),
                    $objectVO->getNumeroext(),
                    $objectVO->getNumeroint(),
                    $objectVO->getEnviarcorreo(),
                    $objectVO->getCuentaban(),
                    $objectVO->getEstado(),
                    $objectVO->getPoliza(),
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
     * @return \CliVO
     */
    public function fillObject($rs) {
        $objectVO = new CliVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setDireccion($rs["direccion"]);
            $objectVO->setColonia($rs["colonia"]);
            $objectVO->setMunicipio($rs["municipio"]);
            $objectVO->setAlias($rs["alias"]);
            $objectVO->setTelefono($rs["telefono"]);
            $objectVO->setActivo($rs["activo"]);
            $objectVO->setContacto($rs["contacto"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setCodigo($rs["codigo"]);
            $objectVO->setCorreo($rs["correo"]);
            $objectVO->setNumeroext($rs["numeroext"]);
            $objectVO->setNumeroint($rs["numeroint"]);
            $objectVO->setEnviarcorreo($rs["enviarcorreo"]);
            $objectVO->setCuentaban($rs["cuentaban"]);
            $objectVO->setEstado($rs["estado"]);
            $objectVO->setFormadepago($rs["formadepago"]);
            $objectVO->setPoliza($rs["poliza"]);
            $objectVO->setStatus($rs["status"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \CliVO
     */
    public function getAll($sql) {
        $array = array();
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $array[] = CliVO::parse($rs);
            }
        } else {
            error_log($this->conn->error);
        }
        return $array;
    }

    /**
     * 
     * @param \CliVO $objectVO
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
     * @return \CliVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new CliVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "'";
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
     * @param \CliVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "direccion = ?, "
                . "colonia = ?, "
                . "municipio = ?, "
                . "alias = ?, "
                . "telefono = ?, "
                . "activo = ?, "
                . "contacto = ?, "
                . "observaciones = ?, "
                . "rfc = ?, "
                . "codigo = ?, "
                . "correo = ?, "
                . "numeroext = ?, "
                . "numeroint = ?, "
                . "enviarcorreo = ?, "
                . "cuentaban = ?, "
                . "estado = ?, "
                . "poliza = ?, "
                . "status = ? "
                . "WHERE id = ? ANd cia = ?";
        if (($ps = $this->conn->prepare($sql)) && $ps->bind_param("sssssssssssssssssssii",
                        $objectVO->getNombre(),
                        $objectVO->getDireccion(),
                        $objectVO->getColonia(),
                        $objectVO->getMunicipio(),
                        $objectVO->getAlias(),
                        $objectVO->getTelefono(),
                        $objectVO->getActivo(),
                        $objectVO->getContacto(),
                        $objectVO->getObservaciones(),
                        $objectVO->getRfc(),
                        $objectVO->getCodigo(),
                        $objectVO->getCorreo(),
                        $objectVO->getNumeroext(),
                        $objectVO->getNumeroint(),
                        $objectVO->getEnviarcorreo(),
                        $objectVO->getCuentaban(),
                        $objectVO->getEstado(),
                        $objectVO->getPoliza(),
                        $objectVO->getStatus(),
                        $objectVO->getId(),
                        $objectVO->getCia()
                )
        ) {
            $updated = $ps->execute();
            return $updated;
        }
        error_log($this->conn->error);
        return false;
    }

}
