<?php

/**
 * Description of ProveedorDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("ProveedorVO.php");

class ProveedorDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "prv";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \ProveedorVO $objectVO
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
                . "estado, "
                . "alias, "
                . "telefono, "
                . "activo, "
                . "contacto, "
                . "observaciones, "
                . "tipodepago, "
                . "limite, "
                . "codigo, "
                . "rfc, "
                . "correo, "
                . "numeroint, "
                . "numeroext, "
                . "enviarcorreo, "
                . "cuentaban, "
                . "proveedorde "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iissssssssssssssssssss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getNombre(),
                    $objectVO->getDireccion(),
                    $objectVO->getColonia(),
                    $objectVO->getMunicipio(),
                    $objectVO->getEstado(),
                    $objectVO->getAlias(),
                    $objectVO->getTelefono(),
                    $objectVO->getActivo(),
                    $objectVO->getContacto(),
                    $objectVO->getObservaciones(),
                    $objectVO->getTipodepago(),
                    $objectVO->getLimite(),
                    $objectVO->getCodigo(),
                    $objectVO->getRfc(),
                    $objectVO->getCorreo(),
                    $objectVO->getNumeroint(),
                    $objectVO->getNumeroext(),
                    $objectVO->getEnviarcorreo(),
                    $objectVO->getCuentaban(),
                    $objectVO->getProveedorde()
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
     * @return \ProveedorVO
     */
    public function fillObject($rs) {
        $objectVO = new ProveedorVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setDireccion($rs["direccion"]);
            $objectVO->setColonia($rs["colonia"]);
            $objectVO->setMunicipio($rs["municipio"]);
            $objectVO->setEstado($rs["estado"]);
            $objectVO->setAlias($rs["alias"]);
            $objectVO->setTelefono($rs["telefono"]);
            $objectVO->setActivo($rs["activo"]);
            $objectVO->setContacto($rs["contacto"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setTipodepago($rs["tipodepago"]);
            $objectVO->setLimite($rs["limite"]);
            $objectVO->setCodigo($rs["codigo"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setCorreo($rs["correo"]);
            $objectVO->setNumeroint($rs["numeroint"]);
            $objectVO->setNumeroext($rs["numeroext"]);
            $objectVO->setEnviarcorreo($rs["enviarcorreo"]);
            $objectVO->setCuentaban($rs["cuentaban"]);
            $objectVO->setProveedorde($rs["proveedorde"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \ProveedorVO
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
     * @param \ProveedorVO $objectVO
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
     * @return \ProveedorVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new ProveedorVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE " . $field . " = '" . $idObjectVO . "'";
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
     * @param \ProveedorVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "direccion = ?, "
                . "colonia = ?, "
                . "municipio = ?, "
                . "estado = ?, "
                . "alias = ?, "
                . "telefono = ?, "
                . "activo = ?, "
                . "contacto = ?, "
                . "observaciones = ?, "
                . "tipodepago = ?, "
                . "limite = ?, "
                . "codigo = ?, "
                . "rfc = ?, "
                . "correo = ?, "
                . "numeroint = ?, "
                . "numeroext = ?, "
                . "enviarcorreo = ?, "
                . "cuentaban = ?, "
                . "proveedorde = ? "
                . "WHERE id = ? AND cia = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssssssssssssssssssii",
                    $objectVO->getNombre(),
                    $objectVO->getDireccion(),
                    $objectVO->getColonia(),
                    $objectVO->getMunicipio(),
                    $objectVO->getEstado(),
                    $objectVO->getAlias(),
                    $objectVO->getTelefono(),
                    $objectVO->getActivo(),
                    $objectVO->getContacto(),
                    $objectVO->getObservaciones(),
                    $objectVO->getTipodepago(),
                    $objectVO->getLimite(),
                    $objectVO->getCodigo(),
                    $objectVO->getRfc(),
                    $objectVO->getCorreo(),
                    $objectVO->getNumeroint(),
                    $objectVO->getNumeroext(),
                    $objectVO->getEnviarcorreo(),
                    $objectVO->getCuentaban(),
                    $objectVO->getProveedorde(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
