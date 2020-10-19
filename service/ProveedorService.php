<?php

#Librerias
include_once ("data/ProveedorDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "proveedores.php?";
$usuarioSesion = getSessionUsuario();

$objectDAO = new ProveedorDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new ProveedorVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setDireccion($sanitize->sanitizeString("Direccion"));
    $objectVO->setColonia($sanitize->sanitizeString("Colonia"));
    $objectVO->setMunicipio($sanitize->sanitizeString("Municipio"));
    $objectVO->setEstado($sanitize->sanitizeString("Estado"));
    $objectVO->setAlias($sanitize->sanitizeString("Alias"));
    $objectVO->setTelefono($sanitize->sanitizeString("Telefono"));
    $objectVO->setActivo($sanitize->sanitizeString("Activo"));
    $objectVO->setContacto($sanitize->sanitizeString("Contacto"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setTipodepago($sanitize->sanitizeString("Tipodepago"));
    $objectVO->setCodigo($sanitize->sanitizeString("Codigo"));
    $objectVO->setRfc($sanitize->sanitizeString("Rfc"));
    $objectVO->setCorreo($sanitize->sanitizeString("Correo"));
    $objectVO->setNumeroint($sanitize->sanitizeString("Numeroint"));
    $objectVO->setNumeroext($sanitize->sanitizeString("Numeroext"));
    $objectVO->setEnviarcorreo($sanitize->sanitizeString("Enviarcorreo"));
    $objectVO->setCuentaban($sanitize->sanitizeString("Cuentaban"));
    $objectVO->setProveedorde($sanitize->sanitizeString("Proveedorde"));

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

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    }


    header("Location: $Return");
}