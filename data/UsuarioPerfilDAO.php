<?php

include_once ("mysqlUtils.php");
include_once ("FunctionsDAO.php");
include_once ("UsuarioPerfilVO.php");
include_once ("UsuarioVO.php");

/**
 * Description of UsuarioPerfilDAO
 *
 * @author 3PX89LA_RS5
 */
class UsuarioPerfilDAO implements FunctionsDAO {

    const PERFIL_ADMIN = "Administrador";
    const PERFIL_DEFAULT = "Operador";
    const TABLA = "authuser_conf";

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * Insert new config 
     * @param UsuarioPerfilVO $usuarioPerfilVO
     * @return bool
     */
    public function create($usuarioPerfilVO) {
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "id_user, "
                . "id_menu, "
                . "permisos,"
                . "editable "
                . ") "
                . "VALUES(" . $usuarioPerfilVO->getIdUsuario() . ", " . $usuarioPerfilVO->getIdMenu() . ", '" . $usuarioPerfilVO->getPermisos() . "', '" . $usuarioPerfilVO->getEditable() . "') "
                . "ON DUPLICATE KEY UPDATE "
                . "permisos = '" . $usuarioPerfilVO->getPermisos() . "',"
                . "editable = '" . $usuarioPerfilVO->getEditable() . "' ";

        if (($ps = $this->conn->prepare($sql))) {
            if($ps->execute()){
                $ps->close();
                return true;
            }
            error_log(mysqli_error($this->conn));
            $ps->close();
        }
        return false;
    }

    /**
     * 
     * @param array() $rs
     * @return \UsuarioPerfilVO
     */
    public function fillObject($rs) {
        $objectVO = new UsuarioPerfilVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setIdUsuario($rs["id_user"]);
            $objectVO->setIdMenu($rs["id_menu"]);
            $objectVO->setPermisos($rs["permisos"]);
            $objectVO->setEditable($rs["editable"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $sql Consulta SQL
     * @return array Arreglo de objetos \UsuarioPerfilVO
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
     * @return \UsuarioPerfilVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new UsuarioPerfilVO();
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
     * @param UsuarioPerfilVO $usuarioPerfilVO
     * @return boolean
     */
    public function update($usuarioPerfilVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "id_user = ?, "
                . "id_menu = ?, "
                . "permisos = ?,"
                . "editable "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("iissi",
                    $usuarioPerfilVO->getIdUsuario(),
                    $usuarioPerfilVO->getIdMenu(),
                    $usuarioPerfilVO->getPermisos(),
                    $usuarioPerfilVO->getEditable(),
                    $usuarioPerfilVO->getId()
            );
            return $ps->execute();
        }
    }

}
