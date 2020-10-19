<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("data/FcDAO.php");
include_once ("CFDIComboBoxes.php");
require_once ("data/RelacionesCfdiDAO.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$nameSession = "relacionFacturacion";
$arrayFilter = array("Tipo" => 1, "cliente" => $request->getAttribute("cliente"), "RelatedID" => $request->getAttribute("id"));
$session = new PaginadorSession("fc.folio", "fc.folio", $nameSession, $arrayFilter, "Tipo");

foreach ($arrayFilter as $key => $value) {
    ${$key} = utils\HTTPUtils::getSessionBiValue($nameSession, $key);
}

$connection = conectarse();
$usuarioSesion = getSessionUsuario();
$busca = $session->getSessionAttribute("criteria");
$Msj = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));

$Id = 53;
$Titulo = "Favor de seleccionar los CFDIs a Relacionar";
$Tabla = "fc";

$conditions = "fc.cia = " . $usuarioSesion->getCia();

if ($Tipo == 1 || $request->hasAttribute("retornar")) {
    $conditions .= " AND $Tabla.tipo = 1 AND $Tabla.status IN ('" . StatusFacturas::CERRADA . "','" . StatusFacturas::CANCELADA . "')";
    $conditions .= " AND fc.cliente = $cliente AND cli.cia = " . $usuarioSesion->getCia();
}

$paginador = new Paginador($Id,
        "fc.id, fc.cliente, fc.uuid, fc.status, rcfdi.tipo_relacion tiporelacion, IF(rcfdi.uuid_relacionado IS NULL, 0, 1 ) checked",
        "LEFT JOIN relacion_cfdi rcfdi ON rcfdi.id = " . $RelatedID . " AND rcfdi.origen = " . ($Tipo == "1" ? "1" : "2") . " AND rcfdi.uuid_relacionado = fc.uuid",
        "",
        "$conditions",
        $session->getSessionAttribute("sortField"),
        $session->getSessionAttribute("criteriaField"),
        utils\Utils::split($session->getSessionAttribute("criteria"), "|"),
        strtoupper($session->getSessionAttribute("sortType")),
        $session->getSessionAttribute("page"),
        "REGEXP",
        "",
        $Tabla . " AS fc");

if ($request->hasAttribute("Boton")) {
    if ($request->getAttribute("Boton") == "Relacionar") {

        $sqlCleanRelacion = "DELETE FROM relacion_cfdi WHERE id = ? AND origen = ?";

        $sqlRelacionFCCFDI = "INSERT INTO relacion_cfdi (id, origen, tipo_relacion, uuid_relacionado) SELECT ?, 1, ?, uuid FROM fc WHERE fc.id = ? ON DUPLICATE KEY UPDATE relacion_cfdi.id = relacion_cfdi.id";
        $sqlRelacionNCCFDI = "INSERT INTO relacion_cfdi (id, origen, tipo_relacion, uuid_relacionado) SELECT ?, 2, ?, uuid FROM fc WHERE fc.id = ? ON DUPLICATE KEY UPDATE relacion_cfdi.id = relacion_cfdi.id";

        $sqlTipoRelacionFCCFDI = "UPDATE fc SET tiporelacion = ? WHERE id = ?";
        $sqlTipoRelacionNCCFDI = "UPDATE nc SET tiporelacion = ? WHERE id = ?";

        $sqlTipoRelacionCFDI = $Tipo == "1" ? $sqlTipoRelacionFCCFDI : $sqlTipoRelacionNCCFDI;

        if (($ps = $connection->prepare($sqlTipoRelacionCFDI)) && $ps->bind_param("si", $request->getAttribute("Tiporelacion"), $RelatedID)) {
            $ps->execute();
        }

        $origenRelacion = $Tipo == "1" ? 1 : 2;
        if (($ps = $connection->prepare($sqlCleanRelacion)) && $ps->bind_param("ii", $RelatedID, $origenRelacion)) {
            $ps->execute();
        }

        $sqlRelacionCFDI = $Tipo == "1" ? $sqlRelacionFCCFDI : $sqlRelacionNCCFDI;
        error_log(print_r($request->getAttributes(), true));
        foreach ($request->getAttributes() as $key => $value) {

            if (strpos($key, "fact_") === 0) {
                if (($ps = $connection->prepare($sqlRelacionCFDI)) && $ps->bind_param("isi", $RelatedID, $request->getAttribute("Tiporelacion"), $value)) {
                    $ps->execute();
                }
            }
        }
        $flag = TRUE;
    }
}
$currSQL = "SELECT * FROM " . ( $Tipo == "1" ? "fc" : "nc" ) . " WHERE id = " . $RelatedID;
$currFC = array();
if ($currRS = $connection->query($currSQL)) {
    $currFC = $currRS->fetch_array();
}
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
        <script>
            $(document).ready(function () {
                $("#Tiporelacion").val('<?= $currFC["tiporelacion"] ?>');
            });
        </script>
        
        <style>
            html, body{
                min-width: 900px;
            }
        </style>
    </head>

    <body <?= $flag ? "onload='window.opener.location.reload(false);self.close();'" : "" ?>>
        
        <?php EncabezadoReportes(); ?>
        
        <form name="form1" method="post" action="">

            <div id="TablaDatos">
                <table aria-hidden="true">
                    <?php echo $paginador->headers(array("Seleccionar"), array()); ?>
                    <tbody>
                        <?php
                        while ($paginador->next()) {
                            $row = $paginador->getDataRow();
                            ?>
                            <tr>
                                <td class="centrar">
                                    <input type="checkbox" class="seleccionar" id="fact_<?= $row["id"] ?>" name="fact_<?= $row["id"] ?>" value="<?= $row["id"] ?>" <?= ($rg['checked'] == 1 ? "checked" : "") ?>>                              
                                </td>                        
                                <td><?= $row["folio"] ?></td>
                                <td><?= $row["fecha"] ?></td>
                                <td><?= $row["cliente"] ?></td>
                                <td><?= $row["nombre"] ?></td>
                                <td class="moneda"><?= number_format($row["cantidad"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["importe"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["iva"], 2) ?></td>
                                <td class="moneda"><?= number_format($row["total"], 2) ?></td>                          

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php
            $nLink = array();
            echo $paginador->footer(false, $nLink, false, false);
            ?>

            <div style="text-align: center;" class="texto_tablas">
                <div>Tipo de Relacion: <?php ComboboxTipoRelacion::generate("Tiporelacion", "class='texto_tablas'"); ?></div>

                <div>
                    <input class="numeros_pagina" type="submit" name="Boton" value="Relacionar">
                    <input class="numeros_pagina" type="button" value="Cancelar" onclick="self.close()">
                </div>
            </div>
        </form>

    </body>
</html>

