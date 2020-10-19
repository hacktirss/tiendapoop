<?php
namespace com\softcoatl\utils;

class ConnectionUtils {
    
    public static function execSql($sql) {
        $object = array();
        $mysqli = \com\softcoatl\utils\IConnection::getConnection();
        try {
            if (($query = $mysqli->query($sql)) && ($rs = $query->fetch_assoc())) {
                $object = $rs;
            }
        } catch (Exception $ex) {
            error_log($ex);
        } finally {
            if ($mysqli->errno > 0) {
                error_log($mysqli->error);
                error_log($sql);
            }
            $mysqli->close();
            return $object;
        }
    }

    /**
     * Devuelve array con registros
     * @param string $sql
     * @param IConnection $connecion
     * @return array
     */
    public static function getRowsFromQuery($sql) {
        $object = array();
        $mysqli = \com\softcoatl\utils\IConnection::getConnection();
        try {
            if (($query = $mysqli->query($sql))) {
                while (($rs = $query->fetch_array())) {
                    $object[] = $rs;
                }
            }
        } catch (Exception $ex) {
            error_log($ex);
        } finally {
            if ($mysqli->errno > 0) {
                error_log($mysqli->error);
                error_log($sql);
            }
            $mysqli->close();
            return $object;
        }
    }
}

