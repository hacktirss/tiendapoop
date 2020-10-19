<?php

/*
 * ComprobanteDAO
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
require_once ('cfdi33/complemento/Pagos.php');
require_once ('cfdi33/addenda/Observaciones.php');
require_once ('pdf/PDFTransformerRP.php');

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;

class ReciboPagoDAO {

    private $cia;
    private $folio;
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
        $this->pagos();
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
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function comprobante() {

        /* @var $emisor \cfdi33\Comprobante */
        $this->comprobante = new Comprobante();
        $sql = "SELECT 
                    ing.folio, 
                    DATE_FORMAT(now(), '%Y-%m-%dT%H:%i:%s') fecha, 
                    cias.codigo
              FROM ing,cias
              WHERE TRUE AND ing.cia = cias.id 
              AND ing.cia = " . $this->cia . "
              AND ing.id = " . $this->folio;
        error_log($sql);
        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {
            $this->comprobante->setFolio($rs['folio']);
            $this->comprobante->setFecha($rs['fecha']);
            $this->comprobante->setTipoDeComprobante("P");
            $this->comprobante->setVersion("3.3");
            $this->comprobante->setMoneda("XXX");
            $this->comprobante->setSubTotal('0');
            $this->comprobante->setTotal('0');
            $this->comprobante->setLugarExpedicion($rs['codigo']);
        }//if
    }

//comprobante

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
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
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function receptor() {

        /* @var $emisor Comprobante\Receptor */
        $receptor = new Comprobante\Receptor();
        $sql = "SELECT cli.nombre Nombre, cli.rfc Rfc FROM ing JOIN cli ON ing.cuenta = cli.id AND ing.cia = cli.cia 
                WHERE ing.id = " . $this->folio . " AND ing.cia = " . $this->cia . "";
        error_log($sql);
        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {
            $receptor->setNombre($rs['Nombre']);
            $receptor->setRfc($rs['Rfc']);
            $receptor->setUsoCFDI("P01");
        }
        $this->comprobante->setReceptor($receptor);
    }

//receptor

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function cfdiRelacionados() {

        $cfdiRelacionados = new Comprobante\CfdiRelacionados();

        $sql = "
            SELECT IFNULL( F.uuid,  '' ) UUID
            FROM fc F
            LEFT JOIN ingd PE ON PE.referencia = F.folio AND F.status = 'Timbrada'
            JOIN ing P ON P.id = PE.id
            WHERE TRUE AND F.cia = P.cia 
            AND P.cia = " . $this->cia . "
            AND P.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql))) {
            $cfdiRelacionados->setTipoRelacion('04');
            while (($rs = $query->fetch_assoc())) {
                if (!empty($rs['UUID'])) {
                    $cfdiRelacionado = new Comprobante\CfdiRelacionados\CfdiRelacionado();
                    $cfdiRelacionado->setUUID($rs['UUID']);
                    $cfdiRelacionados->addCfdiRelacionado($cfdiRelacionado);
                }
            }
            $this->comprobante->setCfdiRelacionados($cfdiRelacionados);
        } else{
            error_log("Relacionados error: " . $this->mysqlConnection->error); 
        }
    }

//cfdiRelacionados

    /**
     * 
     * @throws \com\detisa\omicrom\Exception 
     */
    private function conceptos() {

        $conceptos = new Comprobante\Conceptos();
        $concepto = new Comprobante\Conceptos\Concepto();
        $concepto->setClaveProdServ('84111506');
        $concepto->setClaveUnidad('ACT');
        $concepto->setDescripcion('Pago');
        $concepto->setImporte('0');
        $concepto->setCantidad('1');
        $concepto->setValorUnitario('0');
        $conceptos->addConcepto($concepto);
        $this->comprobante->setConceptos($conceptos);
    }

//conceptos

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function doctosRelacionados() {

        $doctosRelacionados = array();
        $sql = "
            SELECT 
                fc.folio Folio, 
                fc.uuid IdDocumento, 
                ingd.importe ImpPagado, 
                ROUND( fc.total - IFNULL(cxc.importe, 0), 2 ) ImpSaldoAnt, 
                ROUND( fc.total, 2 ) - ROUND( IFNULL(cxc.importe, 0) + ingd.importe, 2 ) ImpSaldoInsoluto, 
                IFNULL(cxc.parcialidades, 0)+1 NumParcialidad
            FROM ingd 
            JOIN fc ON fc.folio = ingd.referencia AND fc.cia = " . $this->cia . "
            LEFT JOIN (
                SELECT SUBSTR(referencia, 3) factura, COUNT(*) parcialidades, SUM( CAST( importe AS DECIMAL( 11, 2 ) ) ) importe 
                FROM (
                    SELECT * FROM cxc WHERE tm = 'H' AND concepto LIKE '%factura%' AND cia = " . $this->cia . "
                    UNION ALL
                    SELECT * FROM cxch WHERE tm = 'H' AND concepto LIKE '%factura%' AND cia = " . $this->cia . "
                ) cxch
                WHERE recibo < " . $this->folio . "
                GROUP BY factura
            ) cxc
            ON fc.folio = cxc.factura 
            WHERE ingd.id = " . $this->folio;
        error_log($sql);
        if (($query = $this->mysqlConnection->query($sql))) {

            while (($rs = $query->fetch_assoc())) {

                $doctoRelacionado = new Comprobante\complemento\Pagos\Pago\DoctoRelacionado();
                $doctoRelacionado->setFolio($rs['Folio']);
                $doctoRelacionado->setIdDocumento($rs['IdDocumento']);
                $doctoRelacionado->setImpPagado(number_format($rs['ImpPagado'], 2, '.', ''));
                $doctoRelacionado->setImpSaldoAnt(number_format($rs['ImpSaldoAnt'], 2, '.', ''));
                $doctoRelacionado->setImpSaldoInsoluto(number_format($rs['ImpSaldoInsoluto'], 2, '.', ''));
                $doctoRelacionado->setMonedaDR("MXN");
                $doctoRelacionado->setMetodoDePagoDR("PPD");
                $doctoRelacionado->setNumParcialidad($rs['NumParcialidad']);
                array_push($doctosRelacionados, $doctoRelacionado);
            }
            return $doctosRelacionados;
        }
    }

//doctosRelacionados

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function pagos() {

        $pagos = new Comprobante\complemento\Pagos();
        $pago = new Comprobante\complemento\Pagos\Pago();

        $pagos->setVersion("1.0");

        $sql = "
            SELECT
                ing.formapago FormaDePagoP,
                DATE_FORMAT(ing.fechap, '%Y-%m-%dT%H:%h:%i') FechaPago, 
                ing.importe Monto,
                ing.numoperacion NumOperacion
            FROM ing
            WHERE TRUE 
            AND ing.cia = " . $this->cia . "
            AND ing.id = " . $this->folio;
        error_log($sql);

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {
            $pago->setMonto(number_format($rs['Monto'], 2, '.', ''));
            $pago->setMonedaP("MXN");
            //$pago->setTipoCambioP(1);
            $pago->setFormaDePagoP($rs['FormaDePagoP']);
            $pago->setFechaPago($rs['FechaPago']);
            $pago->setNumOperacion($rs['NumOperacion']);
            $pago->setDoctoRelacionado($this->doctosRelacionados());
            // TODO Registrar Banco Beneficiario
            // TODO Registrar Banco Emisor
            $pagos->addPagos($pago);
            $this->comprobante->addComplemento($pagos);
        }
    }

//pagos

    /**
     * 
     * @throws \com\detisa\omicrom\Exception
     */
    private function observaciones() {

        $observaciones = new Comprobante\addenda\Observaciones();
        $sql = "
            SELECT ing.concepto
            FROM ing
            WHERE TRUE 
            AND ing.cia = " . $this->cia . " 
            AND ing.id = " . $this->folio;

        if (($query = $this->mysqlConnection->query($sql)) && ($rs = $query->fetch_assoc())) {
            if ($rs['concepto'] != NULL && $rs['concepto'] != "") {
                $observaciones->addObservaciones(new Comprobante\addenda\Observaciones\Observacion($rs['concepto']));
                $this->comprobante->addAddenda($observaciones);
            }
        }
    }

//observaciones

    /**
     * updateFC Actualiza el UUID del registro principal de la factura en la tabla fc
     * @param String $id id del registro en la tabla fc
     * @param String $uuid UUID del CFDI relacionado
     * @return boolean
     */
    public function updateIng($id, $uuid) {

        $sql = "UPDATE ing SET uuid = '" . $uuid . "', statusCFDI = 'Timbrado' 
                WHERE ing.id = " . $id . " AND ing.cia = " . $this->cia . "";
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }

//updateFC

    /**
     * updateFC Actualiza el estatus a Cancelado en la tabla nc
     * @param String $id id del registro en la tabla nc
     * @return boolean
     */
    public function cancel($id) {

        $sql = "UPDATE ing SET statusCFDI = 'Cancelado' WHERE ing.id = " . $id . " AND ing.cia = " . $this->cia . "";
        error_log($sql);
        return $this->mysqlConnection->query($sql);
    }

//updateNC

    /**
     * 
     * @param String $uuid UUID del comprobante en la tabla facturas
     * @param type $acuse XML con el acuse de cancelación
     * @return type
     */
    public function guardaAcuse($uuid, $acuse) {

        $sql = "UPDATE facturas SET acuse_cancelacion = ?, fecha_cancelacion = TIMESTAMP( SUBSTR( ExtractValue( ?, '/Acuse/@Fecha' ), 1, 19 ) ) WHERE uuid = ?";
        if (($ps = $this->mysqlConnection->prepare($sql)) && $ps->bind_param("sss", $acuse, $acuse, $uuid) && $ps->execute()) {
            $cancelled = true;
        }
        return $cancelled;
    }

//guardaAcuse

    /**
     * insertFactura Crea el registro en facturas.
     * @param String $id id del registro en fc
     * @param \cfdi33\Comprobante $Comprobante Objeto Comprobante
     * @param String $clavePAC Clave del PAC usado para certificar el CFDI
     * @return boolean
     */
    public function insertFactura($id, $Comprobante, $xml, $clavePAC) {

        $sql = "INSERT INTO facturas (id_fc_fk, version, origen, fecha_emision, fecha_timbrado, cfdi_xml, clave_pac, emisor, receptor, uuid)"
                . " VALUES (?, ?, 3, ?, ?, ?, ?, ?, ?, ?)";
        error_log($sql);

        $stmt = $this->mysqlConnection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssss",
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
