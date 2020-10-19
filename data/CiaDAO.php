<?php

/*
 * CiaDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('mysqlUtils.php');
include_once ('CiaVO.php');

class CiaDAO {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function __destruct() {
        $this->conn->close();
    }

    /**
     * Parses result set into VO
     * @param array $rs
     * @return CiaVO
     */
    private function parseRS($rs) {
        $cia = new CiaVO();
        $cia->setId($rs['id']);
        $cia->setNombre($rs['nombre']);
        $cia->setAlias($rs['alias']);
        $cia->setDireccion($rs['direccion']);
        $cia->setNumeroext($rs['numeroext']);
        $cia->setNumeroint($rs['numeroint']);
        $cia->setColonia($rs['colonia']);
        $cia->setMunicipio($rs['municipio']);
        $cia->setCiudad($rs['ciudad']);
        $cia->setEstado($rs['estado']);
        $cia->setTelefono($rs['telefono']);
        $cia->setIva($rs['iva']);
        $cia->setIsr($rs['isr']);
        $cia->setRfc($rs['rfc']);
        $cia->setCodigo($rs['codigo']);
        $cia->setSerie($rs['serie']);
        $cia->setFacturacion($rs['facturacion']);
        $cia->setClavesat($rs['clavesat']);
        $cia->setFoliofac($rs['foliofac']);
        $cia->setContacto($rs['contacto']);
        $cia->setObservaciones($rs['observaciones']);
        $cia->setTipodepago($rs['tipodepago']);
        $cia->setCorreo($rs['correo']);
        $cia->setCuentaban($rs['cuentaban']);
        $cia->setCuentaban($rs['cuentaban']);
        $cia->setFolioscom($rs['folioscom']);
        $cia->setFolioscon($rs['folioscon']);
        $cia->setRegimen($rs['regimen']);
        $cia->setVentade($rs['ventade']);
        $cia->setPassword($rs['password']);
        $cia->setRetencioniva($rs['retencioniva']);
        $cia->setFolioarrend($rs['folioarrend']);
        $cia->setFoliohonor($rs['foliohonor']);
        $cia->setIeps($rs['ieps']);
        $cia->setGasolinera($rs['gasolinera']);
        $cia->setPublicidad($rs['publicidad']);
        $cia->setDolar($rs['dolar']);
        $cia->setMaster($rs['master']);
        $cia->setEntrada($rs['entrada']);
        $cia->setClave_regimen($rs['clave_regimen']);
        $cia->setVersion_cfdi($rs['version_cfdi']);
        return $cia;
    }

    /**
     * Gets cia
     * @param String $fields Requested fields, comma separated
     * @return CiaVO
     */    
    public function retrieveFields($fields) {
        $cia = new CiaVO();
        $sql = "SELECT ".$fields." FROM cia";

        if (($query = $this->conn->query($sql)) && ($rs = $query->fetch_assoc())) {
            $cia = $this->parseRS($rs);
        }
        return $cia;
    }
}//CiaDAO
