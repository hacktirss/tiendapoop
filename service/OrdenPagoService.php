<?php

include_once ("data/OrdenPagoDAO.php");
include_once ("data/CiaDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();

$Return = "ordpagos.php?";

$objectDAO = new OrdenPagoDAO();
$ciaDAO = new CiaDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new OrdenPagoVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    } else{
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $objectVO->setFecha($sanitize->sanitizeString("Fecha"));
    $objectVO->setProveedor($sanitize->sanitizeInt("Proveedor"));
    $objectVO->setRubro($sanitize->sanitizeString("Rubro"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setSolicito($sanitize->sanitizeString("Solicito"));
    $objectVO->setCotizacion($sanitize->sanitizeFloat("Cotizacion"));
    $Importe = $sanitize->sanitizeFloat("Importe") == 0 ? $sanitize->sanitizeFloat("Total") : $sanitize->sanitizeFloat("Importe");
    $objectVO->setImporte($Importe);
    $objectVO->setIva($sanitize->sanitizeFloat("Iva"));
    $objectVO->setIva_ret($sanitize->sanitizeFloat("IvaRet"));
    $objectVO->setIsr($sanitize->sanitizeFloat("Isr"));
    $objectVO->setHospedaje($sanitize->sanitizeFloat("Hospedaje"));
    $objectVO->setTotal($sanitize->sanitizeFloat("Total"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setStatus($sanitize->sanitizeString("Status"));

    error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            if ($objectDAO->update($objectVO)) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_CANCEL) {
            $ciaVO = $ciaDAO->retrieveFields("master");
            $clave = $sanitize->sanitizeString("Clave");

            if ($clave === $ciaVO->getMaster()) {
                if ($objectVO->getPagonumero() == 0) {
                    $objectVO->setImporte(0);
                    $objectVO->setIva(0);
                    $objectVO->setTotal(0);
                    $objectVO->setStatus(StatusOrdenPago::CANCELADA);
                    if (!$objectDAO->update($objectVO)) {
                        $Msj = utils\Messages::RESPONSE_ERROR;
                    } else {
                        $Msj = utils\Messages::MESSAGE_DEFAULT;
                    }
                } else {
                    $Msj = "No se puede cancelar este movimiento ya que tiene un egreso asociado!";
                }
            } else {
                $Msj = utils\Messages::RESPONSE_PASSWORD_INCORRECT;
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
