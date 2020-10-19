<?php

#Librerias
include_once ("data/NotaEntradaDAO.php");
include_once ("data/EgresoDAO.php");
include_once ("data/ProductoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "nota_pro_e.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$nameSession = "moduloEntradasPro";

if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, NULL);
    header("Location: egresos.php?criteria=ini&returnLink=nota_pro_ee.php&backLink=nota_pro_e.php");
}

if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$cValVar = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$objectDAO = new NotaEntradaDAO();
$egresoDAO = new EgresoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new NotaEntradaVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($cValVar)) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    }

    $objectVO->setFecha_entra($sanitize->sanitizeString("Fecha_entra"));
    $objectVO->setProveedor($sanitize->sanitizeString("Proveedor"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setFechafac($sanitize->sanitizeString("Fechafac"));
    $objectVO->setFactura($sanitize->sanitizeString("Factura"));
    $objectVO->setImporte($sanitize->sanitizeString("Importe"));
    $objectVO->setEgreso($sanitize->sanitizeInt("Egreso"));
    $objectVO->setOrdpago($sanitize->sanitizeInt("Ordpago"));


    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $objectVO->setResponsable($UsuarioSesion->getId());
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Return = "nota_pro_ee.php?criteria=ini&busca=" . $id;
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

                if ($objectVO->getStatus() === StatusNotaEntrada::CERRADA) {
                    $updateInventario = "
                            UPDATE inv,ned
                            SET inv.existencia = inv.existencia - ned.cantidad  
                            WHERE TRUE 
                            AND inv.id = ned.producto AND ned.id = " . $objectVO->getId() . " 
                            AND inv.cia = " . $objectVO->getCia();
                    if (!$mysqli->query($updateInventario)) {
                        $Msj .= ". " . $mysqli->error;
                    } else {
                        error_log("Productos afectados: " . $mysqli->affected_rows);
                    }
                }

                $objectVO->setStatus(StatusNotaEntrada::CANCELADA);

                if ($objectDAO->update($objectVO)) {
                    $Msj = utils\Messages::RESPONSE_VALID_CANCEL;
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
    $Return = "nota_pro_ee.php?";
    try {
        if ($request->getAttribute("BotonD") === utils\Messages::OP_ADD) {
            $Producto = $sanitize->sanitizeInt("Producto");
            $Cantidad = $sanitize->sanitizeInt("Cnt");
            $Costo = $sanitize->sanitizeFloat("Costo");
            $Total = $Cantidad * $Costo;

            $insertDetalle = "INSERT INTO ned (id,producto,cantidad,precio, total)
                            VALUES($cValVar, $Producto, $Cantidad, $Costo, $Total)";

            if ($mysqli->query($insertDetalle)) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
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


if ($request->hasAttribute("op")) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
        $Return = "nota_pro_ee.php?";
        $cId = $request->getAttribute("cId");

        $deleteDetalle = "DELETE FROM ned WHERE idnvo = $cId LIMIT 1";

        if ($mysqli->query($deleteDetalle)) {
            $Msj = utils\Messages::RESPONSE_VALID_DELETE;
        } else {
            $Msj = utils\Messages::RESPONSE_ERROR;
        }
    } elseif ($request->getAttribute("op") === "cdr") {
        $productoDAO = new ProductoDAO();
        $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
        $egresoVO = $egresoDAO->retrieve($objectVO->getEgreso(), "id", $UsuarioSesion->getCia());

        $selectDetalle = "SELECT * FROM ned WHERE id = $cValVar";
        $registros = utils\ConnectionUtils::getRowsFromQuery($selectDetalle);

        $Cantidad = 0;
        foreach ($registros as $rg) {

            $productoVO = $productoDAO->retrieve($rg["producto"], "id", $UsuarioSesion->getCia());

            $prorateo = ($productoVO->getExistencia() * $productoVO->getCosto() + $rg["cantidad"] * $rg["total"]) / ($productoVO->getExistencia() + $rg["cantidad"]);

            $productoVO->setCostopromedio($prorateo);
            $productoVO->setCosto($rg["precio"]);
            $productoVO->setExistencia($productoVO->getExistencia() + $rg["cantidad"]);

            $productoDAO->update($productoVO);
            $Cantidad += $rg["cantidad"];
        }

        $objectVO->setStatus(StatusNotaEntrada::CERRADA);
        $objectVO->setCantidad($Cantidad);
        $objectDAO->update($objectVO);


        $egresoVO->setEntradaid($cValVar);
        $egresoDAO->update($egresoVO);

        $Msj = utils\Messages::MESSAGE_DEFAULT;
    }


    header("Location: $Return");
}

