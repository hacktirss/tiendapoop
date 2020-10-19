<?php

include_once ("cfdi33/Comprobante.php");
include_once ("pdf/PDFTransformer.php");
include_once ("pdf/PDFTransformerRP.php");
include_once ("MailSender.php");
include_once ("data/CiaDAO.php");
include_once ("data/FcDAO.php");
include_once ("data/NcDAO.php");
include_once ("data/CliDAO.php");
include_once ("data/CertificadoDAO.php");
include_once ("data/ProductoDAO.php");
include_once ("nusoap.php");
include_once ("CFDIComboBoxes.php");
include_once ("data/RelacionesCfdiDAO.php");

use com\softcoatl\cfdi\v33\schema\Comprobante;
use com\softcoatl\utils\HTTPUtils;
use com\softcoatl\utils as utils;

Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\\TimbreFiscalDigital");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\Pagos");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\INE");
Comprobante::registerAddenda("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\addenda\\Observaciones");

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$nameSession = "moduloFacturacion";

if ($request->hasAttribute("busca")) {
    HTTPUtils::setSessionBiValue($nameSession, DETALLE, $request->getAttribute("busca"));
}

$mysqli = conectarse();


$Tipo = HTTPUtils::getSessionBiValue($nameSession, "Paso"); //Tipo de factura o nota de credito
$busca = HTTPUtils::getSessionBiValue($nameSession, DETALLE);

$Return = "facturase.php?";
$UsuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$Id = 54;
$Titulo = "Detalle de factura";

if ($Tipo == 1) {
    $Tabla = "fc";
    $Tablad = "fcd";
} else {
    $Titulo = "Detalle de nota de crédito";
    $Tabla = "nc";
    $Tablad = "ncd";
}

$fcDAO = new FcDAO();
$ncDAO = new NcDAO();
$cliDAO = new CliDAO();
$productoDAO = new ProductoDAO();
$relacionesDAO = new RelacionesCfdiDAO();
$relaciones = $relacionesDAO->retrieve($busca, $Tipo == 1 ? 1 : 2);

try {

    if ($request->hasAttribute("id") && $request->getAttribute("id") === "NUEVO") {
        if ($Tipo == 1) {
            header("Location: clientes.php?criteria=ini&returnLink=facturase.php&backLink=facturas.php");
        } else {
            header("Location: cat_facturas.php?criteria=ini&returnLink=facturase.php&backLink=facturas.php");
        }
    }

    if ($request->hasAttribute("Cliente")) {
        $Cliente = $request->getAttribute("Cliente");
        $cliVO = $cliDAO->retrieve($Cliente, "id", $UsuarioSesion->getCia());

        $Folio = IncrementaFolio($Tabla);

        $fcVO = new FcVO();
        $fcVO->setCia($UsuarioSesion->getCia());
        $fcVO->setSerie($CiaSesion->getSerie());
        $fcVO->setCliente($Cliente);
        $fcVO->setRfc($CiaSesion->getRfc());
        $fcVO->setFolio($Folio);
        $fcVO->setStatus(StatusFacturas::ABIERTA);
        $fcVO->setTipo(TipoFacturas::FACTURACION);

        if (($id = $fcDAO->create($fcVO)) > 0) {
            $Return .= "criteria=ini&busca=" . $id . "&Msj=" . urlencode(utils\Messages::RESPONSE_VALID_CREATE);
            HTTPUtils::setSessionValue("cVar", $id);
        } else {
            $Return = "facturas.php?Msj=" . urlencode(utils\Messages::RESPONSE_ERROR);
        }
        header("Location: " . $Return);
        exit();
    }

    if ($request->hasAttribute("Factura")) {
        $Factura = $request->getAttribute("Factura");
        $fcVO = $fcDAO->retrieve($Factura);
        $Folio = IncrementaFolio($Tabla);

        $ncVO = new NcVO();
        $ncVO->setCia($UsuarioSesion->getCia());
        $ncVO->setSerie($CiaSesion->getSerie());
        $ncVO->setCliente($fcVO->getCliente());
        $ncVO->setStatus(StatusNotas::ABIERTA);
        $ncVO->setRfc($fcVO->getRfc());
        $ncVO->setFolio($Folio);
        $ncVO->setTipo(TipoFacturas::NOTAS_CREDITO);
        $ncVO->setFactura($fcVO->getFolio());
        $ncVO->setRelacioncfdi($fcVO->getFolio());
        $ncVO->setTiporelacion("04");
        $ncVO->setFormadepago($fcVO->getFormadepago());
        $ncVO->setFcId($fcVO->getId());

        if (($id = $ncDAO->create($ncVO)) > 0) {
            $Return .= "criteria=ini&busca=" . $id . "&Msj=" . urlencode(utils\Messages::RESPONSE_VALID_CREATE);
            HTTPUtils::setSessionValue("cVar", $id);
        } else {
            $Return = "facturas.php?Msj=" . urlencode(utils\Messages::RESPONSE_ERROR);
        }
        header("Location: " . $Return);
        exit();
    }
    
    if ($request->hasAttribute("nota")) {
        $Nota = $request->getAttribute("nota");
        $fcVO = $fcDAO->retrieve($busca);
        $updateNota = "UPDATE ns SET factura = CONCAT(F-" . $fcVO->getFolio() . ") WHERE id = $Nota LIMIT 1;";
        if (!$mysqli->query($updateNota)) {
            error_log($mysqli->error);
        }
        header("Location: " . $Return);
    }

    if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
        
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            $Precio = $request->getAttribute("Precio");
            $Cantidad = $request->getAttribute("Cantidad");
            $Descripcion = $request->getAttribute("Descripcion");
            $Descuento = $request->getAttribute("Descuento");
            $Producto = $request->getAttribute("Producto");
            
            $productoVO = $productoDAO->retrieve($Producto, "id", $UsuarioSesion->getCia());

            $PrecioU = !empty($Precio) ? $Precio : $productoVO->getPrecio();
            
            $Iva = 0;
            if ($productoVO->getIva() == 1) {
                $Iva = $CiaSesion->getIva() / 100;
            }           
            $Isr = 0;
            if ($productoVO->getIsr() == 1) {
                $Isr = $CiaSesion->getIsr() / 100;
            }
            $Retencioniva = 0;
            if ($productoVO->getRetencioniva() == 1) {
                $Retencioniva = $CiaSesion->getRetencioninva() / 100;
            }
            $Ieps = 0;
            if ($productoVO->getIeps() == 1) {
                $Ieps = $CiaSesion->getIeps() / 100;
            }

            if ($Tipo == 1) {
                $insertDetalle = "INSERT INTO $Tablad (id, producto, cantidad, precio, iva, ieps, isr, retencioniva, descripcion, descuento) VALUES
                ('$busca', $Producto, $Cantidad, $PrecioU, $Iva ,$Ieps, $Isr, $Retencioniva,'$Descripcion', " . ( empty($Descuento) ? "0" : $Descuento ) . ")";
            } else {
                $insertDetalle = "INSERT INTO $Tablad (id, producto, cantidad, precio, iva, ieps, isr, retencioniva, descripcion) VALUES
                ('$busca', $Producto, $Cantidad, $PrecioU, $Iva,$Ieps, $Isr, $Retencioniva,'$Descripcion')";
            }

            if (!$mysqli->query($insertDetalle)) {
                error_log($mysqli->error);
            }

            Totaliza($busca, $Tabla, $Tablad);
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            $Observaciones = $request->getAttribute("Observaciones");
            $Concepto = $request->getAttribute("Concepto");
            $cSql = "UPDATE $Tabla SET observaciones = '$Observaciones', concepto = '$Concepto' WHERE id='$busca'";

            if (!mysqli_query($mysqli, $cSql)) {
                $Archivo = $Tabla;
                echo "<div align='center'>$cSql</div>";
                die('<div align="center"><p>&nbsp;</p>Error critico[paso 1]<br>el proceso <b>NO</b> se finaliz&oacute; correctamente, favor de informar al <b>departamento de sistemas</b><br><b> ' . $Archivo . mysqli_error() . '</b><br> favor de dar click en la flecha <a href=menu.php?op=102><img src=lib/regresa.jpg border=0></a> para regresar</div>');
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_SEND_EMAIL) {

            $certificadoDAO = new CertificadoDAO();
            $fcDAO = new FcDAO();
            $cliDAO = new CliDAO();
            
            $fc = $fcDAO->retrieve($busca);            
            $cliVO = $cliDAO->retrieve($fc->getCliente(), "id", $UsuarioSesion->getCia());
            $certificadoVO = $certificadoDAO->retrieve($UsuarioSesion->getCia(), "cia");
            
            $cliVO->setCorreo($request->getAttribute("Correo"));

            $table = $Tabla;
            $id = $busca;
            $selectFiles = "SELECT fc.id, fc.uuid, facturas.cfdi_xml, facturas.pdf_format 
                            FROM $table AS fc 
                            LEFT JOIN facturas ON fc.uuid = facturas.uuid 
                            WHERE fc.id = $id";
            $result = mysqli_query($mysqli, $selectFiles);
            $myrowsel = mysqli_fetch_array($result);

            $uuid = $myrowsel['uuid'];
            $xml = $myrowsel['cfdi_xml'];
            $comprobante = Comprobante::parse($xml);

            //$logo = file_get_contents("img/logo.png");
            $pdf = PDFTransformer::getPDF($comprobante, $table === "fc" ? "Factura" : "Nota de Crédito", "S", $certificadoVO->getLogo());
            if (MailSender::send($CiaSesion, $cliVO, $fc->getFolio(), $uuid, $xml, $pdf)) {
                $Msj = "Se han enviado con éxito sus archivos XML y PDF";
            }
        } elseif ($request->getAttribute("Boton") === "Enviar a estado de cuenta") {
            $sql_fc = "SELECT CONCAT('F-',$Tabla.folio) ref FROM $Tabla  WHERE $Tabla.id = '$busca'";
            $CpoA = mysqli_query($mysqli, $sql_fc);
            $Cpo = mysqli_fetch_array($CpoA);

            $sql_delete = "DELETE FROM cxc WHERE referencia = '$Cpo[ref]' AND tm = 'C' LIMIT 1";
            mysqli_query($mysqli, $sql_delete) or error_log($mysqli->error);

            $sql_insert = " 
                INSERT INTO cxc
                    SELECT null id,cliente cuenta,DATE(fecha) fecha,CONCAT('F-',folio) referencia,'C' tm,DATE(fecha) fechav,concepto,total importe,0 reciboant,0 recibo
                    FROM fc WHERE id = '$busca';";

            mysqli_query($mysqli, $sql_insert) or error_log($mysqli->error);

            $Msj = "Se ha agregado la factura al Estado de cuenta.";
        }

        header("Location: " . $Return . "Msj=" . urlencode($Msj));
    }

    if ($request->hasAttribute("op")) {
        $cId = $request->getAttribute("cId");
        if ($request->getAttribute("op") === "cfdi") {
            $cSql = "INSERT INTO relacion_cfdi( id, origen, uuid_relacionado, tipo_relacion) SELECT ?, ?, uuid, ? FROM " . $Tabla . " WHERE id = ?";

            if (($stmt = $mysqli->prepare($sql)) && $stmt->bind_param("iiss", $busca, $origen, $tipoRelacion, $cId) && $stmt->execute()) {
                error_log($mysqli->error);
            }
        } elseif ($request->getAttribute("op") === utils\Messages::OP_DELETE) {
            $lUp = mysqli_query($mysqli, "DELETE FROM $Tablad WHERE idnvo='$cId' limit 1");

            Totaliza($busca, $Tabla, $Tablad);            
        }
        header("Location: " . $Return . "Msj=" . urlencode($Msj));
    }
} catch (Exception $ex) {
    error_log($ex);
} finally {
   
}