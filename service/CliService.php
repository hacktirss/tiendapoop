<?php

#Librerias
include_once ("data/CliDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "clientes.php?";
$usuarioSesion = getSessionUsuario();

$objectDAO = new CliDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new CliVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $objectVO->setNombre($sanitize->sanitizeString("Nombre"));
    $objectVO->setRfc($sanitize->sanitizeString("Rfc"));
    $objectVO->setAlias($sanitize->sanitizeString("Alias"));
    $objectVO->setDireccion($sanitize->sanitizeString("Direccion"));
    $objectVO->setNumeroext($sanitize->sanitizeString("Numeroext"));
    $objectVO->setNumeroint($sanitize->sanitizeString("Numeroint"));
    $objectVO->setColonia($sanitize->sanitizeString("Colonia"));
    $objectVO->setMunicipio($sanitize->sanitizeString("Municipio"));
    $objectVO->setEstado($sanitize->sanitizeString("Estado"));
    $objectVO->setCodigo($sanitize->sanitizeString("Codigo"));
    $objectVO->setTelefono($sanitize->sanitizeString("Telefono"));
    $objectVO->setCorreo($sanitize->sanitizeEmail("Correo"));
    $objectVO->setEnviarcorreo($sanitize->sanitizeString("Enviarcorreo"));
    $objectVO->setCuentaban($sanitize->sanitizeString("Cuentaban"));
    $objectVO->setPoliza($sanitize->sanitizeFloat("Poliza"));
    $objectVO->setActivo($sanitize->sanitizeString("Activo"));
    $objectVO->setContacto($sanitize->sanitizeString("Contacto"));
    $objectVO->setObservaciones($request->getAttribute("Observaciones"));
    $objectVO->setStatus($sanitize->sanitizeString("Status"));

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