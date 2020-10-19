<?php

include_once ("data/PagoDAO.php");
include_once ("data/CliDAO.php");
include_once ("data/CxcDAO.php");
include_once ("data/ProveedorPACDAO.php");
include_once ("data/ReciboElectronicoPagoDetisa.php");
include_once ("data/CertificadoDAO.php");
include_once ("data/FcDAO.php");

include_once ("service/ListasCatalogo.php");

include_once ("cfdi33/pac/PACServiceFactory.php");
include_once ("cfdi33/pac/PACFactory.php");
include_once ("cfdi33/pac/PAC.php");
include_once ("cfdi33/SelloCFDI.php");
include_once ("cfdi33/validator/CFDIValidator.php");
include_once ("cfdi33/Certificates/Certificate.php");
include_once ("cfdi33/Certificates/Commons.php");
include_once ("cfdi33/Comprobante.php");
include_once ("pdf/PDFTransformer.php");
include_once ("pdf/PDFTransformerRP.php");

include_once ("CFDIComboBoxes.php");
include_once ("MailSender.php");

use com\softcoatl\cfdi\v33\schema\Comprobante;
use com\softcoatl\cfdi\v33\PACFactory;
use com\softcoatl\cfdi\v33\PACServiceFactory;
use com\softcoatl\cfdi\v33\SelloCFDI;
use com\softcoatl\utils\HTTPUtils;
use com\softcoatl\security\commons\Certificate;
use com\softcoatl\utils\IConnection;
use com\softcoatl\utils\Messages;
use com\softcoatl\utils as utils;

Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\\TimbreFiscalDigital");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\Pagos");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\INE");
Comprobante::registerAddenda("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\addenda\\Observaciones");

$mysqli = IConnection::getConnection();
$request = HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();

$Return = "pagos.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$certificadoDAO = new CertificadoDAO();
$objectDAO = new PagoDAO();
$cliDAO = new CliDAO();
$cxcDAO = new CxcDAO();

if ($request->hasAttribute("busca")) {
    HTTPUtils::setSessionValue(DETALLE, $sanitize->sanitizeString("busca"));
} elseif ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    HTTPUtils::setSessionValue(DETALLE, $request->getAttribute("id"));
}
$cValVar = HTTPUtils::getSessionValue(DETALLE);
$lBd = FALSE;


$pacDAO = new ProveedorPACDAO();
$ppac = $pacDAO->getActive();

$pac = PACFactory::getPAC($ppac->getUrl_webservice(), $ppac->getUsuario(), $ppac->getPassword(), $ppac->getClave_pac());
$pac->setUrlCancelacion($ppac->getUrl_cancelacion());
if ($pac instanceof cfdi33\SifeiPAC) {
    $pac->setIdEquipo($ppac->getClave_aux());
    $pac->setSerie("");
}

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== Messages::OP_NO_OPERATION_VALID) {
    $Msj = Messages::MESSAGE_NO_OPERATION;

    $SCliente = $request->getAttribute("Cliente");
    $explodeCliente = explode("|", $SCliente);
    $Cliente = trim($explodeCliente[0]);
    if (!empty($Cliente) && is_numeric($Cliente)) {
        $selectCli = "SELECT id value FROM cli WHERE id = " . $Cliente . " AND cia = " . $UsuarioSesion->getCia();
        if (($result = $mysqli->query($selectCli)) && ($rs = $result->fetch_array($result))) {
            $Cliente = $rs['value'];
        }
    }

    $objectVO = new PagoVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    }
    if ($objectVO->getStatus() === StatusPago::ABIERTA) {
        $objectVO->setCuenta($Cliente);
        $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
        $objectVO->setImporte($sanitize->sanitizeFloat("Importe"));
        $objectVO->setReferencia($sanitize->sanitizeString("Referencia"));
        $objectVO->setFormapago($sanitize->sanitizeString("Formapago"));
    }
    $objectVO->setFechap($sanitize->sanitizeString("Fechap"));
    $objectVO->setBanco($sanitize->sanitizeString("Banco"));

    $cliVO = $cliDAO->retrieve($objectVO->getCuenta(), "id", $UsuarioSesion->getCia());

    error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === Messages::OP_ADD) {
            $Folio = IncrementaId("ing", "folio");
            $objectVO->setFolio($Folio);
            $objectVO->setStatus(StatusPago::ABIERTA);
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = Messages::RESPONSE_VALID_CREATE;
                $Return = "pagose.php?busca=$id";
            } else {
                $Msj = Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === Messages::OP_UPDATE) {
            $Return = "pagose.php?";
            if ($objectDAO->update($objectVO)) {
                $Msj = Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === Messages::OP_CANCEL) {

            $clave = $sanitize->sanitizeString("Clave");

            if ($CiaSesion->getMaster() === $clave) {
                if (!empty($objectVO->getUuid()) && $objectVO->getUuid() !== PagoDAO::SIN_TIMBRAR) {
                    try {

                        $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");

                        //$Pfx = "certificado/pfx.pfx";
                        $service = PACServiceFactory::getPACService($pac);
                        error_log("Invocando servicio de cancelación " . $pac->getUrlCancelacion());
                        $acuse = $service->cancelaComprobantePFXBA($CiaSesion->getRfc(), $objectVO->getUuid(), $CiaSesion->getClavesat(), $certificadoVO->getCertificado_pfx());

                        if (TRUE) {
                            $reciboElectronico = new \com\detisa\detisa\ReciboElectronicoPagoDetisa($objectVO->getId(), $objectVO->getCia());
                            $reciboElectronico->acuse($objectVO->getUuid(), $acuse);
                            $reciboElectronico->cancel($objectVO->getId());
                        } else {
                            $Msj = "Error cancelando CFDI";
                        }
                    } catch (Exception $ex) {
                        $Msj = "Error cancelando CFDI " . $ex->getMessage();
                    }
                }

                $deleteCxc = "DELETE FROM cxc WHERE recibo = " . $objectVO->getId() . " AND tm = 'H'  AND cia = " . $UsuarioSesion->getCia();
                if (!$mysqli->query($deleteCxc)) {
                    error_log($mysqli->error);
                } else{
                    error_log("CXC afected: " . $mysqli->affected_rows);
                }

                $deletePag = "DELETE FROM ingd WHERE id = " . $objectVO->getId();
                if (!$mysqli->query($deletePag)) {
                    error_log($mysqli->error);
                } else{
                    error_log("INGD afected: " . $mysqli->affected_rows);
                }

                $updateIng = "UPDATE ing SET status = '" . StatusPago::CANCELADA . "' WHERE id = " . $objectVO->getId() . " AND cia = " . $UsuarioSesion->getCia();
                if ($mysqli->query($updateIng)) {
                    error_log("ING afected: " . $mysqli->affected_rows);
                    $Msj = Messages::MESSAGE_DEFAULT;
                } else {
                    error_log($mysqli->error);
                } 
                
            } else {
                $Msj = Messages::RESPONSE_PASSWORD_INCORRECT;
            }
        } elseif ($request->getAttribute("Boton") === Messages::OP_SEND_EMAIL) {
            $link = conectarse();

            $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");
            $pagoVO = $objectDAO->retrieve($cValVar);
            $cliVO = $cliDAO->retrieve($objectVO->getCuenta(), "id", $UsuarioSesion->getCia());

            $cliVO->setCorreo($request->getAttribute("Correo"));

            $selectFiles = "SELECT ing.id, ing.uuid, facturas.cfdi_xml, facturas.pdf_format 
                            FROM ing LEFT JOIN facturas ON ing.uuid = facturas.uuid 
                            WHERE TRUE 
                            AND ing.cia = " . $UsuarioSesion->getCia() . "
                            AND ing.id = " . $cValVar;
            $result = $link->query($selectFiles);
            $myrowsel = $result->fetch_array();

            $uuid = $myrowsel['uuid'];
            $xml = $myrowsel['cfdi_xml'];
            $comprobante = Comprobante::parse($xml);

            $pdf = PDFTransformerRP::getPDF($comprobante, "Recibo de Pago", "S", $certificadoVO->getLogo());
            if (MailSender::send($CiaSesion, $cliVO, $pagoVO->getId(), $uuid, $xml, $pdf)) {
                $Msj = "Se han enviado con éxito sus archivos XML y PDF";
            }
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}

if ($request->hasAttribute("Abono") && $request->hasAttribute("Factura")) {
    $Abono = $sanitize->sanitizeFloat("Abono");
    $Factura = $sanitize->sanitizeString("Factura");
    $cId = $sanitize->sanitizeInt("cId");
    $Return = "pagose.php?";

    try {
        $updateIngd = "UPDATE ingd SET importe = $Abono WHERE idnvo = $cId";

        $updateCxc = " 
                    UPDATE cxc SET importe = $Abono WHERE TRUE 
                    AND cxc.cia = " . $UsuarioSesion->getCia() . "
                    AND cxc.recibo = $cValVar AND cxc.referencia = 'F-$Factura' AND cxc.tm = 'H' LIMIT 1";

        if ($mysqli->query($updateIngd) && $mysqli->query($updateCxc)) {
            $Msj = Messages::MESSAGE_DEFAULT;
        } else {
            $Msj = Messages::RESPONSE_ERROR;
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}


if ($request->hasAttribute("Factura")) {
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
    $Return = "pagose.php?";
    try {
        $Factura = $sanitize->sanitizeString("Factura");
        $Importe = getSaldo($Factura, $objectVO->getCia(), $objectVO->getCuenta());
        if ($Factura > 0 && $Importe > 0) {
            $selectFactura = "SELECT * FROM fc WHERE TRUE AND cia = " . $UsuarioSesion->getCia() . " AND id = " . $Factura;
            $fc = utils\ConnectionUtils::execSql($selectFactura);
            /* Validamos el importe recibido, con lo que ya existe */
            $selectImporte = "SELECT IFNULL(SUM(ingd.importe),0) importe FROM ingd WHERE id = " . $cValVar;
            $imp = utils\ConnectionUtils::execSql($selectImporte);

            if ($Importe <= ($objectVO->getImporte() - $imp["importe"])) {
                $importe = $Importe;
            } else {
                $importe = $objectVO->getImporte() - $imp["importe"];
            }

            $cxcVO = new CxcVO();
            $cxcVO->setCia($UsuarioSesion->getCia());
            $cxcVO->setCuenta($objectVO->getCuenta());
            $cxcVO->setFecha($objectVO->getFechap());
            $cxcVO->setReferencia("F-" . $fc["folio"]);
            $cxcVO->setTm("H");
            $cxcVO->setFechav($objectVO->getFechap());
            $cxcVO->setConcepto("Pago de factura " . $fc["folio"]);
            $cxcVO->setImporte($importe);
            $cxcVO->setRecibo($cValVar);
            $cxcVO->setFactura($fc["id"]);

            if (($id = $cxcDAO->create($cxcVO)) > 0) {

                $insertIngd = "INSERT INTO ingd (id, referencia, importe) VALUES (" . $cValVar . ", '" . $fc['folio'] . "', " . $importe . ")";

                if ($mysqli->query($insertIngd)) {
                    $Msj = Messages::MESSAGE_DEFAULT;
                } else {
                    $Msj = Messages::RESPONSE_ERROR;
                }
            }
        } else {
            $Msj = Messages::MESSAGE_NO_OPERATION;
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}

if ($request->hasAttribute("Facturas")) {
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
    $Return = "pagose.php?";

    try {

        $Facturas = $request->getAttribute("Facturas");

        foreach ($Facturas as $Factura) {
            $Importe = getSaldo($Factura, $objectVO->getCia(), $objectVO->getCuenta());
            if ($Importe > 0) {
                $selectFactura = "SELECT * FROM fc WHERE TRUE AND cia = " . $UsuarioSesion->getCia() . " AND id = " . $Factura;
                $fc = utils\ConnectionUtils::execSql($selectFactura);
                /* Validamos el importe recibido, con lo que ya existe */
                $selectImporte = "SELECT IFNULL(SUM(ingd.importe),0) importe FROM ingd WHERE id = " . $cValVar;
                $imp = utils\ConnectionUtils::execSql($selectImporte);

                if ($Importe <= ($objectVO->getImporte() - $imp["importe"])) {
                    $importe = $Importe;
                } else {
                    $importe = $objectVO->getImporte() - $imp["importe"];
                }

                $cxcVO = new CxcVO();
                $cxcVO->setCia($UsuarioSesion->getCia());
                $cxcVO->setCuenta($objectVO->getCuenta());
                $cxcVO->setFecha($objectVO->getFechap());
                $cxcVO->setReferencia("F-" . $fc["folio"]);
                $cxcVO->setTm("H");
                $cxcVO->setFechav($objectVO->getFechap());
                $cxcVO->setConcepto("Pago de factura " . $fc["folio"]);
                $cxcVO->setImporte($importe);
                $cxcVO->setRecibo($cValVar);
                $cxcVO->setFactura($fc["id"]);

                if (($id = $cxcDAO->create($cxcVO)) > 0) {

                    $insertIngd = "INSERT INTO ingd (id, referencia, importe) VALUES (" . $cValVar . ", '" . $fc['folio'] . "', " . $importe . ")";

                    if ($mysqli->query($insertIngd)) {
                        $Msj = Messages::MESSAGE_DEFAULT;
                    } else {
                        $Msj = Messages::RESPONSE_ERROR;
                    }
                }
            } else {
                $Msj = Messages::MESSAGE_NO_OPERATION;
            }
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}

if ($request->hasAttribute("op")) {
    $Msj = Messages::MESSAGE_NO_OPERATION;
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
    $cliVO = $cliDAO->retrieve($objectVO->getCuenta(), "id", $UsuarioSesion->getCia());

    try {
        if ($request->getAttribute("op") === Messages::OP_CLOSE || $request->getAttribute("op") === "CerrarTimbrar") {
            $selectIng = "
                        SELECT ing.cuenta, (ing.importe - IFNULL(SUM(ingd.importe),0)) dif, ing.referencia, ing.fechap 
                        FROM ing  
                        LEFT JOIN ingd ON ing.id = ingd.id 
                        WHERE TRUE
                        AND ing.cia = " . $UsuarioSesion->getCia() . "
                        AND ing.id = " . $busca;
            $cxc = utils\ConnectionUtils::execSql($selectIng);

            if ($request->hasAttribute("dif")) {

                $ref = empty($cxc['referencia']) ? "F-99999" : $cxc['referencia'];

                $cxcVO = new CxcVO();
                $cxcVO->setCia($UsuarioSesion->getCia());
                $cxcVO->setCuenta($objectVO->getCuenta());
                $cxcVO->setFecha($objectVO->getFechap());
                $cxcVO->setReferencia($ref);
                $cxcVO->setTm("H");
                $cxcVO->setFechav($objectVO->getFechap());
                $cxcVO->setConcepto("Abono a favor por diferencia " . $cValVar);
                $cxcVO->setImporte($cxc["dif"]);
                $cxcVO->setRecibo($cValVar);

                if (($id = $cxcDAO->create($cxcVO)) > 0) {

                    $insertIngd = "INSERT INTO ingd (id, referencia, importe) VALUES (" . $cValVar . ", 'F-99999', " . $cxc['dif'] . ")";

                    if (!$mysqli->query($insertIngd)) {
                        $Msj = Messages::RESPONSE_ERROR;
                    }
                }
            }

            $objectVO->setStatus(StatusPago::CERRADA);
            if ($objectDAO->update($objectVO)) {
                $Msj = Messages::MESSAGE_DEFAULT;
                $updateBancos = "UPDATE bancos SET saldo = saldo + " . $objectVO->getImporte() . " WHERE id = " . $objectVO->getBanco() . " AND cia = " . $UsuarioSesion->getCia() . "";
                if (!$mysqli->query($updateBancos)) {
                    $Msj = Messages::RESPONSE_ERROR;
                }
                if ($request->getAttribute("op") === "CerrarTimbrar") {
                    $lBd = TRUE;
                    $Return = "";
                }
            } else {
                $Msj = Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("op") === "del") {
            $cId = $sanitize->sanitizeInt("cId");
            $Return = "pagose.php?";

            $selectIngd = "SELECT CONCAT('F-',referencia) referencia FROM ingd WHERE idnvo = " . $cId;
            $result = utils\ConnectionUtils::execSql($selectIngd);

            $deleteCxc = "DELETE FROM cxc WHERE recibo = $cValVar AND tm = 'H' AND referencia = '" . $result["referencia"] . "' AND cia = " . $UsuarioSesion->getCia() . " LIMIT 1";

            $deleteIngd = "DELETE FROM ingd WHERE ingd.id = $cValVar AND idnvo = $cId LIMIT 1";

            if ($mysqli->query($deleteCxc) && $mysqli->query($deleteIngd)) {
                $Msj = Messages::MESSAGE_DEFAULT;
            } else {
                $Msj = Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("op") === "Timbrar") {
            $lBd = TRUE;
            $Return = "";
        } elseif ($request->getAttribute("op") === "Genera") {
            error_log("Inciando proceso de timbrado");
            $reciboElectronico = new com\detisa\detisa\ReciboElectronicoPagoDetisa($cValVar, $UsuarioSesion->getCia());
            error_log(print_r($reciboElectronico, TRUE));

            $certificadoDAO = new CertificadoDAO();
            $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");

            $pacDAO = new ProveedorPACDAO();
            $ppac = $pacDAO->getActive();

            $pac = PACFactory::getPAC($ppac->getUrl_webservice(), $ppac->getUsuario(), $ppac->getPassword(), $ppac->getClave_pac());
            if ($pac instanceof cfdi33\SifeiPAC) {
                $pac->setIdEquipo($ppac->getClave_aux());
                $pac->setSerie("");
            }

            $service = PACServiceFactory::getPACService($pac);
            $csd = new Certificate($certificadoVO->getCertificado(), $certificadoVO->getLlave());
            $sello = new SelloCFDI($csd);

            $sello->sellaComprobante($reciboElectronico->getComprobante());

            $DOMFactura = $reciboElectronico->getComprobante()->asXML();
            $xmlFactura = $DOMFactura->saveXML();

            try {

                error_log("Invocando Servicio de facturación");
                $xmlCFDI = $service->timbraComprobante($xmlFactura);
                error_log($xmlCFDI);

                if ($xmlCFDI) {

                    $comprobanteTimbrado = Comprobante::parse($xmlCFDI);
                    $pdfCFDI = PDFTransformerRP::getPDF($comprobanteTimbrado, "Recibo de Pago", "S", $certificadoVO->getLogo());

                    $reciboElectronico->setComprobanteTimbrado($comprobanteTimbrado);
                    $reciboElectronico->setRepresentacionImpresa($pdfCFDI);
                    $reciboElectronico->setXmlTimbrado($xmlCFDI);

                    $reciboElectronico->update($cValVar);
                    $reciboElectronico->save($cValVar, $pac->getPac());

                    file_put_contents("fae/archivos/" . $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID() . ".xml", $xmlCFDI);
                    file_put_contents("fae/archivos/" . $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID() . ".pdf", $pdfCFDI);

                    if ($Enviar && !empty($cli->getCorreo())) {
                        if (MailSender::send($CiaSesion, $cliVO, $comprobanteTimbrado->getFolio(), $comprobanteTimbrado->getTimbreFiscalDigital()->getUUID(), $xml, $pdfCFDI)) {
                            $Msj = "Se han enviado con éxito sus archivos XML y PDF";
                        }
                    }

                    $Msj = "Tu recibo electrónico de pago ha sido timbrado con exito";
                } else {
                    $Msj = $service->getError();
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
                $Msj = "Error : " . $e->getMessage();
            }
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        if (!empty($Return)) {
            $Return .= "&Msj=" . urlencode($Msj);
            header("Location: $Return");
        }
    }
}

/**
 * 
 * @param int $Factura
 * @param int $Cia
 * @param int $Cliente
 * @return int
 */
function getSaldo($Factura, $Cia, $Cliente) {
    $mysqli = conectarse();
    $resultado = 0;
    $selectSaldo = "
        SELECT * FROM (
            SELECT cxc.factura, cxc.referencia,
            ROUND(SUM(IF(cxc.tm = 'H',cxc.importe * -1,cxc.importe)),2) saldo
            FROM cxc 
            WHERE TRUE AND cxc.cia = $Cia AND cxc.cuenta = $Cliente 
            GROUP BY cxc.referencia
            ORDER BY cxc.referencia
        ) sub 
        WHERE TRUE
        AND factura = $Factura
        HAVING saldo > 0";

    if (($result = $mysqli->query($selectSaldo)) && ($rs = $result->fetch_array())) {
        $resultado = $rs["saldo"];
    } else{
        error_log("Not Found! " . $mysqli->error);
    }

    return $resultado;
}

function contains($original, $busqueda) {
    return strpos(strtoupper($original), strtoupper($busqueda)) !== FALSE;
}