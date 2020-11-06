<?php

#Librerias
include_once ("data/OrdenPagoDAO.php");
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
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("id"));
    //header("Location: egresos.php?criteria=ini&returnLink=nota_pro_ee.php&backLink=nota_pro_e.php");
}

if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$cValVar = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$objectDAO = new NotaEntradaDAO();
$egresoDAO = new EgresoDAO();
$ordenDAO = new OrdenPagoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;


    /* Bloque para crear la Orden de Pago */

    $objectOrdenVO = new OrdenPagoVO();
    $objectOrdenVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        // TODO Validar el retrieve
        $objectOrdenVO = $ordenDAO->retrieve($sanitize->sanitizeInt("Ordpago"), "id", $UsuarioSesion->getCia());

        $objectOrdenVO->setProveedor($sanitize->sanitizeInt("Proveedor"));
        $objectOrdenVO->setConcepto($sanitize->sanitizeString("Concepto"));
        $objectOrdenVO->setCotizacion($sanitize->sanitizeFloat("Importe"));
        $objectOrdenVO->setImporte($sanitize->sanitizeFloat("Importe"));
        $objectOrdenVO->setTotal($sanitize->sanitizeFloat("Importe"));
    } else {
        $objectOrdenVO->setId(IncrementaId($ordenDAO::TABLA));
        $objectOrdenVO->setFecha(date("Y-m-d"));
        $objectOrdenVO->setProveedor($sanitize->sanitizeInt("Proveedor"));
        $objectOrdenVO->setConcepto($sanitize->sanitizeString("Concepto"));
        $objectOrdenVO->setSolicito($UsuarioSesion->getId());
        $objectOrdenVO->setCotizacion($sanitize->sanitizeFloat("Importe"));
        $objectOrdenVO->setImporte($sanitize->sanitizeFloat("Importe"));
        $objectOrdenVO->setTotal($sanitize->sanitizeFloat("Importe"));
    }


    /* Bloque para crear el egreso */

    $objectEgresoVO = new EgresoVO();
    $objectEgresoVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        // TODO Validar el retrieve
        $objectEgresoVO = $egresoDAO->retrieve($sanitize->sanitizeInt("Egreso"), "id", $UsuarioSesion->getCia());

        $objectEgresoVO->setBanco($sanitize->sanitizeInt("Banco"));
        $objectEgresoVO->setFormadepago($sanitize->sanitizeString("Formadepago"));
        $objectEgresoVO->setObservaciones($sanitize->sanitizeString("Concepto"));
        $objectEgresoVO->setPagoreal($sanitize->sanitizeFloat("Importe"));
    } else {
        $objectEgresoVO->setId(IncrementaId($egresoDAO::TABLA));
        $objectOrdenVO->setPagonumero($objectEgresoVO->getId());
        $objectEgresoVO->setFecha(date("Y-m-d"));
        $objectEgresoVO->setOrdendepago($objectOrdenVO->getId());
        $objectEgresoVO->setBanco($sanitize->sanitizeInt("Banco"));
        $objectEgresoVO->setFormadepago($sanitize->sanitizeString("Formadepago"));
        $objectEgresoVO->setObservaciones($sanitize->sanitizeString("Concepto"));
        $objectEgresoVO->setPagoreal($sanitize->sanitizeFloat("Importe"));
    }


    /* Bloque para generar la entrada */

    $objectVO = new NotaEntradaVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($cValVar)) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    }

    $objectVO->setProveedor($sanitize->sanitizeString("Proveedor"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setFechafac($sanitize->sanitizeString("Fechafac"));
    $objectVO->setFactura($sanitize->sanitizeString("Factura"));
    $objectVO->setImporte($sanitize->sanitizeString("Importe"));


    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {

            /* Si la orden se ha craado, entonces se crea el pago */
            if (($idOrden = $ordenDAO->create($objectOrdenVO)) > 0) {
                error_log("La orden se ha generado con exito!");

                /* Si el pago se ha creado, se crea la nota de entrada */
                if (($idPago = $egresoDAO->create($objectEgresoVO)) > 0) {
                    error_log("El pago se ha generado con exito!");

                    $objectVO->setEgreso($objectEgresoVO->getId());
                    $objectVO->setOrdpago($objectOrdenVO->getId());
                    $objectVO->setResponsable($UsuarioSesion->getId());
                    if (($id = $objectDAO->create($objectVO)) > 0) {
                        /* Si la entrada se ha creado, se actualiza el id de la entrada en el pago */

                        $objectEgresoVO->setEntradaid($id);
                        if (!($egresoDAO->update($objectEgresoVO))) {
                            error_log("Error al actualizar id del pago!");
                        }

                        $Return = "nota_pro_ee.php?criteria=ini&busca=" . $id;
                        $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                    } else {
                        $Msj = utils\Messages::RESPONSE_ERROR;
                    }
                } else {
                    error_log("Error al generar el pago!");
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                error_log("Error al generar la orden de compra!");
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            $Return = "nota_pro_ee.php?";

            if (!($ordenDAO->update($objectOrdenVO))) {
                error_log("Error al actualizar orden de pago!");
            }

            if (!($egresoDAO->update($objectEgresoVO))) {
                error_log("Error al actualizar pago o egreso!");
            }

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

            if (!empty($Producto)) {
                $insertDetalle = "INSERT INTO ned (id,producto,cantidad,precio, total)
                            VALUES($cValVar, $Producto, $Cantidad, $Costo, $Total)";

                if ($mysqli->query($insertDetalle)) {
                    $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = "El producto ingresado no existe";
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

