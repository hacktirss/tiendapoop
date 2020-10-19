<?php

#Librerias
include_once ("data/CxcDAO.php");
include_once ("data/PagoDAO.php");
include_once ("data/CliDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "cxc.php?";
$UsuarioSesion = getSessionUsuario();

$objectDAO = new CxcDAO();
$pagoDAO = new PagoDAO();
$cliDAO = new CliDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $SCliente = explode("|", strpos($sanitize->sanitizeString("Cliente"), "Array") ? "" : $sanitize->sanitizeString("Cliente"));
    $Var = trim($SCliente[0]);

    $objectVO = new CxcVO();
    $objectVO->setCia($UsuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $UsuarioSesion->getCia());
    } else {
        //$objectVO->setId(IncrementaId($objectDAO::TABLA));
    }

    $objectVO->setCuenta($Var);
    $objectVO->setFecha($sanitize->sanitizeString("Fecha"));
    $objectVO->setFechav($sanitize->sanitizeString("Fechav"));
    $objectVO->setReferencia($sanitize->sanitizeString("Referencia"));
    $objectVO->setTm($sanitize->sanitizeString("Tm"));
    $objectVO->setConcepto($sanitize->sanitizeString("Concepto"));
    $objectVO->setImporte($sanitize->sanitizeFloat("Importe"));
    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $Return = "edita.php?";

            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            $Return = "edita.php?";
            if ($objectDAO->update($objectVO)) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === "Historico'") {
            $selectCxc = "
                        SELECT SUM(importe) importe 
                        FROM (
                            SELECT ROUND(SUM(cxc.importe),2) importe FROM cxc 
                            WHERE cxc.cuenta = $Cliente AND cxc.tm = 'C' AND cxc.cia = " . $UsuarioSesion->getCia() . "
                            UNION
                            SELECT ROUND(SUM(cxc.importe)*-1,2) importe FROM cxc 
                            WHERE cxc.cuenta = $Cliente AND cxc.tm = 'H' AND cxc.cia = " . $UsuarioSesion->getCia() . "
                        ) cxc ;";

            $res = utils\ConnectionUtils::getRowsFromQuery($selectCxc);

            $tm = $res[0]["importe"] > 0 ? "C" : "H";
            $importe = abs($res[0]["importe"]);

            $selectInsertCxc = "INSERT INTO cxch SELECT * FROM cxc WHERE cxc.cuenta = $Cliente AND cxc.cia = " . $UsuarioSesion->getCia() . ";";
            $deleteCxc = "DELETE FROM cxc WHERE cuenta = $Cliente;";

            if ($mysqli->query($selectInsertCxc) && $mysqli->query($deleteCxc)) {

                $cxcVO = new CxcVO();
                $cxcVO->setCia($UsuarioSesion->getCia());
                $cxcVO->setCuenta($Cliente);
                $cxcVO->setFecha(date("Y-m-d"));
                $cxcVO->setReferencia(date("Ymd"));
                $cxcVO->setTm($tm);
                $cxcVO->setFechav(date("Y-m-d"));
                $cxcVO->setConcepto("SALDO AL " . date("Y-m-d"));
                $cxcVO->setImporte($importe);
                if (($id = $objectDAO->create($cxcVO)) < 0) {
                    $Msj .= " Ocurrio un error al crear registro en estado de cuenta. ";
                }
            } else {
                error_log($mysqli->error);
            }
        }


        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    } finally {
        header("Location: $Return");
    }
} elseif ($request->getAttribute("Boton") === utils\Messages::OP_NO_OPERATION_VALID) {
    if (!empty($Importe) && !empty($Cliente)) {

        $ingVO = new PagoVO();
        $ingVO->setCia($UsuarioSesion->getCia());
        $ingVO->setCuenta($Cliente);
        $ingVO->setConcepto("ABONO A CUENTA");
        $ingVO->setImporte($Importe);
        $ingVO->setFecha(date("Y-m-d H:i:s"));
        $ingVO->setReferencia("CXC");
        $ingVO->setFechap(date("Y-m-d"));
        $ingVO->setStatus("Cerrada");

        if (($id = $pagoDAO->create($ingVO)) < 0) {
            $Msj .= "Ocurrio un error al crear registro en estado de cuenta";
        } else {
            $Recibo = $id;

            $cxcVO = new CxcVO();
            $cxcVO->setCia($UsuarioSesion->getCia());
            $cxcVO->setCuenta($Cliente);
            $cxcVO->setFecha(date("Y-m-d"));
            $cxcVO->setReferencia(!empty($request->getAttribute("Referencia")) ? $request->getAttribute("Referencia") : "F-99999");
            $cxcVO->setTm("H");
            $cxcVO->setFechav(date("Y-m-d"));
            $cxcVO->setConcepto("ABONO A CUENTA");
            $cxcVO->setImporte($Importe);
            $cxcVO->setRecibo($Recibo);
            if (($id = $objectDAO->create($cxcVO)) < 0) {
                $Msj .= " Ocurrio un error al crear registro en estado de cuenta. ";
            }
        }

        header("Location: " . $Return . "Msj=" . urlencode($Msj));
    }
}
