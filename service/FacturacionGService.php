<?php

include_once ("data/CiaDAO.php");
include_once ("data/CliDAO.php");
include_once ("data/FcDAO.php");
include_once ("data/NcDAO.php");
include_once ("data/CxcDAO.php");
include_once ("data/PagoDAO.php");
include_once ("data/FacturaConceptosDAO.php");
include_once ("data/NotaCreditoConceptosDAO.php");
include_once ("data/ProveedorPACDAO.php");
include_once ("data/RelacionesDAO.php");
include_once ("data/MetodoDePagoDAO.php");
include_once ("data/VariablesDAO.php");
include_once ("data/FacturaVO.php");
include_once ("data/CertificadoDAO.php");
include_once ("data/NotaDeCreditoDetisa.php");
include_once ("data/FacturaDetisa.php");

include_once ("cfdi33/pac/PACServiceFactory.php");
include_once ("cfdi33/pac/PACFactory.php");
include_once ("cfdi33/pac/PAC.php");
include_once ("cfdi33/Certificates/Certificate.php");
include_once ("cfdi33/Certificates/Commons.php");
include_once ("cfdi33/SelloCFDI.php");
include_once ("cfdi33/ValidadorCFDI.php");

include_once ("pdf/PDFTransformer.php");
include_once ("MailSender.php");
include_once ("CFDIComboBoxes.php");

use com\softcoatl\utils as utils;
use com\softcoatl\cfdi\v33\schema\Comprobante;
use com\softcoatl\cfdi\v33\PACFactory;
use com\softcoatl\cfdi\v33\PACServiceFactory;
use com\softcoatl\cfdi\v33\SelloCFDI;
use com\softcoatl\cfdi\v33\ValidadorCFDI;
use com\softcoatl\utils\HTTPUtils;
use com\softcoatl\security\commons\Certificate;

Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\\TimbreFiscalDigital");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\Pagos");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\INE");
Comprobante::registerAddenda("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\addenda\\Observaciones");

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$nameSession = "moduloFacturacion";

$mysqli = conectarse();
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$Tipo = HTTPUtils::getSessionBiValue($nameSession, "Paso"); //Tipo de factura o nota de credito
$busca = HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$ciaDAO = new CiaDAO();
$certificadoDAO = new CertificadoDAO();
$cliDAO = new CliDAO();
$cxcDAO = new CxcDAO();
$pagoDAO = new PagoDAO();
$fcDAO = new FcDAO();
$fccDAO = new FacturaConceptosDAO();
$pacDAO = new ProveedorPACDAO();
$mdpDAO = new MetodoDePagoDAO();
$relatedDAO = new RelacionesDAO();
$facturaDetisa = new com\detisa\detisa\FacturaDetisa($busca, $UsuarioSesion->getCia());

if ($Tipo == 1) {
    $cTipo = "Factura";
    $TipoCfd = "FA";
    $Tabla = "fc";
    $TablaDetalle = "fcd";
} else {
    $cTipo = "Nota de credito";
    $TipoCfd = "CR";
    $Tabla = "nc";
    $TablaDetalle = "ncd";

    $fcDAO = new NcDAO();
    $fccDAO = new NotaCreditoConceptosDAO();
    $facturaDetisa = new \com\detisa\detisa\NotaDeCreditoDetisa($busca, $UsuarioSesion->getCia());
}


$cia = $ciaDAO->retrieveFields("*");
$ppac = $pacDAO->getActive();
$fcVO = $fcDAO->retrieve($busca, "id", $UsuarioSesion->getCia());
$cliVO = $cliDAO->retrieve($fcVO->getCliente(), "id", $UsuarioSesion->getCia());

$pac = PACFactory::getPAC($ppac->getUrl_webservice(), $ppac->getUsuario(), $ppac->getPassword(), $ppac->getClave_pac());
if ($pac instanceof cfdi33\SifeiPAC) {
    $pac->setIdEquipo($ppac->getClave_aux());
    $pac->setSerie("");
}

if ($request->hasAttribute("Boton")) {
    error_log(print_r($request, true));
    if ($request->getAttribute("Boton") == "Guardar estos cambios" && $request->hasAttribute("Cliente")) {

        $cliVO->setCorreo($sanitize->sanitizeEmail("Correo"));
        $cliVO->setEnviarcorreo($sanitize->sanitizeString("Enviarcorreo"));
        $cliVO->setCuentaban($sanitize->sanitizeString("Cuentaban"));
        $cliDAO->update($cliVO);

        $fcVO->setMetododepago($sanitize->sanitizeString("Tipodepago"));
        $fcVO->setFormadepago($sanitize->sanitizeString("Formadepago"));
        $fcVO->setCndpago($sanitize->sanitizeString("Cndpago"));
        $fcVO->setUsocfdi($sanitize->sanitizeString("cuso"));
        $fcVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
        $fcVO->setConcepto($sanitize->sanitizeString("Concepto"));
        $fcDAO->update($fcVO);

        $Msj = "Cambio guardado";
    } elseif ($request->getAttribute("Boton") === "Timbrar") {

        if (count($facturaDetisa->getComprobante()->getConceptos()->getConcepto()) == 0) {
            $Msj = "El comprobante no tiene conceptos, no es posible timbrar un comprobante sin conceptos. Favor de verificar.";
        } else {

            $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");
            $service = PACServiceFactory::getPACService($pac);
            //$csd = new Certificate(file_get_contents("certificado/cer.pem"), file_get_contents("certificado/key.pem"));
            $csd = new Certificate($certificadoVO->getCertificado(), $certificadoVO->getLlave());
            $sello = new SelloCFDI($csd);
            $sello->sellaComprobante($facturaDetisa->getComprobante());
            
            $DOMFactura = $facturaDetisa->getComprobante()->asXML();
            $xmlFactura = $DOMFactura->saveXML();
            error_log($xmlFactura);
            $validation = ValidadorCFDI::validate($xmlFactura, "http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd");
            error_log($validation);

            try {

                if ($validation === true) {
                    error_log("Invocando Servicio de facturación");
                    ini_set("log_errors_max_len", 10000);
                    error_log($xmlFactura);
                    $xmlCFDI = $service->timbraComprobante($xmlFactura);
                    error_log($xmlCFDI);

                    if ($xmlCFDI) {
                        $Msj = "Tu " . ($Tipo == 1 ? "Factura" : "Nota de Crédito") . " ha sido creada con exito. " . $Msj;
                        $comprobanteTimbrado = Comprobante::parse($xmlCFDI);

                        $pdfCFDI = PDFTransformer::getPDF($comprobanteTimbrado, $Tipo == 1 ? "Factura" : "Nota de Credito", "S", $certificadoVO->getLogo());

                        $facturaDetisa->setComprobanteTimbrado($comprobanteTimbrado);
                        $facturaDetisa->setRepresentacionImpresa($pdfCFDI);
                        $facturaDetisa->setXmlTimbrado($xmlCFDI);
                        $facturaDetisa->save($busca, $pac->getPac());
                        $facturaDetisa->update($busca);

                        file_put_contents("fae/archivos/" . $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID() . ".xml", $xmlCFDI);
                        file_put_contents("fae/archivos/" . $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID() . ".pdf", $pdfCFDI);


                        $Recibo = 0;
                        $folio = $facturaDetisa->getComprobante()->getFolio();

                        if ($Tipo == 4) {

                            $ingVO = new PagoVO();
                            $ingVO->setCia($UsuarioSesion->getCia());
                            $ingVO->setCuenta($fcVO->getCliente());
                            $ingVO->setConcepto("Pago referente a factura " . $fcVO->getFactura());
                            $ingVO->setImporte($facturaDetisa->getComprobante()->getTotal());
                            $ingVO->setFecha(date("Y-m-d H:i:s"));
                            $ingVO->setReferencia("NC");
                            $ingVO->setFechap(date("Y-m-d"));
                            $ingVO->setFormapago($fcVO->getFormadepago());
                            $ingVO->setStatus("Cerrada");

                            if (($id = $pagoDAO->create($ingVO)) < 0) {
                                $Msj .= "Ocurrio un error al crear registro en estado de cuenta";
                            } else {
                                $Recibo = $id;
                            }
                        }

                        $cxcVO = new CxcVO();
                        $cxcVO->setCia($UsuarioSesion->getCia());
                        $cxcVO->setCuenta($fcVO->getCliente());
                        $cxcVO->setFecha($comprobanteTimbrado->getFecha());
                        $cxcVO->setReferencia($Tipo == 1 ? "F-" . $folio : "NC-" . $folio);
                        $cxcVO->setTm($Tipo == 1 ? "C" : "H");
                        $cxcVO->setFechav($comprobanteTimbrado->getTimbreFiscalDigital()->getFechaTimbrado());
                        $cxcVO->setConcepto($fcVO->getConcepto());
                        $cxcVO->setImporte($comprobanteTimbrado->getTotal());
                        $cxcVO->setRecibo($Recibo);
                        $cxcVO->setFactura($busca);
                        if (($id = $cxcDAO->create($cxcVO)) < 0) {
                            $Msj .= " Ocurrio un error al crear registro en estado de cuenta. ";
                        }


                        if ($cliVO->getEnviarcorreo() == "Si" && !empty($cliVO->getCorreo())) {
                            if (MailSender::send($CiaSesion, $cliVO, $fcVO->getFolio(), $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID(), $xmlCFDI, $pdfCFDI)) {
                                $Msj .= " Se han enviado con éxito sus archivos XML y PDF";
                            }
                        }
                    } else {
                        $Msj = $service->getError();
                    }

                    header("Location: facturas.php?Msj=$Msj");
                } else {
                    $Msj = "Error : " . preg_replace("/[\r\n]/", '', $validation);
                    header("Location: facturas.php?Msj=$Msj");
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
                $Msj = "Error : " . $e->getMessage();
            }
        }
    }
}