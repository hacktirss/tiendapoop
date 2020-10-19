<?php

/*
 * RelacionesCfdiDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('mysqlUtils.php');
include_once ('RelacionesCfdiVO.php');

class RelacionesCfdiDAO {

    private $conn;
    
    public function __construct() {
        $this->conn=getConnection();
    }

    
    public function __destruct() {
        $this->conn->close();
    }

    /*
     * @return RelacionesVO
     */
    public function retrieveByUUID($uuid) {
        $relaciones = array();
        $sql = "SELECT * FROM relacion_cfdi WHERE uuid = ?";
        error_log($sql);
        if (($ps = $this->conn->prepare($sql))
                && $ps->bind_param("s", $uuid)
                && $ps->execute() 
                && ($rs = $ps->get_result()->fetch_array())) {
            $relacion = new RelacionesCfdiVO();
            $relacion->setId($rs['id']);
            $relacion->setOrigen($rs['serie']);
            $relacion->setUuid($rs['fecha']);
            $relacion->setTipo_relacion($rs['cliente']);
            $relacion->setUuid_relacionado($rs['cantidad']);
            $relaciones[] = $relacion;
        }
        return $relaciones;
    }

    /*
     * @return RelacionesVO
     */
    public function retrieve($id, $origen) {
        $relaciones = array();
        $sql = "SELECT * FROM relacion_cfdi WHERE id = " . $id . " AND origen = " . $origen;
        error_log($sql);
        if (($rs = $this->conn->query($sql))) {
            while ($row = $rs->fetch_array()) {
                $relacion = new RelacionesCfdiVO();
                $relacion->setId($row['id']);
                $relacion->setOrigen($row['origen']);
                $relacion->setUuid($row['uuid']);
                $relacion->setTipo_relacion($row['tipo_relacion']);
                $relacion->setUuid_relacionado($row['uuid_relacionado']);
                $relaciones[] = $relacion;
            }
        }
        error_log($this->conn->error);
        return $relaciones;
    }
}
