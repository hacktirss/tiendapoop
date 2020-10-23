<?php

/**
 * Description of ProductoDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("ProductoVO.php");

class ProductoDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "inv";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \ProductoVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id, "
                . "cia, "
                . "rfc, "
                . "descripcion, "
                . "codigo, "
                . "umedida, "
                . "precio, "
                . "menudeo, "
                . "mayoreo, "
                . "costo, "
                . "iva, "
                . "isr, "
                . "retencioniva, "
                . "ieps, "
                . "costopromedio, "
                . "observaciones, "
                . "existencia, "
                . "dlls, "
                . "grupo, "
                . "categoria, "
                . "subcategoria, "
                . "activo, "
                . "inv_cunidad, "
                . "inv_cproducto, "
                . "tipo_servicio "
                . ") "
                . "VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iissssddddiiiidsiiiiissss",
                    $objectVO->getId(),
                    $objectVO->getCia(),
                    $objectVO->getRfc(),
                    $objectVO->getDescripcion(),
                    $objectVO->getCodigo(),
                    $objectVO->getUmedida(),
                    $objectVO->getPrecio(),
                    $objectVO->getMenudeo(),
                    $objectVO->getMayoreo(),
                    $objectVO->getCosto(),
                    $objectVO->getIva(),
                    $objectVO->getIsr(),
                    $objectVO->getRetencioniva(),
                    $objectVO->getIeps(),
                    $objectVO->getCostopromedio(),
                    $objectVO->getObservaciones(),
                    $objectVO->getExistencia(),
                    $objectVO->getDlls(),
                    $objectVO->getGrupo(),
                    $objectVO->getCategoria(),
                    $objectVO->getSubcategoria(),
                    $objectVO->getActivo(),
                    $objectVO->getInv_cunidad(),
                    $objectVO->getInv_cproducto(),
                    $objectVO->getTipo_servicio()
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
     * @return \ProductoVO
     */
    public function fillObject($rs) {
        $objectVO = new ProductoVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setRfc($rs["rfc"]);
            $objectVO->setDescripcion($rs["descripcion"]);
            $objectVO->setCodigo($rs["codigo"]);
            $objectVO->setUmedida($rs["umedida"]);
            $objectVO->setPrecio($rs["precio"]);
            $objectVO->setMenudeo($rs["menudeo"]);
            $objectVO->setMayoreo($rs["mayoreo"]);
            $objectVO->setCosto($rs["costo"]);
            $objectVO->setIva($rs["iva"]);
            $objectVO->setIsr($rs["isr"]);
            $objectVO->setRetencioniva($rs["retencioniva"]);
            $objectVO->setIeps($rs["ieps"]);
            $objectVO->setCostopromedio($rs["costopromedio"]);
            $objectVO->setObservaciones($rs["observaciones"]);
            $objectVO->setExistencia($rs["existencia"]);
            $objectVO->setDlls($rs["dlls"]);
            $objectVO->setGrupo($rs["grupo"]);
            $objectVO->setCategoria($rs["categoria"]);
            $objectVO->setSubcategoria($rs["subcategoria"]);
            $objectVO->setActivo($rs["activo"]);
            $objectVO->setInv_cunidad($rs["inv_cunidad"]);
            $objectVO->setInv_cproducto($rs["inv_cproducto"]);
            $objectVO->setTipo_servicio($rs["tipo_servicio"]);
            $objectVO->setImage($rs["imagen"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return ArrayObject Arreglo de objetos \ProductoVO
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
     * @param \ProductoVO $objectVO
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
     * @return \ProductoVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new ProductoVO();
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
     * @param \ProductoVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "rfc = ?, "
                . "descripcion = ?, "
                . "codigo = ?, "
                . "umedida = ?, "
                . "precio = ?, "
                . "menudeo = ?, "
                . "mayoreo = ?, "
                . "costo = ?, "
                . "iva = ?, "
                . "costopromedio = ?, "
                . "observaciones = ?, "
                . "existencia = ?, "
                . "dlls = ?, "
                . "grupo = ?, "
                . "categoria = ?, "
                . "subcategoria = ?, "
                . "activo = ?, "
                . "inv_cunidad = ?, "
                . "inv_cproducto = ?, "
                . "tipo_servicio = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssddddssssssiissssii",
                    $objectVO->getRfc(),
                    $objectVO->getDescripcion(),
                    $objectVO->getCodigo(),
                    $objectVO->getUmedida(),
                    $objectVO->getPrecio(),
                    $objectVO->getMenudeo(),
                    $objectVO->getMayoreo(),
                    $objectVO->getCosto(),
                    $objectVO->getIva(),
                    $objectVO->getCostopromedio(),
                    $objectVO->getObservaciones(),
                    $objectVO->getExistencia(),
                    $objectVO->getDlls(),
                    $objectVO->getGrupo(),
                    $objectVO->getCategoria(),
                    $objectVO->getSubcategoria(),
                    $objectVO->getActivo(),
                    $objectVO->getInv_cunidad(),
                    $objectVO->getInv_cproducto(),
                    $objectVO->getTipo_servicio(),
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

    public function updateImage($objectVO, $content) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "imagen = ? "
                . "WHERE id = ? AND cia = ?";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("bii",
                    $null,
                    $objectVO->getId(),
                    $objectVO->getCia()
            );
            $ps->send_long_data(0, $content);
            if ($ps->execute()) {
                return true;
            }
        }
        error_log($this->conn->error);
        return false;
    }

}
