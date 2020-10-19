<?php

include_once ('mysqlUtils.php');
include_once ('UsuarioVO.php');
include_once ('UsuarioPwdDAO.php');
include_once ('UsuarioPerfilDAO.php');
include_once ('BasicEnum.php');

/**
 * Description of UsuarioDAO
 *
 * @author Tirso Bautista Anaya
 */
class UsuarioDAO implements FunctionsDAO {

    const RESPONSE_VALID = "OK";
    const TABLA = "authuser";
    const LEVEL_MASTER = 9;
    const LEVEL_DISABLE = 99;

    private $conn;

    function __construct() {
        $this->conn = getConnection();
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param UsuarioVO $objectVO
     * @return UsuarioVO
     */
    public function create($objectVO) {
        $id = -1;
        $sql = "INSERT INTO " . self::TABLA . " ("
                . "cia, "
                . "name, "
                . "uname, "
                . "passwd, "
                . "team, "
                . "level, "
                . "status, "
                . "lastlogin, "
                . "feclave, "
                . "logincount, "
                . "mail "
                . ") "
                . "VALUES(?, ?, ?, MD5(?), ?, ?, ?, ?, ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("issssssssss",
                    $objectVO->getCia(),
                    $objectVO->getNombre(),
                    $objectVO->getUsername(),
                    $objectVO->getPassword(),
                    $objectVO->getTeam(),
                    $objectVO->getLevel(),
                    $objectVO->getStatus(),
                    $objectVO->getLastlogin(),
                    $objectVO->getCreation(),
                    $objectVO->getCount(),
                    $objectVO->getMail()
            );
            if ($ps->execute()) {
                $id = $ps->insert_id;
                $ps->close();
            } else {
                error_log($this->conn->error);
                $ps->close();
            }
        }

        $objectVO->setId($id);
        if ($id > 0) {
            $usuarioPwdDAO = new UsuarioPwdDAO();
            $usuarioPwdVO = new UsuarioPwdVO();
            $usuarioPwdVO->setIdUsuario($id);
            $usuarioPwdVO->setPassword($objectVO->getPassword());
            $usuarioPwdDAO->create($usuarioPwdVO);
        }


        return $id;
    }

    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function changePassword($objectVO) {
        $sql = "UPDATE authuser "
                . "SET passwd = MD5(?),"
                . "feclave = ADDDATE(CURDATE(), INTERVAL " . Usuarios::VALIDITY . " MONTH),"
                . "locked = 0,"
                . "logincount = IF(logincount = 0, 1, logincount) "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ss", $objectVO->getPassword(), $objectVO->getId()
            );
            if ($ps->execute()) {
                error_log("Change password [ " + $objectVO->getId() + " ]");
                $usuarioPwdDAO = new UsuarioPwdDAO();
                $usuarioPwdVO = new UsuarioPwdVO();
                $usuarioPwdVO->setIdUsuario($objectVO->getId());
                $usuarioPwdVO->setPassword($objectVO->getPassword());
                $usuarioPwdVO->setCreation($this->getCurrentDate());
                if ($usuarioPwdDAO->update($objectVO->getId())) {
                    if (($id = $usuarioPwdDAO->create($usuarioPwdVO)) > 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 
     * @param array $rs
     * @return \UsuarioVO
     */
    public function fillObject($rs) {
        $objectVO = new UsuarioVO();
        if (is_array($rs)) {
            $objectVO->setId($rs["id"]);
            $objectVO->setCia($rs["cia"]);
            $objectVO->setNombre($rs["name"]);
            $objectVO->setUsername($rs["uname"]);
            $objectVO->setPassword($rs["passwd"]);
            $objectVO->setTeam($rs["team"]);
            $objectVO->setLevel($rs["level"]);
            $objectVO->setStatus($rs["status"]);
            $objectVO->setLastlogin($rs["lastlogin"]);
            $objectVO->setLastactivity($rs["lastactivity"]);
            $objectVO->setCount($rs["logincount"]);
            $objectVO->setCreation($rs["feclave"]);
            $objectVO->setLocked($rs["locked"]);
            $objectVO->setAlive($rs["alive"]);
            $objectVO->setMail($rs["mail"]);
        }
        return $objectVO;
    }

    /**
     * 
     * @param string $username
     * @param int $idObjectVO
     * @return \UsuarioVO
     */
    public function findByUname($username, $idObjectVO = 0) {
        $sql = "SELECT " . self::TABLA . ".* FROM " . self::TABLA . " "
                . "WHERE uname = '" . $username . "' "
                . "AND status = '" . StatusUsuario::ACTIVO . "' ";
                //. "AND level <= " . self::LEVEL_MASTER;
        if ($idObjectVO > 0) {
            $sql .= " AND id != " . $idObjectVO;
        }
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
            //error_log($objectVO);
            return $objectVO;
        }
        return null;
    }

    /**
     * 
     * @param string $uname
     * @param string $password
     * @return UsuarioVO
     */
    public function finfByUnameAndPassword($uname, $password) {
        $objectVO = new UsuarioVO;
        $sql = "SELECT " . self::TABLA . ".* FROM " . self::TABLA . " "
                . "WHERE uname = '" . $uname . "' "
                . "AND passwd = MD5('" . $password . "') "
                . "AND status = '" . StatusUsuario::ACTIVO . "' ";
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
            return $objectVO;
        }
        return null;
    }

    /**
     * 
     * @param string $sql
     * @return array List users active
     */
    public function getAll($sql) {
        $array = array();
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $objectVO = $this->fillObject($rs);
                array_push($array, $objectVO);
            }
        }
        return $array;
    }

    public function getCurrentDate() {
        return date("Y-m-d H:i:s");
    }

    /**
     * 
     * @param int $idUsuario
     * @param string $field
     * @return boolean
     */
    public function remove($idUsuario, $field = "id") {
        $usuarioPwdDAO = new UsuarioPwdDAO();
        $usuarioPerfilDAO = new UsuarioPerfilDAO();
        if ($usuarioPerfilDAO->remove($idUsuario) && $usuarioPwdDAO->remove($idUsuario)) {
            $sql = "DELETE FROM authuser WHERE $field = ? ";
            if (($ps = $this->conn->prepare($sql))) {
                $ps->bind_param("s", $idUsuario
                );
                return $ps->execute();
            }
        }
    }

    /**
     * 
     * @param int $idObjectVO
     * @return UsuarioVO
     */
    public function retrieve($idObjectVO, $field = "id", $cia = 0) {
        $objectVO = new UsuarioVO;
        $sql = "SELECT " . self::TABLA . ".* FROM " . self::TABLA . " "
                . "WHERE " . self::TABLA . "." . $field . " = " . $idObjectVO;
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $objectVO = $this->fillObject($rs);
            //error_log($objectVO);
            return $objectVO;
        }
        return null;
    }

    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function update($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "name = ?, "
                . "uname = ?, "
                . "team = ?, "
                . "mail = ?, "
                . "locked = ?, "
                . "alive = ?, "
                . "level = ?, "
                . "status = ? "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("ssssiiisi",
                    $objectVO->getNombre(),
                    $objectVO->getUsername(),
                    $objectVO->getTeam(),
                    $objectVO->getMail(),
                    $objectVO->getLocked(),
                    $objectVO->getAlive(),
                    $objectVO->getLevel(),
                    $objectVO->getStatus(),
                    $objectVO->getId()
            );
            if ($ps->execute()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function updateLastLogin($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "lastlogin = NOW(), "
                . "logincount = logincount + 1, "
                . "locked = 0, "
                . "alive = 1 "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("i", $objectVO->getId());
            return $ps->execute();
        }
    }

    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function updateLocked($objectVO) {
        $sql = "UPDATE " . self::TABLA . " SET "
                . "locked = locked + 1 "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $objectVO->getId());
            return $ps->execute();
        }
    }
    
    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function freeAlive($objectVO){
        $sql = "UPDATE " . self::TABLA . " SET "
                . "alive = 0 "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("i", $objectVO->getId());
            return $ps->execute();
        }
    }
    
    /**
     * 
     * @param UsuarioVO $objectVO
     * @return boolean
     */
    public function updateLastActivity($objectVO){
        $sql = "UPDATE " . self::TABLA . " SET "
                . "lastactivity = NOW() "
                . "WHERE id = ? ";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("i", $objectVO->getId());
            return $ps->execute();
        }
    }

}

abstract class StatusUsuario extends BasicEnum {

    const ACTIVO = "active";
    const INACTIVO = "inactive";

}

abstract class StatusSesion extends BasicEnum {

    const ALIVE = 1;
    const DEAD = 0;

}
