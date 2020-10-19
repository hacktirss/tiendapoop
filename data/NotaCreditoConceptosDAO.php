<?php

/*
 * NotaCreditoConceptosDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('mysqlUtils.php');
include_once ('FacturaConceptoVO.php');

class NotaCreditoConceptosDAO {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }

    public function __destruct() {
        $this->conn->close();
    }
    
    public function retrieveConceptos($folio) {
        $conceptos = array();
        $sql = "
                SELECT 
                   ncd.producto clave,
                   ncd.descripcion,
                   CAST(ncd.iva AS DECIMAL(10, 6)) factoriva,
                   ncd.iva,
                   CAST(ncd.ieps AS DECIMAL(10, 6)) factorieps,
                   ncd.ieps,
                   CAST(ncd.isr AS DECIMAL(10, 6)) factorisr,
                   ncd.isr, 
                   CAST(ncd.hospedaje AS DECIMAL(10, 6)) factorish,
                   ncd.hospedaje ish,
                   inv.umedida,
                   inv.inv_cunidad,
                   inv.inv_cproducto,
                   ncd.cantidad,
                   ncd.precio,
                   ROUND(ncd.cantidad * ncd.precio, 4) subtotal,
                   ROUND(ncd.importe, 4) total,
                   ROUND(ncd.cantidad * ncd.precio * ncd.iva, 4) impiva,
                   ROUND(ncd.cantidad * ncd.ieps, 4) impieps,
                   ROUND(ncd.importe * ncd.isr, 4) impisr,
                   ROUND(ncd.hospedaje * ncd.importe, 4) impish,
                   ROUND(ncd.hospedaje * ncd.importe, 4) impish,
                   ROUND(ncd.retencioniva * ncd.importe, 4) retencioniva
                FROM ncd 
                LEFT JOIN inv ON ncd.producto=inv.id
                WHERE ncd.id='".$folio."'
        ";
        error_log($sql);
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $concepto = new FacturaConceptoVO();
                $concepto->setClave($rs['clave']);
                $concepto->setDescripcion($rs['descripcion']);
                $concepto->setIva($rs['iva']);
                $concepto->setFactoriva($rs['factoriva']);
                $concepto->setIeps($rs['ieps']);
                $concepto->setFactorieps($rs['factorieps']);
                $concepto->setIsr($rs['isr']);
                $concepto->setFactorisr($rs['factorisr']);
                $concepto->setIsh($rs['ish']);
                $concepto->setFactorish($rs['factorish']);
                $concepto->setUmedida($rs['umedida']);
                $concepto->setInv_cproducto($rs['inv_cproducto']);
                $concepto->setInv_cunidad($rs['inv_cunidad']);
                $concepto->setCantidad($rs['cantidad']);
                $concepto->setTotal($rs['total']);
                $concepto->setSubtotal($rs['subtotal']);
                $concepto->setImpiva($rs['impiva']);
                $concepto->setImpieps($rs['impieps']);
                $concepto->setImpisr($rs['impisr']);
                $concepto->setImpish($rs['impish']);
                $concepto->setRetencioniva($rs['retencioniva']);
                $concepto->setPrecio($rs['precio']);
                array_push($conceptos, $concepto);
            }
        }
        error_log(mysqli_error($this->conn));
        return $conceptos;
    }//retrieveConceptos
}//NotaCreditoConceptosDAO
