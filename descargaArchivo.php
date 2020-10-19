<?php

#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("cfdi33/Comprobante.php");
include_once ("pdf/PDFTransformer.php");
include_once ("pdf/PDFTransformerRP.php");
include_once ("data/FacturaDAO.php");

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;
use com\softcoatl\utils as utils;

Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\\TimbreFiscalDigital");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\Pagos");
Comprobante::registerComplemento("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\complemento\INE");
Comprobante::registerAddenda("com\\softcoatl\\cfdi\\v33\\schema\\Comprobante\\addenda\\Observaciones");

$request = utils\HTTPUtils::getRequest();
$mysqli = conectarse();

$table = $request->getAttribute("file");
$tipo = $request->getAttribute("type");
$id = $request->getAttribute("id");

$sql = "SELECT " . $table . ".id, " . $table . ".uuid, facturas.cfdi_xml, facturas.pdf_format FROM " . $table . " LEFT JOIN facturas ON " . $table . ".uuid = facturas.uuid WHERE " . $table . ".id = $id";
error_log($sql);
$result = $mysqli->query($sql);

if ($result) {
    while ($myrowsel = mysqli_fetch_array($result)) {

        $xml = $myrowsel["cfdi_xml"];
        $uuid = $myrowsel["uuid"];
        $id = $myrowsel["id"];

        if (empty($xml)) {

            error_log("No existe en BD");
            $fileName = "fae/archivos/" . $uuid . ".xml";
            if (file_exists($fileName)) {

                $xml = file_get_contents($fileName);
                $comprobante = Comprobante::parse($xml);
                insertFactura($id, $comprobante, $xml, "SIFEI");
            } else {
                error_log("No existe " . $fileName . " en HD");
                echo "No se encontró el CFDI. Favor de notificar a Soporte";
            }
        } else {

            error_log("Existe en BD");
            error_log($xml);
            $comprobante = Comprobante::parse($xml);
            //error_log(print_r($comprobante, true));
        }

        if ($tipo === "pdf") {
            //error_log($comprobante->getVersion());
            if ($comprobante->getVersion() == "3.3") {

                error_log("Enviando PDF FAE 3.3");
                $logo = file_get_contents("img/logo.png");
                ob_end_clean();
                header("Content-Type: application/pdf");
                header("Content-Disposition: inline; filename=" . $uuid . ".pdf");
                if ($table == "ing") {                    
                   echo PDFTransformerRP::getPDF($comprobante, "Recibo de Pago", "S", $logo);
                } else {
                   echo PDFTransformer::getPDF($comprobante, $table === "fc" ? "Factura" : "Nota de Crédito", "S", $logo);
                }
                exit();
            } else {

                error_log("Enviando PDF FAE 3.2");
                $pdf = $myrowsel["pdf_format"];
                if (empty($pdf)) {

                    error_log("No existe PDF en BD");
                    $fileName = "fae/archivos/" . $uuid . ".pdf";
                    if (file_exists($fileName)) {

                        $pdf = file_get_contents($fileName);
                        updateFactura($id, $pdf);
                    }
                } else {
                    echo "No se encontró el CFDI. Favor de notificar a Soporte";
                }
            }

            ob_end_clean();
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $uuid . ".pdf");
            echo $pdf;
        } else {

            ob_end_clean();
            header("Content-Type: application/xml");
            header("Content-Disposition: attachment; filename=" . $uuid . ".xml");
            echo $xml;
        }
    }
}

function insertFactura($id, $Comprobante, $xml, $clavePAC) {

    $sql = "INSERT INTO facturas (id_fc_fk, version, fecha_emision, fecha_timbrado, cfdi_xml, clave_pac, emisor, receptor, uuid)"
            . " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    error_log($sql);

    $mysqlConnection = getConnection();
    $stmt = $mysqlConnection->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssss",
                $id,
                $Comprobante->getVersion(),
                $Comprobante->getFecha(),
                $Comprobante->getTimbreFiscalDigital()->getFechaTimbrado(),
                $xml,
                $clavePAC,
                $Comprobante->getEmisor()->getRfc(),
                $Comprobante->getReceptor()->getRfc(),
                $Comprobante->getTimbreFiscalDigital()->getUUID());
        if (!$stmt->execute()) {
            error_log($stmt->error);
        }
    } else {
        error_log("Error insertando factura " . $this->mysqlConnection->error);
    }
}

//insertFactura

function updateFactura($id, $pdf) {

    $sql = "UPDATE facturas SET pdf_format = ? WHERE id_fc_fk = ?";
    error_log($sql);

    $mysqlConnection = getConnection();
    $stmt = $mysqlConnection->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ss",
                $pdf,
                $id);
        if (!$stmt->execute()) {
            error_log($stmt->error);
        }
    } else {
        error_log("Error insertando factura " . $this->mysqlConnection->error);
    }
}
