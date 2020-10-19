<?php

/*
 * FacturaConceptosDAO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('mysqlUtils.php');
include_once ('FacturaConceptoVO.php');

class FacturaConceptosDAO {
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
                   fcd.producto clave,
                   fcd.descripcion,
                   fcd.cantidad,
                   round( fcd.precio, 4 ) precio,
                   fcd.iva,
                   fcd.ieps,
                   fcd.isr, 
                   fcd.hospedaje ish,
                   inv.umedida,
                   CAST( fcd.iva AS DECIMAL( 10, 6 ) ) factoriva,
                   CAST( fcd.ieps AS DECIMAL( 10, 6 ) ) factorieps,
                   CAST( fcd.isr AS DECIMAL( 10, 6 ) ) factorisr,
                   CAST( fcd.hospedaje AS DECIMAL( 10, 6 ) ) factorish,
                   inv.inv_cunidad,
                   inv.inv_cproducto,
                   round( fcd.cantidad * fcd.precio, 4 ) subtotal,
                   round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ), 4 ) base,
                   round( fcd.cantidad * ( fcd.precio + fcd.ieps ) * IFNULL(fcd.descuento, 0)/100, 4 ) descuento,
                   round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.iva, 4 ) impiva,
                   round( fcd.cantidad * fcd.ieps, 4 ) impieps,
                   round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.isr, 4 ) impisr,
                   round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.hospedaje, 4 ) impish,
                   round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.retencioniva, 4 ) riva,
                   round( fcd.importe, 4 ) total
                FROM fcd 
                LEFT JOIN inv ON fcd.producto=inv.id
                WHERE fcd.id='".$folio."'
        ";
        error_log($sql);
        error_log("Concepto Nuevo ");
        if (($query = $this->conn->query($sql))) {
            while (($rs = $query->fetch_assoc())) {
                $concepto = new FacturaConceptoVO();
                $concepto->setClave($rs['clave']);
                $concepto->setDescripcion($rs['descripcion']);
                $concepto->setCantidad($rs['cantidad']);
                $concepto->setPrecio($rs['precio']);
                $concepto->setIva($rs['iva']);
                $concepto->setIeps($rs['ieps']);
                $concepto->setIsr($rs['isr']);
                $concepto->setIsh($rs['ish']);
                $concepto->setUmedida($rs['umedida']);
                $concepto->setFactoriva($rs['factoriva']);
                $concepto->setFactorieps($rs['factorieps']);
                $concepto->setFactorisr($rs['factorisr']);
                $concepto->setFactorish($rs['factorish']);
                $concepto->setInv_cproducto($rs['inv_cproducto']);
                $concepto->setInv_cunidad($rs['inv_cunidad']);
                $concepto->setSubtotal($rs['subtotal']);
                $concepto->setBase($rs['base']);
                $concepto->setDescuento($rs['descuento']);
                $concepto->setImpiva($rs['impiva']);
                $concepto->setImpieps($rs['impieps']);
                $concepto->setImpisr($rs['impisr']);
                $concepto->setImpish($rs['impish']);
                $concepto->setRetencioniva($rs['riva']);
                $concepto->setTotal($rs['total']);
                error_log("Concepto Nuevo ");
                error_log($concepto);
                array_push($conceptos, $concepto);
            }
        }
        error_log(mysqli_error($this->conn));
        return $conceptos;
    }//retrieveConceptos
}//FacturaConceptosDAO
