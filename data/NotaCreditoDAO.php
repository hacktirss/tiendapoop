<?php

/*
 * NotaCreditoDAO Objeto DAO.
 * Recupera la información referente a la nota de crédito con nc.id = $folio
 * Crea un objeto de tipo Comprobante y los nodos requeridos.
 * La información vaciada en Comprobante se encuentra contenida en las tablas cia, cli, nc, ncd.
 * Este módulo está escrito de acuerdo a la estructura de base de datos, reglas y definiciones del sistema Detisa®, sistema administrativo interno,
 * y cumple con las especificaciones definidas por la autoridad tributaria SAT.
 * 
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

namespace com\detisa\detisa;

require_once ('mysqlUtils.php');
require_once ('cfdi33/Comprobante.php');
require_once ('cfdi33/addenda/Observaciones.php');

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;

class NotaCreditoDAO {

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
        $this->comprobante= new Comprobante();
        $this->mysqlConnection = getConnection();

        $this->comprobante();
        $this->emisor();
        $this->receptor();
        $this->cfdiRelacionados();
        $this->conceptos();
        $this->impuestos();
        $this->observaciones();
    }

    function getComprobante() {
        return $this->comprobante;
    }

    function setFolio($folio) {
        $this->folio = $folio;
    }

    function setComprobante($comprobante) {
        $this->comprobante = $comprobante;
    }

    /**
     * Recupera la información relativa a la nota de crédito.
     * Crea el objeto Comprobante
     */
    private function comprobante() {

        /* @var $emisor Comprobante */
        $this->comprobante = new Comprobante();
        $sql = "SELECT 
                    nc.id Folio, 
                    DATE_FORMAT(nc.fecha, '%Y-%m-%dT%H:%h:%i') Fecha, 
                    nc.formadepago FormaPago, 
                    nc.metododepago MetodoPago, 
                    nc.total Total,
                    nc.importe SubTotal,
                    cias.codigo LugarExpedicion
                FROM nc 
                LEFT JOIN cias ON cias.id = " . $this->cia . "
                WHERE nc.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $this->comprobante->setFolio($rs['Folio']);
            $this->comprobante->setFecha($rs['Fecha']);
            $this->comprobante->setTipoDeComprobante("E");
            $this->comprobante->setVersion("3.3");
            $this->comprobante->setFormaPago($rs['FormaPago']);
            $this->comprobante->setMetodoPago($rs['MetodoPago']);
            $this->comprobante->setMoneda("MXN");
            $this->comprobante->setTipoCambio(1);
            $this->comprobante->setTotal(number_format($rs['Total'], 2, '.', ''));
            $this->comprobante->setSubTotal(number_format($rs['SubTotal'], 2, '.', ''));
            $this->comprobante->setLugarExpedicion($rs['LugarExpedicion']);
        }
    }//comprobante

    /**
     * Recupera los datos de la estación de servicio.
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
    }//emisor

    /**
     * Recupera los datos del receptor del CFDI.
     * Crea el nodo Receptor.
     */
    private function receptor() {

        /* @var $emisor Comprobante\Receptor */
        $receptor = new Comprobante\Receptor();
        $sql = "SELECT cli.nombre Nombre, cli.rfc Rfc, 'G02' UsoCFDI 
                FROM nc JOIN cli ON nc.cliente = cli.id AND cli.cia = " . $this->cia . " 
                WHERE nc.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {

            $receptor->setNombre($rs['Nombre']);
            $receptor->setRfc($rs['Rfc']);
            $receptor->setUsoCFDI($rs['UsoCFDI']);
        }
        $this->comprobante->setReceptor($receptor);
    }//receptor

    /**
     * Recupera el CFDI relacionado. Por definición de Detisa, solo se soporta un CFDI relacionado.
     * En el caso de la nota de crédito, este nodo es obligatorio.
     * Crea el nodo CfdiRelacionados.
     */
    private function cfdiRelacionados() {

        $cfdiRelacionados = new Comprobante\CfdiRelacionados();

        $sql = "SELECT * FROM relacion_cfdi WHERE id = " . $this->folio . " AND origen = 2";
        if (($query = $this->mysqlConnection->query($sql))) {
            while ($rs = $query->fetch_assoc()) {

                    $cfdiRelacionado = new Comprobante\CfdiRelacionados\CfdiRelacionado();
                    $cfdiRelacionado->setUUID($rs['uuid_relacionado']);
                    $cfdiRelacionados->addCfdiRelacionado($cfdiRelacionado);

                    $cfdiRelacionados->setTipoRelacion($rs['tipo_relacion']);
                }
        }
        
        if (count($cfdiRelacionados->getCfdiRelacionado())>0) {
            $this->comprobante->setCfdiRelacionados($cfdiRelacionados);            
        }
    }//cfdiRelacionados

    /**
     * Recupera los conceptos asociados a la nota de crédito.
     * Crea el nodo Conceptos, el arreglo de nodos Concepto y los nodos de Impuesto asociados a cada Concepto.
     */
    private function conceptos() {

        $conceptos = new Comprobante\Conceptos();

        $sql  = "
            SELECT 
               ncd.producto NoIdentificacion,
               ncd.descripcion Descripcion,
               CAST(ncd.iva AS DECIMAL(10, 6)) factoriva,
               CAST(ncd.ieps AS DECIMAL(10, 6)) factorieps,
               CAST(ncd.isr AS DECIMAL(10, 6)) factorisr,
               inv.inv_cunidad ClaveUnidad,
               inv.inv_cproducto ClaveProdServ,
               ncd.cantidad Cantidad,
               ncd.precio ValorUnitario,
               ROUND(ncd.cantidad * ncd.precio, 4) Importe,
               ROUND(ncd.cantidad * ncd.precio, 4) Base,
               ROUND(ncd.importe, 4) Total,
               ROUND(ncd.cantidad * ncd.precio * ncd.iva, 4) tax_iva,
               ROUND(ncd.cantidad * ncd.ieps, 4) tax_ieps,
               ROUND(ncd.importe * ncd.isr, 4) tax_isr
            FROM ncd 
            LEFT JOIN inv ON ncd.producto=inv.id
            WHERE ncd.id = " . $this->folio;

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

                $traslados = new Comprobante\Conceptos\Concepto\Impuestos\Traslados();
                $retenciones = new Comprobante\Conceptos\Concepto\Impuestos\Retenciones();

                if ($rs['tax_iva']>0) {

                    $iva = new Comprobante\Conceptos\Concepto\Impuestos\Traslados\Traslado();
                    $iva->setBase(number_format($rs['Base'], 2, '.', ''));
                    $iva->setImpuesto('002');
                    $iva->setTasaOCuota($rs['factoriva']);
                    $iva->setTipoFactor('Tasa');
                    $iva->setImporte(number_format($rs['tax_iva'], 2, '.', ''));

                    $traslados->addTraslado($iva);
                }

                if ($rs['tax_ieps']>0) {

                    $ieps = new Comprobante\Conceptos\Concepto\Impuestos\Traslados\Traslado();
                    $ieps->setBase(number_format($rs['Cantidad'], 2, '.', ''));
                    $ieps->setImpuesto('003');
                    $ieps->setTasaOCuota($rs['factorieps']);
                    $ieps->setTipoFactor('Cuota');
                    $ieps->setImporte(number_format($rs['tax_ieps'], 2, '.', ''));

                    $traslados->addTraslado($ieps);
                }

                if ($rs['tax_isr']>0) {

                    $isr = new Comprobante\Conceptos\Concepto\Impuestos\Retenciones\Retencion();
                    $isr->setBase(number_format($rs['Base'], 2, '.', ''));
                    $isr->setImpuesto('001');
                    $isr->setTasaOCuota($rs['factorisr']);
                    $isr->setTipoFactor('Tasa');
                    $isr->setImporte(number_format($rs['tax_isr'], 2, '.', ''));

                    $retenciones->addRetencion($isr);
                }

                $impuestos = new Comprobante\Conceptos\Concepto\Impuestos();

                if (count($traslados->getTraslado())>0) {
                    $impuestos->setTraslados($traslados);
                }

                if (count($retenciones->getRetencion())>0) {
                    $impuestos->setRetenciones($retenciones);
                }

                $concepto->setImpuestos($impuestos);

                $conceptos->addConcepto($concepto);
            }//while

            $this->comprobante->setConceptos($conceptos);
        }
    }//retrieveConceptosNotaDeCredito

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function impuestos() {

        $impuestos = new Comprobante\Impuestos();
        $traslados = new Comprobante\Impuestos\Traslados();
        $retenciones = new Comprobante\Impuestos\Retenciones();

        $sql = "
            SELECT 
               CAST(ncd.iva AS DECIMAL(10, 6)) factoriva,
               CAST(ncd.ieps AS DECIMAL(10, 6)) factorieps,
               CAST(ncd.isr AS DECIMAL(10, 6)) factorisr,
               SUM( ROUND(ncd.cantidad * ncd.precio * ncd.iva, 4) ) tax_iva,
               ROUND(ncd.cantidad * ncd.ieps, 4) tax_ieps,
               SUM( ROUND(ncd.importe * ncd.isr, 4) ) tax_isr
            FROM ncd 
            WHERE ncd.id = " . $this->folio . "
            GROUP BY factorieps";


        $importe_iva = 0.00;
        $importe_isr = 0.00;

        $total_traslado = 0.00;
        $total_retencion = 0.00;

        $factor_iva = 0.000000;
        $factor_isr = 0.000000;

        if (($query = $this->mysqlConnection->query($sql))) {

            while (($rs = $query->fetch_assoc())) {

                $total_traslado += $rs['tax_iva'] + $rs['tax_ieps'];
                $total_retencion += $rs['tax_isr'];

                $importe_iva += $rs['tax_iva'];
                $importe_isr += $rs['tax_isr'];

                $factor_iva = $rs['factoriva'];
                $factor_isr = $rs['factorisr'];

                if ($rs['tax_ieps']>0) {

                    $ieps = new Comprobante\Impuestos\Traslados\Traslado();
                    $ieps->setImporte(number_format($rs['tax_ieps'], 2, '.', ''));
                    $ieps->setImpuesto('003');
                    $ieps->setTasaOCuota($rs['factorieps']);
                    $ieps->setTipoFactor('Cuota');
                    $traslados->addTraslado($ieps);
                }
            }

            if ($importe_iva>0) {

                $iva = new Comprobante\Impuestos\Traslados\Traslado();
                $iva->setImporte(number_format($importe_iva, 2, '.', ''));
                $iva->setImpuesto('002');
                $iva->setTasaOCuota($factor_iva);
                $iva->setTipoFactor('Tasa');
                $traslados->addTraslado($iva);
            }

            if ($importe_isr>0) {

                $isr = new Comprobante\Impuestos\Traslados\Traslado();
                $isr->setImporte(number_format($importe_isr, 2, '.', ''));
                $isr->setImpuesto('001');
                $isr->setTasaOCuota($factor_isr);
                $isr->setTipoFactor('Tasa');
                $retenciones->addRetencion($isr);
            }

            error_log("Total Retenciones " . $total_retencion);
            error_log("Total Traslados " . $total_traslado);

            if (count($traslados->getTraslado())>0 && $total_traslado>0) {
                $impuestos->setTraslados($traslados);
                $impuestos->setTotalImpuestosTrasladados(number_format($total_traslado, 2, '.', ''));
            }
            
            if (count($retenciones->getRetencion())>0 && $total_retencion>0) {
                $impuestos->setRetenciones($retenciones);
                $impuestos->setTotalImpuestosRetenidos(number_format($total_retencion, 2, '.', ''));
            }


            $this->comprobante->setImpuestos($impuestos);
        }
    }//getImpuestosFactura

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function observaciones() {

        $observaciones = new Comprobante\addenda\Observaciones();
        $sql = "
            SELECT nc.observaciones 
            FROM nc
            WHERE ncd.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {
            $observaciones->addObservaciones(new Comprobante\addenda\Observaciones\Observacion($observacion = $rs['observaciones']));                    
            return $observaciones;
        }
    }

    /**
     * updateCfdiRelacionado Actualiza el UUID de los registros relacionados
     * @param String $id id del registro en la tabla fc
     * @param String $uuid UUID del CFDI relacionado
     * @return boolean
     */    
    public function updateCfdiRelacionado($id, $uuid) {

        $sql = "UPDATE relacion_cfdi SET uuid = '" . $uuid . "' WHERE id = " . $id . " AND origen = 2";
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }//updateFC

    /**
     * updateFC Actualiza el UUID del registro principal de la factura en la tabla fc
     * @param String $id id del registro en la tabla fc
     * @param String $uuid UUID del CFDI relacionado
     * @return boolean
     */    
    public function updateNC($id, $uuid) {

        $sql = "UPDATE nc SET uuid = '" . $uuid . "', status = 'Timbrada' WHERE nc.id = " . $id;
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }//updateFC

    /**
     * updateFC Actualiza el estatus a Cancelado en la tabla nc
     * @param String $id id del registro en la tabla nc
     * @return boolean
     */    
    public function cancel($id) {

        $sql = "UPDATE nc SET status = 'Cancelada', cantidad = 0, importe = 0, iva = 0, ieps = 0, total = 0 WHERE nc.id = " . $id;
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }//updateNC

    /**
     * 
     * @param String $uuid UUID del comprobante en la tabla facturas
     * @param type $acuse XML con el acuse de cancelación
     * @return type
     */
    public function guardaAcuse($uuid, $acuse) {

        $sql = "UPDATE facturas SET acuse_cancelacion = ?, fecha_cancelacion = TIMESTAMP( SUBSTR( ExtractValue( ?, '/Acuse/@Fecha' ), 1, 19 ) ) WHERE uuid = ?";
        if (($ps = $this->mysqlConnection->prepare($sql))
                && $ps->bind_param("sss", $acuse, $acuse, $uuid)
                && $ps->execute()) {
            $cancelled = true;
        }
        return $cancelled;
    }//guardaAcuse

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
                . " VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    }//insertFactura
}//NotaCreditoDAO
