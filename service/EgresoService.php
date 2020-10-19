<?php

include_once ("data/OrdenPagoDAO.php");
include_once ("data/EgresoDAO.php");
include_once ("data/CiaDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();
$Return = "egresos.php?";

$objectDAO = new EgresoDAO();
$ordenPagoDAO = new OrdenPagoDAO();
$ciaDAO = new CiaDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new EgresoVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id" , $UsuarioSesion->getCia());
    } else{
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $ordenPagoVO = $ordenPagoDAO->retrieve($sanitize->sanitizeInt("Ordendepago"), "id" , $UsuarioSesion->getCia());

    $objectVO->setFecha($sanitize->sanitizeString("Fecha"));
    $objectVO->setOrdendepago($sanitize->sanitizeInt("Ordendepago"));
    $objectVO->setBanco($sanitize->sanitizeInt("Banco"));
    $objectVO->setFormadepago($sanitize->sanitizeString("Formadepago"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setPagoreal($sanitize->sanitizeFloat("Pagoreal"));
    $objectVO->setOtropago($sanitize->sanitizeFloat("Otropago"));

    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                $ordenPagoVO->setPagonumero($id);
                if (!$ordenPagoDAO->update($ordenPagoVO)) {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
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
                if ($objectVO->getEntradaid() == 0) {
                    $ordenPagoVO->setPagonumero(0);
                    if ($ordenPagoDAO->update($ordenPagoVO)) {
                        $Msj = utils\Messages::MESSAGE_DEFAULT;
                        $objectVO->setPagoreal(0);
                        $objectVO->setOtropago(0);
                        $objectVO->setOrdendepago(0);
                        if (!$objectDAO->update($objectVO)) {
                            $Msj = utils\Messages::RESPONSE_ERROR;
                        }
                    } else {
                        $Msj = utils\Messages::RESPONSE_ERROR;
                    }
                } else {
                    $Msj = "No se puede cancelar este movimiento ya que tiene una entrada asociada!";
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
