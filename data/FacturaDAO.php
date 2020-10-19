<?php

/*
 * FacturaDAO Objeto DAO.
 * Recupera la información referente a la factura con fc.id = $folio
 * Crea un objeto de tipo Comprobante y los nodos requeridos.
 * La información vaciada en Comprobante se encuentra contenida en las tablas cia, cli, fc, fcd.
 * Este módulo está escrito de acuerdo a la estructura de base de datos, reglas y definiciones del sistema Detisa®, sistema administrativo interno,
 * y cumple con las especificaciones definidas por la autoridad tributaria SAT.
 * 
 * Detisa®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since ene 2018
 */

namespace com\detisa\detisa;

require_once ('mysqlUtils.php');
require_once ('cfdi33/Comprobante.php');
require_once ('cfdi33/addenda/Observaciones.php');
require_once ('pdf/PDFTransformer.php');

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;

class FacturaDAO {

    private $folio;
    private $cia;
    /* @var $comprobante \cfdi33\Comprobante */
    private $comprobante;
    /* @var $mysqlConnection \mysqli */
    private $mysqlConnection;

    function __construct($folio, $cia) {

        error_log("Cargando CFDI con folio " . $folio);

        $this->folio = $folio;
        $this->cia = $cia;
        $this->comprobante = new Comprobante();
        $this->mysqlConnection = getConnection();

        $this->comprobante();
        $this->emisor();
        $this->receptor();
        $this->cfdiRelacionados();
        $this->conceptos();
        $this->impuestos();
        $this->observaciones();
    }

//constructor

    public function __destruct() {
        $this->mysqlConnection->close();
    }

    function getComprobante() {
        return $this->comprobante;
    }

    function getFolio() {
        return $this->folio;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    /**
     * Recupera la información relativa a la factura.
     * Crea el objeto Comprobante
     */
    private function comprobante() {

        $sql = "SELECT 
                    fc.folio Folio, 
                    DATE_FORMAT(fc.fecha, '%Y-%m-%dT%H:%i:%s') Fecha, 
                    fc.serie Serie, 
                    fc.formadepago FormaPago, 
                    fc.metododepago MetodoPago, 
                    fc.total Total,
                    fc.importe SubTotal,
                    cias.codigo LugarExpedicion
                FROM fc 
                LEFT JOIN cias ON cias.id = " . $this->cia . "
                WHERE fc.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $this->comprobante->setFolio($rs['Folio']);
            $this->comprobante->setSerie($rs['Serie']);
            $this->comprobante->setFecha($rs['Fecha']);
            $this->comprobante->setTipoDeComprobante("I");
            $this->comprobante->setVersion("3.3");
            $this->comprobante->setFormaPago($rs['FormaPago']);
            $this->comprobante->setMetodoPago($rs['MetodoPago']);
            $this->comprobante->setMoneda("MXN");
            $this->comprobante->setTipoCambio(1);
            $this->comprobante->setTotal(number_format($rs['Total'], 2, '.', ''));
            $this->comprobante->setSubTotal(number_format($rs['SubTotal'], 2, '.', ''));
            $this->comprobante->setLugarExpedicion($rs['LugarExpedicion']);
        }//if
    }

//comprobante

    /**
     * Recupera los datos de Detisa.
     * Crea el nodo Emisor.
     */
    private function emisor() {

        /* @var $emisor Comprobante\Emisor */
        $emisor = new Comprobante\Emisor();
        $sql = "SELECT nombre Nombre, rfc Rfc, regimen RegimenFiscal FROM cias WHERE id = " . $this->cia;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $emisor->setNombre($rs['Nombre']);
            $emisor->setRfc($rs['Rfc']);
            $emisor->setRegimenFiscal($rs['RegimenFiscal']);
        }
        $this->comprobante->setEmisor($emisor);
    }

//emisor

    /**
     * Recupera los datos del receptor del CFDI.
     * Crea el nodo Receptor.
     */
    private function receptor() {

        /* @var $receptor Comprobante\Receptor */
        $receptor = new Comprobante\Receptor();
        $sql = "SELECT cli.nombre Nombre, cli.rfc Rfc, fc.usocfdi UsoCFDI 
                FROM fc 
                LEFT JOIN cli ON fc.cliente = cli.id AND cli.cia = " . $this->cia . " 
                WHERE fc.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $receptor->setNombre($rs['Nombre']);
            $receptor->setRfc($rs['Rfc']);
            $receptor->setUsoCFDI($rs['UsoCFDI']);
        }
        $this->comprobante->setReceptor($receptor);
    }

//receptor

    /**
     * Recupera el CFDI relacionado. Por definición de Detisa, solo se soporta un CFDI relacionado.
     * Crea el nodo CfdiRelacionados si es necesario.
     */
    private function cfdiRelacionados() {

        $cfdiRelacionados = new Comprobante\CfdiRelacionados();

        $sql = "SELECT * FROM relacion_cfdi WHERE id = " . $this->folio . " AND origen = 1";
        if (($query = $this->mysqlConnection->query($sql))) {
            while ($rs = $query->fetch_assoc()) {

                $cfdiRelacionado = new Comprobante\CfdiRelacionados\CfdiRelacionado();
                $cfdiRelacionado->setUUID($rs['uuid_relacionado']);
                $cfdiRelacionados->addCfdiRelacionado($cfdiRelacionado);

                $cfdiRelacionados->setTipoRelacion($rs['tipo_relacion']);
            }
        }

        if (count($cfdiRelacionados->getCfdiRelacionado()) > 0) {
            $this->comprobante->setCfdiRelacionados($cfdiRelacionados);
        }
    }

//cfdiRelacionados

    /**
     * Recupera los conceptos asociados a la factura.
     * Crea el nodo Conceptos, el arreglo de nodos Concepto y los nodos de Impuesto asociados a cada Concepto.
     */
    private function conceptos() {

        $conceptos = new Comprobante\Conceptos();

        $sql = "
            SELECT 
               fcd.producto NoIdentificacion,
               fcd.descripcion Descripcion,
               fcd.cantidad Cantidad,
               round( fcd.precio, 4 ) ValorUnitario,
               CAST( fcd.iva AS DECIMAL( 10, 6 ) ) factoriva,
               CAST( fcd.ieps AS DECIMAL( 10, 6 ) ) factorieps,
               CAST( fcd.isr AS DECIMAL( 10, 6 ) ) factorisr,
               inv.inv_cunidad ClaveUnidad,
               inv.inv_cproducto ClaveProdServ,
               round( fcd.cantidad * fcd.precio, 4 ) Importe,
               round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ), 4 ) Base,
               round( fcd.cantidad * ( fcd.precio + fcd.ieps ) * IFNULL(fcd.descuento, 0)/100, 4 ) Descuento,
               round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.iva, 4 ) tax_iva,
               round( fcd.cantidad * fcd.ieps, 4 ) tax_ieps,
               round( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.isr, 4 ) tax_isr,
               round( fcd.importe, 4 ) total
            FROM fcd 
            LEFT JOIN inv ON fcd.producto = inv.id AND inv.cia = " . $this->cia . "
            WHERE fcd.id = " . $this->folio;

        $cTotal = 0.0;
        $cDescuento = 0.0;

        if (($query = $this->mysqlConnection->query($sql))) {
            while (($rs = $query->fetch_assoc())) {

                $concepto = new Comprobante\Conceptos\Concepto();
                $concepto->setClaveProdServ($rs['ClaveProdServ']);
                $concepto->setClaveUnidad($rs['ClaveUnidad']);
                $concepto->setDescripcion($rs['Descripcion']);
                $concepto->setImporte(number_format($rs['Importe'], 2, '.', ''));
                $concepto->setCantidad(number_format($rs['Cantidad'], 4, '.', ''));
                $concepto->setNoIdentificacion($rs['NoIdentificacion']);
                $concepto->setValorUnitario(number_format($rs['ValorUnitario'], 4, '.', ''));
                if ($rs['Descuento'] > 0) {
                    $concepto->setDescuento(number_format($rs['Descuento'], 2, '.', ''));
                }

                $cDescuento += round($rs['Descuento'], 2);
                $cTotal += round($rs['Importe'], 2) + round($rs['tax_iva'], 2) + round($rs['tax_ieps'], 2) - round($rs['tax_isr'], 2) - round($rs['Descuento'], 2);
                $traslados = new Comprobante\Conceptos\Concepto\Impuestos\Traslados();

                if ($rs['tax_iva'] > 0) {

                    $iva = new Comprobante\Conceptos\Concepto\Impuestos\Traslados\Traslado();
                    $iva->setBase(number_format($rs['Base'], 2, '.', ''));
                    $iva->setImpuesto('002');
                    $iva->setTasaOCuota($rs['factoriva']);
                    $iva->setTipoFactor('Tasa');
                    $iva->setImporte(number_format($rs['tax_iva'], 2, '.', ''));

                    $traslados->addTraslado($iva);
                }

                if ($rs['tax_ieps'] > 0) {
                    $ieps = new Comprobante\Conceptos\Concepto\Impuestos\Traslados\Traslado();
                    $ieps->setBase(number_format($rs['Cantidad'], 2, '.', ''));
                    $ieps->setImpuesto('003');
                    $ieps->setTasaOCuota($rs['factorieps']);
                    $ieps->setTipoFactor('Cuota');
                    $ieps->setImporte(number_format($rs['tax_ieps'], 2, '.', ''));

                    $traslados->addTraslado($ieps);
                }

                $retenciones = new Comprobante\Conceptos\Concepto\Impuestos\Retenciones();

                if ($rs['tax_isr'] > 0) {
                    $isr = new Comprobante\Conceptos\Concepto\Impuestos\Retenciones\Retencion();
                    $isr->setBase(number_format($rs['Base'], 2, '.', ''));
                    $isr->setImpuesto('001');
                    $isr->setTasaOCuota($rs['factorisr']);
                    $isr->setTipoFactor('Tasa');
                    $isr->setImporte(number_format($rs['tax_isr'], 2, '.', ''));

                    $retenciones->addRetencion($isr);
                }

                $impuestos = new Comprobante\Conceptos\Concepto\Impuestos();

                if (count($traslados->getTraslado()) > 0) {
                    $impuestos->setTraslados($traslados);
                }

                if (count($retenciones->getRetencion()) > 0) {
                    $impuestos->setRetenciones($retenciones);
                }

                if (count($retenciones->getRetencion()) > 0 || count($traslados->getTraslado()) > 0) {
                    $concepto->setImpuestos($impuestos);
                }

                $conceptos->addConcepto($concepto);
            }//while

            error_log("Total calculado " . $cTotal);
            error_log("Total declarado " . $this->comprobante->getTotal());

            if ($cDescuento > 0) {
                $this->comprobante->setDescuento(number_format($cDescuento, 2, '.', ''));
            }
            $this->comprobante->setTotal($cTotal);
            $this->comprobante->setConceptos($conceptos);
        }
    }

//conceptos

    /**
     * Recupera el sumarizado de impuestos asociados a la factura.
     * Crea el nodo Impuestos y el nodo Traslados. En el caso de Omicrom, el nodo de Retenciones no existe.
     */
    private function impuestos() {

        $impuestos = new Comprobante\Impuestos();
        $traslados = new Comprobante\Impuestos\Traslados();
        $retenciones = new Comprobante\Impuestos\Retenciones();

        $sql = "
            SELECT 
               CAST( fcd.iva AS DECIMAL( 10, 6 ) ) factoriva,
               CAST( fcd.ieps AS DECIMAL( 10, 6 ) ) factorieps,
               CAST( fcd.isr AS DECIMAL( 10, 6 ) ) factorisr,
               SUM( ROUND( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.iva, 2 ) ) tax_iva,
               SUM( ROUND( fcd.cantidad * fcd.ieps, 2 ) ) tax_ieps,
               SUM( ROUND( fcd.cantidad * fcd.precio * ( 1 - IFNULL(fcd.descuento, 0)/100 ) * fcd.isr, 2 ) ) tax_isr
            FROM fcd 
            WHERE fcd.id = " . $this->folio . "
            GROUP BY factorieps
        ";

        $importe_iva = 0.00;
        $importe_isr = 0.00;

        $total_traslado = 0.00;
        $total_retencion = 0.00;

        $factor_iva = 0.000000;
        $factor_isr = 0.000000;

        if (($query = $this->mysqlConnection->query($sql))) {

            while (($rs = $query->fetch_assoc())) {

                $total_traslado += round($rs['tax_iva'], 2) + round($rs['tax_ieps'], 2);
                $total_retencion += round($rs['tax_isr'], 2);

                $importe_iva += round($rs['tax_iva'], 2);
                $importe_isr += round($rs['tax_isr'], 2);

                $factor_iva = $rs['factoriva'];
                $factor_isr = $rs['factorisr'];

                if ($rs['tax_ieps'] > 0) {

                    $ieps = new Comprobante\Impuestos\Traslados\Traslado();
                    $ieps->setImporte(number_format($rs['tax_ieps'], 2, '.', ''));
                    $ieps->setImpuesto('003');
                    $ieps->setTasaOCuota($rs['factorieps']);
                    $ieps->setTipoFactor('Cuota');
                    $traslados->addTraslado($ieps);
                }
            }

            if ($importe_iva > 0) {

                $iva = new Comprobante\Impuestos\Traslados\Traslado();
                $iva->setImporte(number_format($importe_iva, 2, '.', ''));
                $iva->setImpuesto('002');
                $iva->setTasaOCuota($factor_iva);
                $iva->setTipoFactor('Tasa');
                $traslados->addTraslado($iva);
            }

            if ($importe_isr > 0) {

                $isr = new Comprobante\Impuestos\Retenciones\Retencion();
                $isr->setImporte(number_format($importe_isr, 2, '.', ''));
                $isr->setImpuesto('001');
                $isr->setTasaOCuota($factor_isr);
                $isr->setTipoFactor('Tasa');
                $retenciones->addRetencion($isr);
            }

            error_log("Total Retenciones " . $total_retencion);
            error_log("Total Traslados " . $total_traslado);

            if (count($traslados->getTraslado()) > 0 && $total_traslado > 0) {
                $impuestos->setTraslados($traslados);
                $impuestos->setTotalImpuestosTrasladados(number_format($total_traslado, 2, '.', ''));
            }

            if (count($retenciones->getRetencion()) > 0 && $total_retencion > 0) {
                $impuestos->setRetenciones($retenciones);
                $impuestos->setTotalImpuestosRetenidos(number_format($total_retencion, 2, '.', ''));
            }

            //$impuestos->setTotalImpuestosRetenidos(number_format($total_retencion, 2, '.', ''));
            //$impuestos->setTotalImpuestosTrasladados(number_format($total_traslado, 2, '.', ''));

            $this->comprobante->setImpuestos($impuestos);
        }
    }

//impuestos

    /**
     * Recupera el valor del campo observaciones en fc.
     * Si existe, crea la addenda Observaciones, definida por Detisa
     */
    private function observaciones() {

        $observaciones = new Comprobante\addenda\Observaciones();
        $sql = "
            SELECT fc.observaciones, fc.concepto
            FROM fc 
            WHERE fc.id = " . $this->folio;

        $observacion = '';

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $observacion = $rs['observaciones'];
            if ($observacion !== '') {
                $observaciones->addObservaciones(new Comprobante\addenda\Observaciones\Observacion($observacion));
                $this->comprobante->addAddenda($observaciones);
            }
        }
    }

//observaciones

    /**
     * updateCfdiRelacionado Actualiza el UUID de los registros relacionados
     * @param String $id id del registro en la tabla fc
     * @param String $uuid UUID del CFDI relacionado
     * @return boolean
     */
    public function updateCfdiRelacionado($id, $uuid) {

        $sql = "UPDATE relacion_cfdi SET uuid = '" . $uuid . "' WHERE id = " . $id . " AND origen = 1";
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }

//updateFC

    /**
     * updateFC Actualiza el UUID del registro principal de la factura en la tabla fc
     * @param String $id id del registro en la tabla fc
     * @param String $uuid UUID del CFDI relacionado
     * @return boolean
     */
    public function updateFC($id, $uuid) {

        $sql = "UPDATE fc SET uuid = '" . $uuid . "', status = 'Timbrada' WHERE fc.id = " . $id;
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }

//updateFC

    /**
     * updateFC Actualiza el status a Cancelado en la tabla fc
     * @param String $id id del registro en la tabla fc
     * @return boolean
     */
    public function cancel($id) {

        $sql = "UPDATE fc SET status = 'Cancelada', cantidad = 0, importe = 0, iva = 0, ieps = 0, total = 0 WHERE fc.id = " . $id;
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }

//updateFC

    /**
     * 
     * @param String $uuid UUID del comprobante en la tabla facturas
     * @param type $acuse XML con el acuse de cancelación
     * @return type
     */
    public function guardaAcuse($uuid, $acuse) {

        //ini_set("log_errors_max_len", 10240);
        error_log("Cancelando " . $uuid);
        $sql = "UPDATE facturas SET acuse_cancelacion = ?, fecha_cancelacion = TIMESTAMP( SUBSTR( ExtractValue( ?, '/Acuse/@Fecha' ), 1, 19 ) ) WHERE uuid = ?";
        if (($ps = $this->mysqlConnection->prepare($sql)) && $ps->bind_param("sss", $acuse, $acuse, $uuid) && $ps->execute()) {
            $cancelled = true;
        }
        error_log($this->mysqlConnection->error);

        return $cancelled;
    }

//guardaAcuse

    /**
     * insertFactura Crea el registro en facturas.
     * @param String $id id del registro en fc
     * @param \cfdi33\Comprobante $Comprobante Objeto Comprobante
     * @param String XML timbrado
     * @param String $clavePAC Clave del PAC usado para certificar el CFDI
     * @return boolean
     */
    public function insertFactura($id, $Comprobante, $xml, $clavePAC) {

        $sql = "INSERT INTO facturas (id_fc_fk, origen, version, fecha_emision, fecha_timbrado, cfdi_xml, clave_pac, emisor, receptor, uuid)"
                . " VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?)";
        error_log($sql);

        $stmt = $this->mysqlConnection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issssssss",
                    $id,
                    $Comprobante->getVersion(),
                    $Comprobante->getFecha(),
                    $Comprobante->getTimbreFiscalDigital()->getFechaTimbrado(),
                    $xml,
                    $clavePAC,
                    $Comprobante->getEmisor()->getRfc(),
                    $Comprobante->getReceptor()->getRfc(),
                    $Comprobante->getTimbreFiscalDigital()->getUUID());
            if (!$stmt->execute()) {
                error_log($stmt->error);
            }
        } else {
            error_log("Error insertando factura " . $this->mysqlConnection->error);
        }
    }

//insertFactura
}

//FacturaDAO
