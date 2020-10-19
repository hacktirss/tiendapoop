<?php

include_once ("CFDIComboBoxes.php");
include_once ("data/CiasDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "empresas.php?";

$objectDAO = new CiasDAO();
if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new CiasVO();
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"));
    }
    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setAlias($sanitize->sanitizeString("Alias"));
    $objectVO->setRfc($sanitize->sanitizeString("Rfc"));
    $objectVO->setDireccion($sanitize->sanitizeString("Direccion"));
    $objectVO->setNumeroext($sanitize->sanitizeString("Exterior"));
    $objectVO->setNumeroint($sanitize->sanitizeString("Interior"));
    $objectVO->setColonia($sanitize->sanitizeString("Colonia"));
    $objectVO->setMunicipio($sanitize->sanitizeString("Municipio"));
    $objectVO->setEstado($sanitize->sanitizeString("Estado"));
    $objectVO->setCodigo($sanitize->sanitizeString("Codigo"));
    $objectVO->setCorreo($sanitize->sanitizeString("Correo"));
    $objectVO->setTelefono($sanitize->sanitizeString("Telefono"));
    $objectVO->setContacto($sanitize->sanitizeString("Contacto"));
    $objectVO->setObservaciones($request->getAttribute("Observaciones"));
    $objectVO->setFacturacion($sanitize->sanitizeInt("Facturacion"));
    $objectVO->setRegimen($sanitize->sanitizeString("Regimen"));
    $objectVO->setSerie($sanitize->sanitizeString("Serie"));
    $objectVO->setClavesat($request->getAttribute("Clavesat"));
    $objectVO->setIva($sanitize->sanitizeFloat("Iva"));
    $objectVO->setRetencioninva($sanitize->sanitizeFloat("Retencioniva"));
    $objectVO->setIsr($sanitize->sanitizeFloat("Isr"));
    $objectVO->setIeps($sanitize->sanitizeFloat("Ieps"));


    //error_log(print_r($objectVO, TRUE));
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
