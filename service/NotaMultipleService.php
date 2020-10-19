<?php

#Librerias
include_once ("data/NotaMultipleDAO.php");
include_once ("data/EgresoDAO.php");
include_once ("data/ProductoDAO.php");
include_once ("data/EquipoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "nota_mul_e.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$nameSession = "moduloEntradasMul";

if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, NULL);
    header("Location: egresos.php?criteria=ini&returnLink=nota_mul_ee.php&backLink=nota_mul_e.php");
}

if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$cValVar = utils\HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$objectDAO = new NotaMultipleDAO();
$egresoDAO = new EgresoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new NotaMultipleVO();
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

    //error_log(print_r($request, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $objectVO->setResponsable($UsuarioSesion->getId());
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Return = "nota_mul_ee.php?criteria=ini&busca=" . $id;
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            $Return = "nota_mul_ee.php?";
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

                $objectVO->setStatus(StatusNotaMultiple::CANCELADA);

                if ($objectDAO->update($objectVO)) {
                    $Msj = utils\Messages::RESPONSE_VALID_CANCEL;

                    $updateInventario = "
                            UPDATE inv,nmd
                            SET inv.existencia = inv.existencia - nmd.cantidad  
                            WHERE TRUE 
                            AND inv.id = nmd.producto AND nmd.id = " . $objectVO->getId() . " AND nmd.tipo = " . TipoNotaMultiple::PRODUCTO . "
                            AND inv.cia = " . $objectVO->getCia();
                    if (!$mysqli->query($updateInventario)) {
                        $Msj .= ". " . $mysqli->error;
                    } else {
                        error_log("Productos afectados: " . $mysqli->affected_rows);
                    }

                    $updateEquipos = "
                            UPDATE nmd, equipos SET equipos.cia = -equipos.cia WHERE TRUE 
                            AND equipos.numero_entrada = nmd.id
                            AND equipos.marca = nmd.marca
                            AND equipos.numero_serie = nmd.numero_serie
                            AND nmd.id = " . $objectVO->getId() . " AND nmd.tipo = " . TipoNotaMultiple::EQUIPO . " 
                            AND equipos.cia = " . $objectVO->getCia();
                    if (!$mysqli->query($updateEquipos)) {
                        $Msj .= ". " . $mysqli->error;
                    } else {
                        error_log("Equipos afectados: " . $mysqli->affected_rows);
                    }
                } else {
                    $Msj = utils\Messages::RESPONSE_ERROR;
                }
            } else {
                $Msj = utils\Messages::RESPONSE_PASSWORD_INCORRECT;
            }
        } elseif ($request->getAttribute("Boton") === "Cancelar nota") {
            $objectVO->setStatus(StatusNotaMultiple::CANCELADA);
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
    $Return = "nota_mul_ee.php?";
    try {
        if ($request->getAttribute("BotonP") === utils\Messages::OP_ADD) {
            $Producto = $sanitize->sanitizeInt("Producto");
            $Cantidad = $sanitize->sanitizeInt("CantidadP");
            $Costo = $sanitize->sanitizeFloat("CostoP");

            $insertDetalle = "INSERT INTO nmd (id, tipo, producto, cantidad, costo)
                            VALUES($cValVar, " . TipoNotaMultiple::PRODUCTO . ",$Producto, $Cantidad, $Costo)";

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
    $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
    $Return = "nota_mul_ee.php?";

    try {
        if ($request->getAttribute("BotonE") === utils\Messages::OP_ADD) {

            $Grupo = $sanitize->sanitizeInt("Grupo");
            $Marca = $sanitize->sanitizeString("Marca");
            $Modelo = $sanitize->sanitizeString("Modelo");
            $Serie = $sanitize->sanitizeString("Serie");
            $Cantidad = $sanitize->sanitizeInt("Cantidad");
            $Costo = $sanitize->sanitizeFloat("Costo");
            $Precio = $Costo + ($Costo * 0.20);

            $buscarSerie = "SELECT COUNT(id) registros FROM nmd WHERE id = $cValVar AND numero_serie = '$Serie'";
            $equipo = utils\ConnectionUtils::execSql($buscarSerie);

            if ($equipo["registros"] === "0") {
                for ($i = 0; $i < $Cantidad; $i++) {
                    $insertDetalle = "INSERT INTO nmd (id, tipo, grupo, marca, modelo, numero_serie, cantidad, costo, precio)
                            VALUES($cValVar, " . TipoNotaMultiple::EQUIPO . ", $Grupo, '$Marca', '$Modelo', '$Serie', $Cantidad, $Costo, $Precio)";

                    if ($mysqli->query($insertDetalle)) {
                        $Msj = utils\Messages::RESPONSE_VALID_CREATE;
                    } else {
                        error_log($mysqli->error);
                        $Msj = utils\Messages::RESPONSE_ERROR;
                    }
                }
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

    if ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
        $Return = "nota_mul_ee.php?";
        $cId = $request->getAttribute("cId");

        $deleteDetalle = "DELETE FROM nmd WHERE idnvo = $cId LIMIT 1";

        if ($mysqli->query($deleteDetalle)) {
            $Msj = utils\Messages::RESPONSE_VALID_DELETE;
        } else {
            $Msj = utils\Messages::RESPONSE_ERROR;
        }
    } elseif ($request->getAttribute("op") === "cdr") {
        $productoDAO = new ProductoDAO();
        $objectVO = $objectDAO->retrieve($cValVar, "id", $UsuarioSesion->getCia());
        $egresoVO = $egresoDAO->retrieve($objectVO->getEgreso(), "id", $UsuarioSesion->getCia());

        $selectDetalle = "SELECT * FROM nmd WHERE id = $cValVar AND tipo = " . TipoNotaMultiple::PRODUCTO;
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

        $insertEquipos = "
                INSERT INTO equipos (cia, marca, descripcion, grupo, numero_serie, modelo, costo, precio, numero_entrada)
                SELECT nm.cia, nmd.marca, nmd.descripcion, nmd.grupo, nmd.numero_serie, nmd.modelo,
                nmd.costo, nmd.precio, nmd.id
                FROM nm, nmd 
                WHERE TRUE AND nm.id = nmd.id AND nmd.tipo = " . TipoNotaMultiple::EQUIPO . " AND nm.id = $cValVar";

        if (!$mysqli->query($insertEquipos)) {
            error_log($mysqli->error);
        } else {
            error_log("Equipos afectados: " . $mysqli->affected_rows);
        }

        $objectVO->setStatus(StatusNotaMultiple::CERRADA);
        $objectVO->setCantidad($Cantidad);
        $objectDAO->update($objectVO);


        $egresoVO->setEntradaid($cValVar);
        $egresoDAO->update($egresoVO);

        $Msj = utils\Messages::MESSAGE_DEFAULT;
    }


    header("Location: $Return");
}

