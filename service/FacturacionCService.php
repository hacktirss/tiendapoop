<?php

include_once ("data/FacturaDetisa.php");
include_once ("data/NotaDeCreditoDetisa.php");
include_once ("data/CiaDAO.php");
include_once ("data/FcDAO.php");
include_once ("data/NcDAO.php");
include_once ("data/ProveedorPACDAO.php");
include_once ("data/RelacionesCfdiDAO.php");
include_once ("data/CertificadoDAO.php");

include_once ("cfdi33/pac/PACServiceFactory.php");
include_once ("cfdi33/pac/PACFactory.php");
include_once ("cfdi33/pac/PAC.php");
include_once ("cfdi33/validator/CFDIValidator.php");

include_once ("CFDIComboBoxes.php");

use com\softcoatl\cfdi\v33\PACServiceFactory;
use com\softcoatl\cfdi\v33\PACFactory;
use com\softcoatl\cfdi\v33\validation\CFDIValidator;
use com\softcoatl\utils as utils;
use com\softcoatl\utils\HTTPUtils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$nameSession = "moduloFacturacion";

if ($request->hasAttribute("busca")) {
    HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$mysqli = conectarse();

$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();
$Tipo = HTTPUtils::getSessionBiValue($nameSession, "Paso"); //Tipo de factura o nota de credito
$busca = HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$Return = "facturas.php?";

$Id = 54;
$Titulo = "Cancelación de CFDI";

if ($Tipo == 1) {
    $Tabla = "fc";
    $Tablad = "fcd";
} else {
    $Titulo = "Detalle de nota de crédito";
    $Tabla = "nc";
    $Tablad = "ncd";
}

$Tabla = $Tipo == 1 ? $Tabla = "fc" : $Tabla = "nc";

$certificadoDAO = new CertificadoDAO();
$relacionesDAO = new RelacionesCfdiDAO();
$relaciones = $relacionesDAO->retrieve($busca, $Tipo == 1 ? 1 : 2);


if ($request->hasAttribute("Boton")) {

    $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");
    $Clave = $request->getAttribute("Clave");

    if ($request->getAttribute("Boton") === utils\Messages::OP_CANCEL && $CiaSesion->getMaster() === $Clave) {

        $selectFc = "
                    SELECT fc.fecha, fc.cliente, fc.cantidad, fc.iva, fc.ieps, fc.importe,
  		    cli.rfc, cli.nombre, fc.uuid, cli.direccion, cli.colonia, cli.municipio, cli.codigo
  		    FROM cli JOIN $Tabla AS fc ON fc.cliente = cli.id AND cli.cia = " . $UsuarioSesion->getCia() . "
                    WHERE fc.id = $busca";

        $CpoA = utils\ConnectionUtils::getRowsFromQuery($selectFc, $mysqli);
        $Cpo = $CpoA[0];

        if ($Cpo[uuid] === "-----") {
            $updateTabla = "UPDATE $Tabla SET cantidad = 0, importe = 0, iva = 0, ieps = 0, total = 0, status = 'Cancelada S/T' WHERE id = " . $busca;

            if (!$mysqli->query($updateTabla)) {
                error_log($mysqli->error);
            }

            $deleteDetalle = "DELETE FROM $Tablad  WHERE id = '$busca'";
            if (!$mysqli->query($deleteDetalle)) {
                error_log(mysqli_error($mysqli));
            }
            $Msj = "Tu factura No. " . $busca . " ha sido cancelada con exito(aun no se habia timbrado)";
        } else {


            try {
                $pacDAO = new ProveedorPACDAO();
                $ppac = $pacDAO->getActive();
                $pac = PACFactory::getPAC($ppac->getUrl_webservice(), $ppac->getUsuario(), $ppac->getPassword(), $ppac->getClave_pac());
                if ($pac instanceof cfdi33\SifeiPAC) {
                    $pac->setIdEquipo($ppac->getClave_aux());
                    $pac->setSerie("");
                }
                $pac->setUrlCancelacion($ppac->getUrl_cancelacion());

                $Pfx = file_get_contents("certificado/pfx.pfx");

                $service = PACServiceFactory::getPACService($pac);
                error_log("Invocando servicio de cancelación " . $pac->getUrlCancelacion());
                $acuse = $service->cancelaComprobantePFXBA($certificadoVO->getRfc(), $Cpo["uuid"], $certificadoVO->getClave(), $certificadoVO->getCertificado_pfx());
                error_log("Acuase" . print_r($service->getError(), TRUE));

                $facturaDetisa = new com\detisa\detisa\FacturaDetisa($busca, $UsuarioSesion->getCia());
                if ($Tipo != 1) {
                    $facturaDetisa = new \com\detisa\detisa\NotaDeCreditoDetisa($busca, $UsuarioSesion->getCia());
                }

                if ($acuse) {
                    $facturaDetisa->cancel($busca);
                    $facturaDetisa->acuse($Cpo["uuid"], $acuse);

                    $selectCxc = "SELECT CONCAT('F-',folio)  folio,cliente FROM fc WHERE id = '$busca' AND cia = " . $UsuarioSesion->getCia();
                    if (($result = $mysqli->query($selectCxc)) && ($row = $result->fetch_array())) {
                        $cxc = $row;

                        if ($Tipo == 1) {
                            $deleteCxc = "DELETE FROM cxc WHERE referencia = '" . $cxc["folio"] . "' AND tm = 'C' AND cuenta = ' " . $cxc["cliente"] . "' AND cia = " . $UsuarioSesion->getCia();
                            if (!$mysqli->query($deleteCxc)) {
                                error_log(mysqli_error($mysqli));
                            }
                            $Msj = "Factura cancelada con exito!";
                        } else {
                            $selectCxc = "SELECT * FROM cxc WHERE referencia = '" . $cxc["folio"] . "' AND tm = 'H' AND cia = " . $UsuarioSesion->getCia();
                            if (($result_ = $mysqli->query($selectCxc)) && ($row_ = $result_->fetch_array())) {
                                $rg = $row_;
                                $deleteCxc = "DELETE FROM ing WHERE id = '" . $rg["recibo"] . "' AND cia = " . $UsuarioSesion->getCia();
                                $deleteIng = "DELETE FROM cxc WHERE id = '" . $rg[id] . "' AND cia = " . $UsuarioSesion->getCia();
                                if (!$mysqli->query($deleteIng) || !$mysqli->query($deleteCxc)) {
                                    error_log(mysqli_error($mysqli));
                                }
                            }
                        }
                    }
                    $Msj .= " , " . $Msj2 . ", " . $Msj3 . ", " . $Msj4;
                } elseif (contains($service->getError(), "202")) {
                    $facturaDetisa->cancel($busca);
                    $facturaDetisa->acuse($Cpo["uuid"], $acuse);
                    $Msj = $service->getError();
                } else {
                    $Msj = $service->getError();
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    } else {
        $Msj = "Lo siento!!! su clave no coincide";
    }


    header("Location: " . $Return . "Msj=" . urldecode($Msj));
}

function contains($original, $busqueda) {
    return strpos(strtoupper($original), strtoupper($busqueda)) !== FALSE;
}

$selectFactura = "
            SELECT 
            fc.fecha, fc.cantidad, fc.iva, fc.importe, cli.rfc, fc.total,
            facturas.fecha_emision fecha, facturas.fecha_timbrado, fc.status, fc.cliente,
            CONCAT( IF( fc.serie IS NOT NULL AND fc.serie <> '', CONCAT( fc.serie, ' - ' ), ''), fc.folio ) foliofactura,
            facturas.receptor, facturas.emisor, cli.nombre, fc.uuid, 
            IF( ExtractValue(facturas.cfdi_xml, '/cfdi:Comprobante/@Total')= '', ExtractValue(facturas.cfdi_xml, '/cfdi:Comprobante/@total'), ExtractValue(facturas.cfdi_xml, '/cfdi:Comprobante/@Total') ) cfditotal,
            IF( facturas.version IS NOT NULL AND facturas.version = '3.3', ExtractValue(facturas.cfdi_xml, '/cfdi:Comprobante/@Sello'), ExtractValue(facturas.cfdi_xml, '/cfdi:Comprobante/@sello')) sello
            FROM $Tabla AS fc
            JOIN cli ON fc.cliente = cli.id AND cli.cia = " . $UsuarioSesion->getCia() . "
            LEFT JOIN facturas ON facturas.uuid = fc.uuid 
            WHERE fc.id = " . $busca;

$Cpo = $mysqli->query($selectFactura)->fetch_array();

if (!empty($Cpo["uuid"]) && $Cpo["uuid"] !== FcDAO::SINTIMBRAR) {
    $expresion = implode("&", [
        "id=" . $Cpo['uuid'],
        "re=" . $Cpo['emisor'],
        "rr=" . $Cpo['receptor'],
        "tt=" . number_format($Cpo['cfditotal'], 2, '.', ''),
        "fe=" . substr($Cpo['sello'], - 8)]);
    $statusCFDI = CFDIValidator::CallAPI($expresion);
    $verificacionURL = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?" . $expresion;
    error_log(print_r($statusCFDI, TRUE));
}


$relacionados = "";
if ((!empty($statusCFDI->EsCancelable) && contains($statusCFDI->EsCancelable, "No Cancelable")) || contains($statusCFDI->Estado, "No Encontrado")) {

    $sqlRelacionado = " 
            SELECT * 
            FROM ( 
                SELECT fc.id, fc.uuid, relacion.uuid uuidr 
                FROM $Tabla AS fc
                JOIN facturas ON fc.uuid = facturas.uuid 
                JOIN facturas relacion ON ExtractValue(relacion.cfdi_xml, '/cfdi:Comprobante/cfdi:CfdiRelacionados/cfdi:CfdiRelacionado/@UUID') = fc.uuid 
                WHERE fc.id = '$busca'  AND relacion.cfdi_xml LIKE CONCAT('%', fc.uuid, '%')
            ) sub
            JOIN ( 
                SELECT 'FC' tabla, id idr, uuid FROM fc  WHERE uuid IS NOT NULL AND uuid NOT IN ('', '-----') 
                UNION ALL 
                SELECT 'NC' tabla, id idr, uuid FROM nc WHERE uuid IS NOT NULL AND uuid NOT IN ('', '-----') 
                UNION ALL 
                SELECT 'PG' tabla, id idr, uuid FROM ing WHERE uuid IS NOT NULL AND uuid NOT IN ('', '-----') 
            ) rel ON rel.uuid =  sub.uuidr";
    error_log($sqlRelacionado);
    $qryRelacionado = $mysqli->query($sqlRelacionado);
    while ($rsRelacionado = $qryRelacionado->fetch_array()) {
        if (!empty($rsRelacionado['uuidr'])) {
            if ($rsRelacionado['tabla'] == 'PG') {
                $relacionados .= "<a href=\"pagose.php?busca=" . $rsRelacionado['idr'] . "\">Cancelar Pago " . $rsRelacionado['uuidr'] . "</a><br />";
            } else if ($rsRelacionado['tabla'] == 'NC') {
                $relacionados .= "<a href=\"canfactura332.php?busca=" . $rsRelacionado['idr'] . "&tabla=4\">Cancelar Nota de Crédito " . $rsRelacionado['uuidr'] . "</a><br />";
            } else if ($rsRelacionado['tabla'] == 'FC') {
                $relacionados .= "<a href=\"canfactura332.php?busca=" . $rsRelacionado['idr'] . "&tabla=1\">Cancelar Factura " . $rsRelacionado['uuidr'] . "</a><br />";
            }
        }
    }
}