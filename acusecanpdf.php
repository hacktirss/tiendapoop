<?php

#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("nusoap/nusoap.php");
include_once ("pdf/PDFTransformerAC.php");
include_once ("data/CertificadoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();

$busca = $request->getAttribute("busca");
$table = $request->getAttribute("table");

$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();
$link = conectarse();
$certificadoDAO = new CertificadoDAO();

$cSQL = "SELECT facturas.uuid, facturas.acuse_cancelacion acuse 
        FROM $table AS fc 
        JOIN facturas ON facturas.id_fc_fk = fc.id 
        AND fc.id = $busca AND fc.cia = " . $UsuarioSesion->getCia();
$cRS = $mysqli->query($cSQL)->fetch_array();

$cUid = $cRS["uuid"];
$cAcuse = $cRS["acuse"];

if (empty($cAcuse)) {
    try {
        error_log("Try recovering acuse ...");
        $wsdl = "http://localhost:9190/GeneradorCFDIsWEB/Facturador?wsdl";

        $client = new nusoap_client($wsdl, true);

        $client->timeout = 180;
        $client->soap_defencoding = "UTF-8";
        $client->namespaces = array("SOAP-ENV" => "http://schemas.xmlsoap.org/soap/envelope/");

        $params = array(
            "user" => "WS0DDT0026",
            "password" => "e16875b942",
            "uuid" => $cUid);
        $result = $client->call("obtenerAcuseCancelacion", $params, false, "", "");
    } catch (Exception $e) {
        error_log("Error obteniendo acuse: " . $e->getMessage());
    }
}

$selectAcuse = "SELECT 
            IF (facturas.version = '3.3', ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/@Nombre')  , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/@nombre')) nombre, 
            IF (facturas.version = '3.3', ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/@Rfc')     , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/@rfc')) rfc, 
            IF (facturas.version = '3.3', cias.direccion                                                   , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@calle')) calle, 
            IF (facturas.version = '3.3', cias.numeroext                                                   , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@noExterior')) noExterior, 
            IF (facturas.version = '3.3', cias.colonia                                                     , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@colonia')) colonia, 
            IF (facturas.version = '3.3', cias.municipio                                                   , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@municipio')) municipio, 
            IF (facturas.version = '3.3', cias.estado                                                      , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@estado')) estado, 
            IF (facturas.version = '3.3', 'MÉXICO'                                                         , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@pais')) pais, 
            IF (facturas.version = '3.3', cias.codigo                                                       , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Emisor/cfdi:DomicilioFiscal/@codigoPostal')) codigoPostal, 
            IF (facturas.version = '3.3', ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Receptor/@Nombre'), ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Receptor/@nombre')) rnombre, 
            IF (facturas.version = '3.3', ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Receptor/@Rfc')   , ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Receptor/@rfc')) rrfc, 
            IF (facturas.version = '3.3', ExtractValue(cfdi_xml, '/cfdi:Comprobante/@Folio')               , ExtractValue(cfdi_xml, '/cfdi:Comprobante/@folio')) folio, 
            ExtractValue(cfdi_xml, '/cfdi:Comprobante/cfdi:Complemento/tfd:TimbreFiscalDigital/@UUID') UUID, 
            ExtractValue(acuse_cancelacion, '/Acuse/Signature/SignatureValue') SelloAcuse, 
            ExtractValue(acuse_cancelacion, '/Acuse/@Fecha')  FechaCancelacion 
        FROM cias, $table AS fc 
        JOIN facturas ON facturas.id_fc_fk = fc.id
        WHERE TRUE 
        AND fc.cia = cias.id
        AND fc.cia = " . $UsuarioSesion->getCia() . "
        AND acuse_cancelacion IS NOT NULL 
        AND TRIM( acuse_cancelacion ) <>  '' 
        AND fc.id = $busca 
        UNION
        SELECT 
            cias.nombre, cias.rfc rfc, cias.direccion calle, cias.numeroext noExterior, cias.colonia colonia, cli.rfc rrfc,
            cias.municipio, cias.estado estado, 'MÉXICO' pais, cias.codigo codigoPostal, cli.nombre rnombre, 
            fc.id folio, fc.uuid UUID, 'NO DISPONIBLE' SelloAcuse, 'NO DISPONIBLE' FechaCancelacion 
        FROM cias, $table AS fc 
        LEFT JOIN cli ON " . ($table === "ing" ? "fc.cuenta" : "fc.cliente") . " = cli.id 
        WHERE TRUE 
        AND fc.cia = cias.id
        AND fc.cia = cli.cia
        AND fc.cia = " . $UsuarioSesion->getCia() . "
        AND FOUND_ROWS() = 0 AND fc.id = $busca";

$acuse = new AcuseVO();

if (($selectFactura = $mysqli->query($selectAcuse))) {
    //error_log($selectAcuse);
    while ($rg = $selectFactura->fetch_array()) {

        $acuse->setNombreEmisor($rg['nombre']);
        $acuse->setRfcEmisor($rg['rfc']);
        $direccion = $rg['calle'] . " " . $rg['numero'] . " " . $rg['colonia'] . "<br />" . $rg['municipio'] . ", " . $rg['estado'] . " C.P. " . $rg['codigoPostal'];
        $acuse->setNombreReceptor($rg['rnombre']);
        $acuse->setRfcReceptor($rg['rrfc']);
        $acuse->setFolio(( empty($rg['serie']) ? "" : ( $rg['serie'] . " - " ) ) . $rg['folio']);
        $acuse->setUUID($rg['UUID']);
        $acuse->setSello($rg['SelloAcuse']);
        $acuse->setFecha($rg['FechaCancelacion']);
    }
} else {
    error_log($mysqli->error);
}

$certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename='" . $myrowsel['uuid'] . ".pdf'");
echo PDFTransformerAC::getPDF($acuse, $direccion, "S", $certificadoVO->getLogo());
