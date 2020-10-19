<?php

/*
 * BitacoraDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Lino Diaz Soto
 * @version 1.0
 * @since April 2019
 */

include_once ('mysqlUtils.php');
include_once ('BitacoraVO.php');

class BitacoraDAO {

    // Hold an instance of the class
    private static $instance;
    private $conn;

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new BitacoraDAO();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->conn = getConnection();
        error_log("Se Creo  la conexion BitacoraDAO");
    }

    /**
     * 
     * @param UsuarioVO $user
     * @param string $evtType
     * @param string $evtDesc
     * @param string $query_str
     * @param int $numberAlarm
     * @param string $ip
     */
    public function saveLog($user, $evtType, $evtDesc, $query_str = "", $numberAlarm = 0, $ip = null) {

        $sql = " INSERT INTO  bitacora_eventos "
                . " (cia, fecha_evento, hora_evento, usuario , tipo_evento , descripcion_evento, query_str, numero_alarma,ip_evento) "
                . " VALUES "
                . " (?, CURRENT_DATE() , CURRENT_TIME() , ? , ? , ? , ? , ?, ?) ";
        //error_log($sql);
        $ps = $this->conn->prepare($sql);

        if (($ps)) {
            $ps->bind_param("issssss", $user->getCia(), $user->getNombre(), $evtType, $evtDesc, $query_str, $numberAlarm, $ip);
            $ps->execute();
            $ps->close();
        } else {
            error_log("Error al ps: " . $this->conn->error);
        }
    }

    public function recuperaBitacora($fechaBitacora, $filters, $limitInf = 0, $tamPag = 500) {

        $bitacoraList = array();
        $sql = "SELECT * FROM bitacora_eventos WHERE fecha_evento = '" . $filters[0] . "' ";
        if ($filters[1] <> 'TODOS') {
            $sql = $sql . " AND usuario = '" . $filters[1] . "'";
        }
        if ($filters[2] <> 'TODOS') {
            $sql = $sql . " AND tipo_evento = '" . $filters[2] . "'";
        }
        $sql = $sql . " ORDER BY id_bitacora asc LIMIT " . $limitInf . ", " . $tamPag;


        //error_log($sql);

        if (($query = $this->conn->query($sql))) {

            while (($rs = $query->fetch_assoc())) {
                // error_log("Adding: ");
                $bitacoraVO = new BitacoraVO();
                $bitacoraVO->setIdBitacora($rs['id_bitacora']);
                $bitacoraVO->setFechaEvento($rs['fecha_evento']);
                $bitacoraVO->setHoraEvento($rs['hora_evento']);
                $bitacoraVO->setTipoEvento($rs['tipo_evento']);
                $bitacoraVO->setUsuario($rs['usuario']);
                $bitacoraVO->setDescripcionEvento($rs['descripcion_evento']);

                array_push($bitacoraList, $bitacoraVO);
            }
        }
        return $bitacoraList;
    }

    public function recuperaItem($idItem) {
        $bitacoraVO = null;
        $sql = "SELECT * FROM bitacora_eventos WHERE id_bitacora = " . $idItem;
        //error_log($sql);

        if (($query = $this->conn->query($sql))) {

            while (($rs = $query->fetch_assoc())) {
                $bitacoraVO = new BitacoraVO();
                $bitacoraVO->setIdBitacora($rs['id_bitacora']);
                $bitacoraVO->setFechaEvento($rs['fecha_evento']);
                $bitacoraVO->setHoraEvento($rs['hora_evento']);
                $bitacoraVO->setTipoEvento($rs['tipo_evento']);
                $bitacoraVO->setUsuario($rs['usuario']);
                $bitacoraVO->setDescripcionEvento($rs['descripcion_evento']);
            }
        }
        return $bitacoraVO;
    }

    /**
     * 
     * @param BitacoraVO $bitacoraVO
     */
    public function saveItem($bitacoraVO) {

        $sql = " INSERT INTO  bitacora_eventos "
                . " (cia, fecha_evento, hora_evento, usuario , tipo_evento , descripcion_evento ) "
                . " VALUES "
                . " (?, ? , ? , ? , ? , ? ) ";

        $ps = $this->conn->prepare($sql);

        if (($ps)) {

            $ps->bind_param("isssss"
                    , $bitacoraVO->getCia()
                    , $bitacoraVO->getFechaEvento()
                    , $bitacoraVO->getHoraEvento()
                    , $bitacoraVO->getUsuario()
                    , $bitacoraVO->getTipoEvento()
                    , $bitacoraVO->getDescripcionEvento());
            $ps->execute();
            //error_log("Se inserto eN BITACORA.....");
            $ps->close();
        }
    }

    // Destructor function called just before a UnitCounter object
    // is destroyed
    function __destruct() {
        if (isset($conn)) {
            $conn->close();
        }
    }

}
