<?php

include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("BasicEnum.php");
include_once ("ListaVO.php");
include_once ("ListaValoresDAO.php");

/**
 * Description of ListaDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ListaDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "listas";
    
    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }
    
    /**
     * 
     * @param ListaVO $objectVO
     * @return int
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "nombre_lista,"
                . "descripcion_lista,"
                . "default_lista,"
                . "tipo_dato_lista,"
                . "longitud_lista,"
                . "estado_lista,"
                . "mayus_lista,"
                . "min_lista,"
                . "max_lista"
                . ") "
                . "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssiiiii",
                    $objectVO->getNombre(),
                    $objectVO->getDescripcion(),
                    $objectVO->getDefault(),
                    $objectVO->getTipo_dato(),
                    $objectVO->getLongitud(),
                    $objectVO->getEstado(),
                    $objectVO->isMayus(),
                    $objectVO->getMin(),
                    $objectVO->getMax()
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
     * @return ListaVO
     */
    public function fillObject($rs) {
        $objectVO = new ListaVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id_lista"]);
            $objectVO->setNombre($rs["nombre_lista"]);
            $objectVO->setDescripcion($rs["descripcion_lista"]);
            $objectVO->setDefault($rs["default_lista"]);
            $objectVO->setTipo_dato($rs["tipo_dato_lista"]);
            $objectVO->setLongitud($rs["longitud_lista"]);
            $objectVO->setEstado($rs["estado_lista"]);
            $objectVO->setMayus($rs["mayus_lista"]);
            $objectVO->setMin($rs["min_lista"]);
            $objectVO->setMax($rs["max_lista"]);
        }
        return $objectVO;
    }
    
    /**
     * 
     * @param string $sql
     * @return array \ListaVO
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
     * @param int $idObjectVO
     * @param string $field
     * @return boolean
     */    
    public function remove($idObjectVO, $field = "id_lista"){
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
     * @return ListaVO
     */
    public function retrieve($idObject, $field = "id_lista", $cia = 0) {
        $objectVO = new ListaVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE $field = " . $idObject;
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
        }
        return $objectVO;
    }

    
    /**
     * 
     * @param ListaVO $objectVO
     * @return boolean
     */
    public function update($objectVO) {
        //$objectVO = new ListaVO();
        $sql = "UPDATE " . self::TABLA . " SET "
                . "nombre_lista = ?, "
                . "descripcion_lista = ?, "
                . "default_lista = ?, "
                . "tipo_dato_lista = ?, "
                . "longitud_lista = ?, "
                . "estado_lista = ?, "
                . "mayus_lista = ?, "
                . "min_lista = ?, "
                . "max_lista = ? "
                . "WHERE id_lista = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssiiiiii",
                    $objectVO->getNombre(),
                    $objectVO->getDescripcion(),
                    $objectVO->getDefault(),
                    $objectVO->getTipo_dato(),
                    $objectVO->getLongitud(),
                    $objectVO->getEstado(),
                    $objectVO->isMayus(),
                    $objectVO->getMin(),
                    $objectVO->getMax(),
                    $objectVO->getId()
            );
            return $ps->execute();
        } else {
            error_log($this->conn->error);
        }
    }

    /**
     * 
     * @param string $nombreLista
     * @return boolean
     */
    public function existsByNombre($nombreLista) {
        $sql = "SELECT * FROM " . self::TABLA . " WHERE nombre_lista = " . $nombreLista;
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            return self::RESPONSE_VALID;
        }
        return null;
    }

    /**
     * 
     * @param string $nombreLista
     * @return ListaVO
     */
    public function getByNombre($nombreLista) {
        $objectVO = new ListaVO();
        $sql = "SELECT * FROM " . self::TABLA . " WHERE nombre_lista = " . $nombreLista;
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
        }
        return $objectVO;
    }

    

    

    /**
     * Crea un array llave -> valor para llenar un combobox
     * @param type $nombreLista
     */
    public function getComboBox($nombreLista) {
        $arrayList = array();
        $sql = "SELECT * FROM " . self::TABLA . ",lista_valores WHERE id_lista = id_lista_lista_valor "
                . "AND nombre_lista = '" . $nombreLista . "' AND estado_lista_valor = 1;";
        //error_log($sql);
        if (($query = $this->conn->query($sql))) {
            while ($rs = $query->fetch_assoc()) {
                $arrayList[$rs['llave_lista_valor']] = $rs['valor_lista_valor'];
            }
        } else{
            error_log($this->conn->error);
        }
        return $arrayList;
    }

}