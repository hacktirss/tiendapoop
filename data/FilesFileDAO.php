<?php

/**
 * Description of FilesFileDAO
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
include_once ('mysqlUtils.php');
include_once 'FilesFileVO.php';

class FilesFileDAO {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function __destruct() {
        $this->conn->close();
    }

    /**
     * 
     * @param type $idFile = Id file
     * @return \FilesFileVO
     */
    public function retrieve($idFile) {
        $filesFileVO = new FilesFileVO();
        $sql = "SELECT * FROM files_file WHERE id_file = " . $idFile;
        //error_log($sql);
        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $filesFileVO = $this->fillResulset($rs);
            return $filesFileVO;
        }
        return null;
    }

    /**
     * 
     * @param FilesFileVO $filesFileVO
     * @return new Id File
     */
    public function create($filesFileVO) {
        //$filesFileVO = new FilesFileVO();
        $id = -1;
        $sql = "INSERT INTO files_file ("
                . "cia,"
                . "filename_file,"
                . "description_file,"
                . "is_public_file,"
                . "is_folder_file,"
                . "created_at_file,"
                . "size_file,"
                . "user_id_file,"
                . "folder_id_file"
                . ") "
                . "VALUES(?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("isssssss",
                    $filesFileVO->getCia(),
                    $filesFileVO->getFilename(),
                    $filesFileVO->getDescription(),
                    $filesFileVO->getIs_public(),
                    $filesFileVO->getIs_folder(),
                    $filesFileVO->getSize(),
                    $filesFileVO->getUser_id(),
                    $filesFileVO->getFolder_id()
            );
            if ($ps->execute()) {
                $id = $ps->insert_id;
                $filesFileVO->setId($id);
            } else {
                error_log($this->conn->error);
            }
            $ps->close();
        }

        return $id;
    }

    /**
     * 
     * @param type $filesFileVO = FilesFileVO
     * @return true o false
     */
    public function update($filesFileVO) {
        //$filesFileVO = new FilesFileVO();
        $sql = "UPDATE files_file SET "
                . "filename_file = TRIM(?), "
                . "description_file = ?, "
                . "is_public_file = ?, "
                . "is_folder_file = ?, "
                . "created_at_file = ?, "
                . "size_file = ?, "
                . "user_id_file = ?, "
                . "folder_id_file = ? "
                . "WHERE id_file = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("sssssssss",
                    $filesFileVO->getFilename(),
                    $filesFileVO->getDescription(),
                    $filesFileVO->getIs_public(),
                    $filesFileVO->getIs_folder(),
                    $filesFileVO->getCreated_at(),
                    $filesFileVO->getSize(),
                    $filesFileVO->getUser_id(),
                    $filesFileVO->getFolder_id(),
                    $filesFileVO->getId()
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param type $idFile = Id File
     * @return true o false
     */
    public function remove($idFile) {
        $sql = "DELETE FROM files_file WHERE id_file = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $idFile
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @param type $idFolder = Id parent
     * @return true o false
     */
    public function removeByFolder($idFolder) {
        $sql = "DELETE FROM files_file WHERE folder_id_file = ? LIMIT 1";
        if (($ps = $this->conn->prepare($sql))) {
            $ps->bind_param("s", $idFolder
            );
            return $ps->execute();
        }
    }

    /**
     * 
     * @return array List files
     */
    public function getAll() {
        $array = array();
        $sql = "SELECT * FROM files_file WHERE 1 = 1 ";
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $filesFileVO = $this->fillResulset($rs);
                array_push($array, $filesFileVO);
            }
        }
        return $array;
    }

    /**
     * 
     * @return array List \FilesFileVO by folder parent
     */
    public function getAllByFolder($idFolder) {
        $array = array();
        $sql = "SELECT * FROM files_file WHERE 1 = 1 AND folder_id_file = '" . $idFolder . "'";
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $filesFileVO = $this->fillResulset($rs);
                array_push($array, $filesFileVO);
            }
        }
        return $array;
    }

    public function existsFolder($filesFileVO) {
        //$filesFileVO = new FilesFileVO();
        $folder_id = is_numeric($filesFileVO->getFolder_id()) ? " = " . $filesFileVO->getFolder_id() : " IS NULL" ;
        $sql = "SELECT * FROM files_file WHERE 1 = 1 AND is_folder_file = 1 "
                . "AND filename_file = TRIM('" . $filesFileVO->getFilename() . "') "
                . "AND user_id_file = " . $filesFileVO->getUser_id() . " "
                . "AND folder_id_file" . $folder_id . " ";
        if (!empty($filesFileVO->getId())) {
            $sql .= " AND id_file <> '" . $filesFileVO->getId() . "' ";
        }
        error_log($sql);
        if (($query = $this->conn->query($sql))) {
            if ($query->num_rows > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param type $rs
     * @return \FilesFileVO
     */
    public function fillResulset($rs) {
        $filesFileVO = new FilesFileVO();
        if (is_array($rs)) {
            $filesFileVO->setFilename($rs['filename_file']);
            $filesFileVO->setDescription($rs['description_file']);
            $filesFileVO->setIs_public($rs['is_public_file']);
            $filesFileVO->setIs_folder($rs['is_folder_file']);
            $filesFileVO->setCreated_at($rs['created_at_file']);
            $filesFileVO->setSize($rs['size_file']);
            $filesFileVO->setUser_id($rs['user_id_file']);
            $filesFileVO->setFolder_id($rs['folder_id_file']);
            $filesFileVO->setId($rs['id_file']);
        }
        return $filesFileVO;
    }

    public function getCurrentDate() {
        return date("Y-m-d H:i:s");
    }

}
