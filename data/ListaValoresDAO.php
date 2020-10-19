<?php

include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("BasicEnum.php");
include_once ("ListaValoresVO.php");

/**
 * Description of ListaValoresDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ListaValoresDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "lista_valores";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param ListaValoresVO $objectVO
     * @return int
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "llave_lista_valor,"
                . "valor_lista_valor,"
                . "estado_lista_valor,"
                . "id_lista_lista_valor"
                . ") "
                . "VALUES(?, ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssii",
                    $objectVO->getLlave(),
                    $objectVO->getValor(),
                    $objectVO->getEstado(),
                    $objectVO->getId_lista()
            );
            if ($ps->execute()) {
                $id = $ps->insert_id;
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
     * @param array $rs
     * @return ListaValoresVO
     */
    public function fillObject($rs) {
        $objectVO = new ListaValoresVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id_lista_valor"]);
            $objectVO->setLlave($rs["llave_lista_valor"]);
            $objectVO->setValor($rs["valor_lista_valor"]);
            $objectVO->setEstado($rs["estado_lista_valor"]);
            $objectVO->setId_lista($rs["id_lista_lista_valor"]);
            $objectVO->setAlarma($rs["alarma_lista_valor"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param int $idLista
     * @param bool $activos Si TRUE solo los registros activos
     * @return array \ListaValoresVO
     */
    public function getAll($idLista, $activos = TRUE) {
        $array = array();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE 1 = 1 "
                . "AND id_lista_lista_valor = '" . $idLista . " '";
        if ($activos) {
            $sql .= " AND estado_lista_valor = '1' ";
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
     * @param string $idObjectVO
     * @param string $field
     * @return type
     */
    public function remove($idObjectVO, $field = "id_lista_valor") {
        $sql = "DELETE FROM " . self::TABLA . " WHERE $field = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $idObjectVO
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param int $idObject
     * @return ListaValoresVO
     */
    public function retrieve($idObject, $field = "id_lista_valor", $cia = 0) {
        $objectVO = new ListaValoresVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE $field = " . $idObject;
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
        }
        return $objectVO;
    }

    /**
     * 
     * @param ListaValoresVO $objectVO
     * @return boolean
     */
    public function update($objectVO) {
        //$objectVO = new ListaValoresVO();
        $sql = "UPDATE " . self::TABLA . " SET "
                . "llave_lista_valor = ?, "
                . "valor_lista_valor = ?, "
                . "estado_lista_valor = ?, "
                . "id_lista_lista_valor = ? "
                . "WHERE id_lista_valor = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssss",
                    $objectVO->getLlave(),
                    $objectVO->getValor(),
                    $objectVO->getEstado(),
                    $objectVO->getId_lista(),
                    $objectVO->getId()
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param ListaValoresVO $objectVO
     * @return boolean
     */
    public function existsKey($objectVO) {
        //$objectVO = new ListaValoresVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE 1 =1 "
                . "AND llave_lista_valor = '" . $objectVO->getLlave() . "' "
                . "AND id_lista_lista_valor = '" . $objectVO->getId_lista() . "'";
        if (is_numeric($objectVO->getId())) {
            $sql .= " AND id_lista_valor <> " . $objectVO->getId();
        }

        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            return true;
        }
        return false;
    }

}
