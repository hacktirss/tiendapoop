<?php

#Librerias
include_once ("data/NotaEntradaDAO.php");
include_once ("data/NotaEntradaEquipoDAO.php");
include_once ("data/EgresoDAO.php");
include_once ("data/EquipoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "nota_eq_e.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$nameSession = "moduloEntradasEq";

if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, NULL);
    header("Location: egresos.php?criteria=ini&returnLink=nota_eq_ee.php&backLink=nota_eq_e.php");
}

if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$cValVar = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$objectDAO = new NotaEntradaEquipoDAO();
$egresoDAO = new EgresoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new NotaEntradaEquipoVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($cValVar)) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    }

    $objectVO->setFecha($sanitize->sanitizeString("Fecha"));
    $objectVO->setProveedor($sanitize->sanitizeString("Proveedor"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setFechafac($sanitize->sanitizeString("Fechafac"));
    $objectVO->setFactura($sanitize->sanitizeString("Factura"));
    $objectVO->setImporte($sanitize->sanitizeString("Importe"));
    $objectVO->setEgreso($sanitize->sanitizeInt("Egreso"));


    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $objectVO->setResponsable($UsuarioSesion->getId());
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Return = "nota_eq_ee.php?criteria=ini&busca=" . $id;
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
            $Clave = $sanitize->sanitizeString("Clave");
            if ($Clave === $CiaSesion->getMaster()) {

                $egresoVO = $egresoDAO->retrieve($objectVO->getEgreso(), "id", $UsuarioSesion->getCia());
                $egresoVO->setEntradaid(0);
                $egresoDAO->update($egresoVO);

                $objectVO->setStatus(StatusNotaEntradaEquipo::CANCELADA);

                if ($objectDAO->update($objectVO)) {
                    $Msj = utils\Messages::RESPONSE_VALID_CANCEL;

                    $deleteEquipos = "DELETE FROM equipos WHERE equipos.numero_entrada = $cValVar";
                    if (!$mysqli->query($updateInventario)) {
                        $Msj .= ". " . $mysqli->error;
                    }  else {
                        error_log("Equipos afectados: " . $mysqli->affected_rows);
                    }
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = utils\Messages::RESPONSE_PASSWORD_INCORRECT;
            }
        } elseif ($request->getAttribute("Boton") === "Cancelar nota") {
            $objectVO->setStatus(StatusNotaEntrada::CANCELADA);
            if ($objectDAO->update($objectVO)) {
                $Msj = utils\Messages::RESPONSE_VALID_CANCEL;
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

if ($request->hasAttribute("BotonD") && $request->getAttribute("BotonD") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
    $Return = "nota_eq_ee.php?";

    try {
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {

            $Grupo = $sanitize->sanitizeInt("Grupo");
            $Marca = $sanitize->sanitizeString("Marca");
            $Modelo = $sanitize->sanitizeString("Modelo");
            $Serie = $sanitize->sanitizeString("Serie");
            $Cantidad = $sanitize->sanitizeInt("Cantidad");
            $Costo = $sanitize->sanitizeFloat("Costo");
            $Precio = $sanitize->sanitizeFloat("Precio");

            $buscarSerie = "SELECT COUNT(id) registros FROM need WHERE id = $cValVar AND numero_serie = '$Serie'";
            $equipo = utils\ConnectionUtils::execSql($buscarSerie);

            if ($equipo["registros"] === "0") {
                for ($i = 0; $i < $Cantidad; $i++) {
                    $insertDetalle = "INSERT INTO need (id, grupo, marca, modelo, numero_serie, cantidad, costo, precio)
                            VALUES($cValVar, $Grupo, '$Marca', '$Modelo', '$Serie', 1, $Costo, $Precio)";

                    if ($mysqli->query($insertDetalle)) {
                        $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                    } else {
                        error_log($mysqli->error);
                        $Msj = utils\Messages::RESPONSE_ERROR;
                    }
                }

                TotalizaEntrada($objectDAO, $objectVO);
            } else {
                $Msj = "Favor de verificar! el numero de serie ya se capturo [$Serie]";
            }
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    }


    header("Location: $Return");
}


if ($request->hasAttribute("op")) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());

    try {
        if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
            $Return = "nota_eq_ee.php?";
            $cId = $request->getAttribute("cId");

            $deleteDetalle = "DELETE FROM need WHERE idnvo = $cId LIMIT 1";

            if ($mysqli->query($deleteDetalle)) {
                $Msj = utils\Messages::RESPONSE_VALID_DELETE;
                TotalizaEntrada($objectDAO, $objectVO);
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("op") === "cdr") {
            $productoDAO = new EquipoDAO();
            $egresoVO = $egresoDAO->retrieve($objectVO->getEgreso(), "id", $UsuarioSesion->getCia());

            $insertEquipos = "
                INSERT INTO equipos (id, cia, marca, descripcion, grupo, numero_serie, modelo, costo, precio, numero_entrada)
                SELECT need.idnvo, nee.cia, need.marca, need.descripcion, need.grupo, need.numero_serie, need.modelo,
                need.costo, need.precio, need.id
                FROM nee, need 
                WHERE TRUE AND nee.id = need.id AND nee.id = $cValVar";

            if ($mysqli->query($insertEquipos)) {
                $Msj = utils\Messages::MESSAGE_DEFAULT;

                $objectVO->setStatus(StatusNotaEntrada::CERRADA);
                $objectDAO->update($objectVO);


                $egresoVO->setEntradaid($cValVar);
                $egresoDAO->update($egresoVO);
            } else {
                error_log($mysqli->error);
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        }

        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    }

    header("Location: $Return");
}

/**
 * 
 * @param NotaEntradaEquipoDAO $objectDAO
 * @param NotaEntradaEquipoVO $objectVO
 */
function TotalizaEntrada($objectDAO, $objectVO) {

    $selectTotales = "SELECT COUNT(cantidad) cantidad,SUM(costo) costo FROM need WHERE id = " . $objectVO->getId();
    $result = utils\ConnectionUtils::execSql($selectTotales);

    $objectVO->setCantidad($result["cantidad"]);
    $objectVO->setCosto_entrada($result["costo"]);

    $objectDAO->update($objectVO);
}
