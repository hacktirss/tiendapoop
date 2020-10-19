<?php

/**
 * Description of QrysDAO
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
include_once ("QrysVO.php");

class QrysDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "qrys";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param \QrysVO $objectVO
     * @return int Nuevo identificador generado
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "nombre, "
                . "campos, "
                . "froms, "
                . "edi, "
                . "tampag, "
                . "ayuda, "
                . "joins"
                . ") "
                . "VALUES(?,?,?,?,?,?,?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssss",
                    $objectVO->getNombre(),
                    $objectVO->getCampos(),
                    $objectVO->getFroms(),
                    $objectVO->getEdi(),
                    $objectVO->getTampag(),
                    $objectVO->getAyuda(),
                    $objectVO->getJoins()
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
     * @return \QrysVO
     */
    public function fillObject($rs) {
        $objectVO = new QrysVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setNombre($rs["nombre"]);
            $objectVO->setCampos($rs["campos"]);
            $objectVO->setFroms($rs["froms"]);
            $objectVO->setEdi($rs["edi"]);
            $objectVO->setTampag($rs["tampag"]);
            $objectVO->setAyuda($rs["ayuda"]);
            $objectVO->setJoins($rs["joins"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \QrysVO
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
     * @return \QrysVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new QrysVO();
        $sql = "SELECT " . self::TABLA . ".*  FROM " . self::TABLA . "                
                WHERE 1 = 1 AND " . self::TABLA . "." . $field . " = '" . $idObjectVO . "'";
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
     * @param \QrysVO $objectVO
     * @return boolean Si la operación fue exitosa devolvera TRUE
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre = ?, "
                . "campos = ?, "
                . "froms = ?, "
                . "edi = ?, "
                . "tampag = ?, "
                . "ayuda = ?, "
                . "joins = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssi",
                    $objectVO->getNombre(),
                    $objectVO->getCampos(),
                    $objectVO->getFroms(),
                    $objectVO->getEdi(),
                    $objectVO->getTampag(),
                    $objectVO->getAyuda(),
                    $objectVO->getJoins(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
        error_log($this->conn->error);
        return false;
    }

}
