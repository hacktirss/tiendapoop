<?php

#Librerias
include_once ("data/NotaSalidaDAO.php");
include_once ("data/EgresoDAO.php");
include_once ("data/ProductoDAO.php");
include_once ("data/EquipoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "nota_pro_s.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$nameSession = "moduloSalidasEq";

if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("id"));
}

if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$cValVar = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$objectDAO = new NotaSalidaDAO();
$egresoDAO = new EgresoDAO();
$equipoDAO = new EquipoDAO();
$productoDAO = new ProductoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new NotaSalidaVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($cValVar)) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    }

    $objectVO->setFecha($sanitize->sanitizeString("Fecha"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setCliente($sanitize->sanitizeString("Cliente"));

    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $objectVO->setResponsable($UsuarioSesion->getId());
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Return = "nota_pro_se.php?criteria=ini&busca=" . $id;
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

                if ($objectVO->getStatus() === StatusNotaSalida::CERRADA) {
                    $updateProductos = "
                            UPDATE inv, nsd
                            SET inv.existencia = inv.existencia + nsd.cantidad  
                            WHERE TRUE 
                            AND inv.id = nsd.producto AND nsd.id = " . $objectVO->getId() . " AND nsd.tipo = " . TipoNotaSalida::PRODUCTO . " 
                            AND inv.cia = -" . $objectVO->getCia();
                    if (!$mysqli->query($updateProductos)) {
                        $Msj .= ". " . $mysqli->error;
                    } else {
                        error_log("Productos afectados: " . $mysqli->affected_rows);
                    }

                    $updateEquipos = "
                            UPDATE nsd, equipos SET equipos.cia = ABS(equipos.cia) WHERE TRUE 
                            AND equipos.id = nsd.producto
                            AND nsd.id = " . $objectVO->getId() . " AND nsd.tipo = " . TipoNotaSalida::EQUIPO . " 
                            AND equipos.cia = " . $objectVO->getCia();
                    if (!$mysqli->query($updateProductos)) {
                        $Msj .= ". " . $mysqli->error;
                    } else {
                        error_log("Equipos afectados: " . $mysqli->affected_rows);
                    }
                }

                $objectVO->setStatus(StatusNotaSalida::CANCELADA);

                if ($objectDAO->update($objectVO)) {
                    $Msj = utils\Messages::RESPONSE_VALID_CANCEL;
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = utils\Messages::RESPONSE_PASSWORD_INCORRECT;
            }
        } elseif ($request->getAttribute("Boton") === "Cancelar nota") {
            $objectVO->setStatus(StatusNotaSalida::CANCELADA);
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

if ($request->hasAttribute("BotonP") && $request->getAttribute("BotonP") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $Return = "nota_pro_se.php?";
    try {
        $Producto = $sanitize->sanitizeInt("Producto");
        $Cantidad = $sanitize->sanitizeInt("Cnt");
        $Costo = $sanitize->sanitizeFloat("Costo");
        if ($request->getAttribute("BotonP") === utils\Messages::OP_ADD) {
            $Tipo = TipoNotaSalida::PRODUCTO;

            $insertDetalle = "INSERT INTO nsd (id, producto, cantidad, costo, tipo)
                            VALUES($cValVar, $Producto, $Cantidad, $Costo, $Tipo)";

            if ($mysqli->query($insertDetalle)) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
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

if ($request->hasAttribute("BotonE") && $request->getAttribute("BotonE") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;
    $Return = "nota_pro_se.php?";
    try {
        $Equipo = $sanitize->sanitizeInt("Equipo");
        $Cantidad = $sanitize->sanitizeInt("Cnt");
        $Costo = $sanitize->sanitizeFloat("Costo");
        if ($request->getAttribute("BotonE") === utils\Messages::OP_ADD) {
            $Tipo = TipoNotaSalida::EQUIPO;

            $objectoVO = $equipoDAO->retrieve($Equipo, "id", $UsuarioSesion->getCia());

            $insertDetalle = "INSERT INTO nsd (id, tipo, producto, grupo, marca, numero_serie, modelo, cantidad, costo, precio)
                            VALUES($cValVar, $Tipo, $Equipo, '" . $objectoVO->getGrupo() . "', 
                            '" . $objectoVO->getMarca() . "', '" . $objectoVO->getNumero_serie() . "', 
                            '" . $objectoVO->getModelo() . "', $Cantidad, $Costo, " . $objectoVO->getCosto() . ")";

            if ($mysqli->query($insertDetalle)) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
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


if ($request->hasAttribute("op")) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
        $Return = "nota_pro_se.php?";
        $cId = $request->getAttribute("cId");

        $deleteDetalle = "DELETE FROM nsd WHERE idnvo = $cId LIMIT 1";

        if ($mysqli->query($deleteDetalle)) {
            $Msj = utils\Messages::RESPONSE_VALID_DELETE;
        } else {
            $Msj = utils\Messages::RESPONSE_ERROR;
        }
    } elseif ($request->getAttribute("op") === "cdr") {
        $productoDAO = new ProductoDAO();
        $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
        $updateProductos = "
                            UPDATE inv, nsd
                            SET inv.existencia = inv.existencia - nsd.cantidad  
                            WHERE TRUE 
                            AND inv.id = nsd.producto AND nsd.id = " . $objectVO->getId() . " AND nsd.tipo = " . TipoNotaSalida::PRODUCTO . " 
                            AND inv.cia = " . $objectVO->getCia();
        if (!$mysqli->query($updateProductos)) {
            $Msj .= ". " . $mysqli->error;
        } else {
            error_log("Productos afectados: " . $mysqli->affected_rows);
        }

        $updateEquipos = "
                            UPDATE nsd, equipos SET equipos.cia = -equipos.cia WHERE TRUE 
                            AND equipos.id = nsd.producto
                            AND nsd.id = " . $objectVO->getId() . " AND nsd.tipo = " . TipoNotaSalida::EQUIPO . " 
                            AND equipos.cia = " . $objectVO->getCia();
        if (!$mysqli->query($updateEquipos)) {
            $Msj .= ". " . $mysqli->error;
        } else {
            error_log("Equipos afectados: " . $mysqli->affected_rows);
        }

        $objectVO->setStatus(StatusNotaSalida::CERRADA);
        $objectDAO->update($objectVO);

        $Msj = utils\Messages::MESSAGE_DEFAULT;
    }


    header("Location: $Return");
}

