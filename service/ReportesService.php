<?php

include_once ("data/FcDAO.php");
include_once ("data/NcDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$UsuarioSesion = getSessionUsuario();

//error_log(print_r($request, true));

if ($request->hasAttribute("criteria")) {
    utils\HTTPUtils::setSessionValue("Fecha", date("Y-m-d"));
    utils\HTTPUtils::setSessionValue("FechaI", date("Y-m-01"));
    utils\HTTPUtils::setSessionValue("FechaF", date("Y-m-d"));
    utils\HTTPUtils::setSessionValue("Detallado", "Si");
    utils\HTTPUtils::setSessionValue("Cliente", 0);
    utils\HTTPUtils::setSessionValue("SCliente", "");
    utils\HTTPUtils::setSessionValue("Status", "Todos");
    utils\HTTPUtils::setSessionValue("Formato", 2);
    utils\HTTPUtils::setSessionValue("Rubro", "Todos");
    utils\HTTPUtils::setSessionValue("Forma", "Todos");
    utils\HTTPUtils::setSessionValue("Importe", 0);
    utils\HTTPUtils::setSessionValue("Orden", "fecha");
    utils\HTTPUtils::setSessionValue("busca", "");
}


if ($request->hasAttribute("Fecha")) {
    utils\HTTPUtils::setSessionValue("Fecha", $sanitize->sanitizeString("Fecha"));
}
if ($request->hasAttribute("FechaI")) {
    utils\HTTPUtils::setSessionValue("FechaI", $sanitize->sanitizeString("FechaI"));
}
if ($request->hasAttribute("FechaF")) {
    utils\HTTPUtils::setSessionValue("FechaF", $sanitize->sanitizeString("FechaF"));
}
if ($request->hasAttribute("Detallado")) {
    utils\HTTPUtils::setSessionValue("Detallado", $sanitize->sanitizeString("Detallado"));
}
if ($request->hasAttribute("SCliente")) {
    utils\HTTPUtils::setSessionValue("SCliente", $sanitize->sanitizeString("SCliente"));
    $SCliente = explode("|", strpos($sanitize->sanitizeString("SCliente"), "Array") ? "" : $sanitize->sanitizeString("SCliente"));
    $Var = trim($SCliente[0]);
    if ($Var > 0) {
        $selectCli = "SELECT id, CONCAT(id, ' | ', tipodecliente, ' | ', nombre) cliente FROM cli WHERE id = '$Var' AND cia = " . $UsuarioSesion->getCia();
        if (($dbCliQuery = $mysqli->query($selectCli)) && ($dbCliRS = $dbCliQuery->fetch_array())) {
            $SCliente = $dbCliRS['cliente'];
            $Cliente = $dbCliRS['id'];
            utils\HTTPUtils::setSessionValue("Cliente", $Cliente);
        }
    } else {
        utils\HTTPUtils::setSessionValue("Cliente", "");
    }
}
if ($request->hasAttribute("Status")) {
    utils\HTTPUtils::setSessionValue("Status", $sanitize->sanitizeString("Status"));
}
if ($request->hasAttribute("Formato")) {
    utils\HTTPUtils::setSessionValue("Formato", $sanitize->sanitizeString("Formato"));
}
if ($request->hasAttribute("Rubro")) {
    utils\HTTPUtils::setSessionValue("Rubro", $sanitize->sanitizeString("Rubro"));
}
if ($request->hasAttribute("Forma")) {
    utils\HTTPUtils::setSessionValue("Forma", $sanitize->sanitizeString("Forma"));
}
if ($request->hasAttribute("Importe")) {
    utils\HTTPUtils::setSessionValue("Importe", $sanitize->sanitizeFloat("Importe"));
}
if ($request->hasAttribute("Orden")) {
    utils\HTTPUtils::setSessionValue("Orden", $sanitize->sanitizeString("Orden"));
}
if ($request->hasAttribute("busca")) {
    utils\HTTPUtils::setSessionValue("busca", $sanitize->sanitizeString("busca"));
}

$Fecha = utils\HTTPUtils::getSessionValue("Fecha");
$FechaI = utils\HTTPUtils::getSessionValue("FechaI");
$FechaF = utils\HTTPUtils::getSessionValue("FechaF");
$Detallado = utils\HTTPUtils::getSessionValue("Detallado");
$Cliente = utils\HTTPUtils::getSessionValue("Cliente");
$SCliente = utils\HTTPUtils::getSessionValue("SCliente");
$Status = utils\HTTPUtils::getSessionValue("Status");
$Formato = utils\HTTPUtils::getSessionValue("Formato");
$Rubro = utils\HTTPUtils::getSessionValue("Rubro");
$Forma = utils\HTTPUtils::getSessionValue("Forma");
$Importe = utils\HTTPUtils::getSessionValue("Importe");
$Orden = utils\HTTPUtils::getSessionValue("Orden");
$busca = utils\HTTPUtils::getSessionValue("busca");

$StatusArray = Array(
    "Cancelada" => "Cancelada",
    "Abierta" => "Abierta",
    "Cerrada" => "Cerrada",
    "Timbrada" => "Timbrada",
    "Todos" => "Todos"
);

/* Consulatar estado de cuenta */
$orden = $Orden == "referencia" ? "cxc.referencia,cxc.tm" : $Orden == "factura" ? "cxc.factura,cxc.tm" : "cxc.fecha,cxc.tm";
$selectCxc = "SELECT DISTINCT cxc.* 
        FROM (
            SELECT cxc.id,cxc.fecha,cxc.cuenta,cxc.referencia,cxc.tm,
            cxc.concepto,ROUND(cxc.importe,2) importe,IFNULL(nc.folio,0) folio,IFNULL(CONCAT('F-',fc.folio),IFNULL(nc.folio,0)) factura 
            FROM cxc 
            LEFT JOIN (
                SELECT CONCAT('F-',nc.factura) folio,CONCAT('NC-',nc.factura) nota 
                FROM nc 
                WHERE TRUE 
                AND nc.cia = " . $UsuarioSesion->getCia() . "
                AND nc.cliente = $Cliente AND nc.status = '" . StatusNotas::CERRADA . "'
            ) nc 
            ON cxc.referencia = nc.nota
            LEFT JOIN fc ON cxc.cia = fc.cia AND cxc.referencia = CONCAT('F-',fc.folio)
            WHERE TRUE 
            AND cxc.cia = " . $UsuarioSesion->getCia() . "
            AND cuenta = $Cliente
            AND DATE(cxc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ) cxc 
        ORDER BY $orden ";

/* Consulatar estado de cuenta historico */

$selectCxcH = "SELECT DISTINCT cxc.* 
        FROM (
            SELECT cxc.id,cxc.fecha,cxc.cuenta,cxc.referencia,cxc.tm,
            cxc.concepto,ROUND(cxc.importe,2) importe,IFNULL(nc.folio,0) folio,IFNULL(CONCAT('F-',fc.folio),IFNULL(nc.folio,0)) factura 
            FROM cxch AS cxc 
            LEFT JOIN (
                SELECT CONCAT('F-',nc.factura) folio,CONCAT('NC-',nc.factura) nota 
                FROM nc 
                WHERE TRUE 
                AND nc.cia = " . $UsuarioSesion->getCia() . "
                AND nc.cliente = $Cliente AND nc.status = '" . StatusNotas::CERRADA . "'
            ) nc 
            ON cxc.referencia = nc.nota
            LEFT JOIN fc ON cxc.cia = fc.cia AND cxc.referencia = CONCAT('F-',fc.folio)
            WHERE TRUE 
            AND cxc.cia = " . $UsuarioSesion->getCia() . "
            AND cuenta = $Cliente
            AND DATE(cxc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ) cxc 
        ORDER BY $orden ";


/* Consulta para saldos por cliente */

$selectSaldosCliente = "
        SELECT cli.id cuenta,cli.alias,cli.nombre,cli.status,IFNULL(SUM(sub.importe),0) importe
        FROM cli
        LEFT JOIN (
                SELECT cxc.cuenta,SUM(cxc.importe) importe FROM cxc
                WHERE cxc.tm = 'C' AND cxc.cia = " . $UsuarioSesion->getCia() . "
                AND DATE(cxc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
                GROUP BY cxc.cuenta 
            UNION 
                SELECT cxc.cuenta,SUM(-cxc.importe) importe FROM cxc 
                WHERE cxc.tm = 'H' AND cxc.cia = " . $UsuarioSesion->getCia() . "
                AND DATE(cxc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
                GROUP BY cxc.cuenta
        ) sub ON sub.cuenta = cli.id
        WHERE TRUE
        AND cli.cia = " . $UsuarioSesion->getCia() . "
        GROUP BY cli.id 
        ORDER BY cli.status, cli.nombre ASC";


/* Consulta para reporte de pagos */

$selectPagosCliente = "
        SELECT COUNT(ing.id) pagos, ing.id, ing.fechap fecha,ing.cuenta, cli.nombre, SUM(ing.importe) importe
        FROM ing 
        LEFT JOIN cli ON ing.cuenta = cli.id AND ing.cia = cli.cia
        WHERE TRUE 
        AND ing.cia = " . $UsuarioSesion->getCia() . "
        AND DATE(ing.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF') ";

$selectPagosClienteCnt = $selectPagosCliente . " GROUP BY ing.cuenta ORDER BY ing.cuenta";

$selectPagosCliente .= " GROUP BY ing.id ORDER BY ing.id";


/* Consulta para cargos, abonos y saldos */

$selectCargosAbonosSaldos = "
        SELECT 
        C.cuenta, 
        cli.nombre, 
        cli.alias, 
        SUM(IFNULL(inicial, 0)) inicial,
        SUM(IFNULL(cargo, 0)) cargos,
        SUM(IFNULL(abono, 0)) abonos,
        ROUND(SUM(IFNULL(inicial, 0)) + SUM(IFNULL(cargo, 0)) - SUM(IFNULL(abono, 0)) , 2) importe
        FROM cli
        JOIN (
                SELECT cxc.cuenta,ROUND( SUM( IF(tm = 'C',importe,-importe) ), 2) inicial,
                0 abono,0 cargo
                FROM cxc
                WHERE cxc.cuenta > 0 
                AND cxc.cia = " . $UsuarioSesion->getCia() . "
                AND DATE(cxc.fecha) < DATE('$FechaI')
                GROUP BY cxc.cuenta		
                UNION ALL		
                SELECT
                cxc.cuenta,0 inicial,
                ROUND( SUM( IF(tm = 'C',0,importe) ), 2) abono,
                ROUND( SUM( IF(tm = 'C',importe,0) ), 2) cargo
                FROM cxc
                WHERE cxc.cuenta > 0 
                AND cxc.cia = " . $UsuarioSesion->getCia() . "
                AND DATE(cxc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF') 
                GROUP BY cxc.cuenta,cxc.tm
        ) C ON C.cuenta = cli.id 
        WHERE TRUE
        AND cli.cia = " . $UsuarioSesion->getCia() . "
        GROUP by C.cuenta ";


/* Consulta para reporte de inventario */

$selectInventario = "
        SELECT inv.id clave, inv.descripcion, inv.existencia, inv.costo, inv.precio
        FROM inv WHERE TRUE 
        AND inv.cia = " . $UsuarioSesion->getCia() . "
        ";
if ($Status !== "Todos") {
    $selectInventario .= " AND inv.existencia > 0 ";
}

$selectInventario .= " ORDER BY descripcion";


/* Consulta para inventario de equipos */

$selectInventarioE = "
        SELECT COUNT(equipos.id) cantidad, equipos.grupo, grupos.descripcion, equipos.marca, equipos.numero_serie,
        equipos.modelo, equipos.costo, equipos.precio, equipos.numero_entrada
        FROM equipos,grupos 
        WHERE TRUE 
        AND equipos.cia = grupos.cia
        AND equipos.grupo = grupos.id
        AND equipos.cia = " . $UsuarioSesion->getCia() . "
        GROUP BY equipos.marca, equipos.modelo, equipos.numero_serie
        ";

if ($Detallado === "Si") {
    $selectInventarioE = "
        SELECT 1 cantidad, equipos.grupo, grupos.descripcion, equipos.marca, equipos.numero_serie,
        equipos.modelo, equipos.costo, equipos.precio, equipos.numero_entrada
        FROM equipos,grupos 
        WHERE TRUE 
        AND equipos.cia = grupos.cia
        AND equipos.grupo = grupos.id
        AND equipos.cia = " . $UsuarioSesion->getCia() . "
        ORDER BY equipos.grupo, equipos.marca, equipos.modelo, equipos.numero_serie
        ";
}


/* Consulta para relacion de facturas */

$selectRelacionFacturas = "
        SELECT CONCAT('F-',fc.folio) folio,DATE(fc.fecha) fecha, fc.cliente, cli.nombre, fc.concepto, UPPER(fc.uuid) uuid,
        fc.importe,fc.iva,fc.total,fc.status
        FROM fc, cli
        WHERE TRUE 
        AND fc.cia = cli.cia
        AND fc.cliente = cli.id 
        AND fc.cia = " . $UsuarioSesion->getCia() . "
        AND DATE(fc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ";
if (!empty($Status) && $Status !== "Todos") {
    $selectRelacionFacturas .= " AND fc.status LIKE '%$Status%'";
}

$selectRelacionFacturas .= " ORDER BY fc.id";

/* Consulta pare relacion de pogo de facturas */

if ($Formato == 1) {
    $selectPagoFacturas = "
        SELECT 
        cxc.idpago recibo, fc.folio folioFc, cxc.fechaPago, DATE(fc.fecha) fechaFac, cxc.nombre,
        fc.importe, fc.iva,
        ROUND(fc.total - cxc.notas, 2) total, ROUND(cxc.importe ,2) depositado, 
        ROUND(fc.total - cxc.notas - cxc.importe,2) diferencia,
        IFNULL(UPPER(fc.uuid),cxc.concepto) uuid, IFNULL(fc.status,'Cerrada') status, cxc.banco 
        FROM (
            SELECT ing.id idpago, cxc.referencia, cli.nombre, SUBSTRING(cxc.referencia,3) folio,
            ing.fechap fechaPago, IFNULL(bancos.nombre,'-----') banco, cxc.recibo, cxc.fecha, cxc.concepto,
            ROUND(SUM(cxc.importe),2) importe, IFNULL(ROUND(SUM(cxc_h.importe),2),0) notas                              
            FROM cli,cxc 
            LEFT JOIN cxc cxc_h ON cxc.cia = cxc_h.cia AND SUBSTRING(cxc.referencia,3) = SUBSTRING(cxc_h.referencia,4)
            LEFT JOIN ing ON cxc.cia = ing.cia AND cxc.recibo = ing.id 
            LEFT JOIN bancos ON ing.cia = bancos.cia AND ing.banco = bancos.id
            WHERE 1 = 1 
            AND cxc.cia = cli.cia
            AND cxc.cuenta = cli.id
            AND cxc.cia = " . $UsuarioSesion->getCia() . "
            AND cxc.referencia LIKE 'F-%' AND cxc.tm = 'H'
            GROUP BY cxc.referencia
        ) cxc 
        LEFT JOIN fc ON fc.folio = cxc.folio AND fc.cia = " . $UsuarioSesion->getCia() . "
        WHERE 
        DATE(cxc.fechaPago) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')";
} else {
    $selectPagoFacturas = "
        SELECT 
        cxc.idpago recibo,cxc.folio, cxc.fechaPago, cxc.nombre, 
        IFNULL(UPPER(fc.uuid),cxc.concepto) uuid, IFNULL(fc.status,'Cerrada') status,
        ROUND((cxc.importe / (1 + (cias.iva /100))), 2) importe,
        ROUND(cxc.importe - (cxc.importe / (1 + (cias.iva /100))), 2) iva,
        cxc.importe total, cxc.importe depositado, cxc.banco          
        FROM (
            SELECT ing.id idpago, cxc.referencia, cli.nombre, SUBSTRING(cxc.referencia,3) folio,                
            ing.fechap fechaPago, IFNULL(bancos.nombre,'-----') banco, cxc.recibo, cxc.fecha, cxc.concepto,
            ROUND(SUM(cxc.importe),2) importe, IFNULL(ROUND(SUM(cxc_h.importe),2),0) notas                
            FROM cli,cxc 
            LEFT JOIN cxc cxc_h ON cxc.cia = cxc_h.cia AND SUBSTRING(cxc.referencia,3) = SUBSTRING(cxc_h.referencia,4)
            LEFT JOIN ing ON cxc.cia = ing.cia AND cxc.recibo = ing.id 
            LEFT JOIN bancos ON ing.cia = bancos.cia AND ing.banco = bancos.id
            WHERE 1 = 1 
            AND cxc.cia = cli.cia
            AND cxc.cuenta = cli.id
            AND cxc.cia = " . $UsuarioSesion->getCia() . "
            AND cxc.referencia LIKE 'F-%' AND cxc.tm = 'H'
            GROUP BY cxc.referencia,cxc.recibo
        ) cxc 
        LEFT JOIN fc ON fc.folio = cxc.folio AND fc.cia = " . $UsuarioSesion->getCia() . "
        LEFT JOIN cias ON cias.id = fc.cia
        WHERE 
        DATE(cxc.fechaPago) BETWEEN DATE('$FechaI') AND  DATE('$FechaF') ";
}

if (!empty($Status) && $Status !== "Todos") {
    $selectPagoFacturas .= " AND fc.status LIKE '$Status'";
}


$selectPagoFacturas .= " ORDER BY cxc.banco,cxc.fechaPago,cxc.recibo,cxc.folio";


/* Relacion de salidas y productos */

$selectRelacionSalidas = "
        SELECT ns.id,date(ns.fecha) fecha,nsd.producto,inv.descripcion,nsd.modelo,nsd.numero_serie, 
       nsd.cantidad, grupos.nombre,cli.alias,ns.estacion,nsd.grupo,nsd.modelo 
       FROM cli,ns,nsd 
       LEFT JOIN inv ON nsd.producto = inv.id AND inv.cia = " . $UsuarioSesion->getCia() . "
       LEFT JOIN grupos ON nsd.grupo = grupos.id AND grupos.cia = " . $UsuarioSesion->getCia() . "
       WHERE TRUE 
       AND cli.cia = ns.cia
       AND ns.estacion = cli.id AND ns.id = nsd.id AND ns.cia = " . $UsuarioSesion->getCia() . "
       AND DATE(ns.fecha) BETWEEN DATE('$FechaI') AND DATE('$FechaF') 
       ORDER BY nsd.grupo,nsd.producto";


/* Consultas para reporte de relacion de ordenes de pago */

$selectOrdenesPago = "
        SELECT ordpagos.id orden, ordpagos.pagonumero, ordpagos.fecha, prv.alias proveedor, 
        ordpagos.rubro, ordpagos.concepto, ordpagos.status, ordpagos.importe
        FROM ordpagos 
        LEFT JOIN prv ON ordpagos.cia = prv.cia AND ordpagos.proveedor = prv.id 
        WHERE 1 = 1
        AND ordpagos.cia = " . $UsuarioSesion->getCia() . "
        AND ordpagos.fecha BETWEEN DATE('$FechaI') AND  DATE('$FechaF')";

if (!empty($Status) && $Status !== "Todos") {
    $selectOrdenesPago .= " AND ordpagos.status LIKE '%$Status%'";
}
if (!empty($Rubro) && $Rubro !== "Todos") {
    $selectOrdenesPago .= " AND ordpagos.rubro LIKE '%$Rubro%'";
}
$selectOrdenesPago .= "
        ORDER BY ordpagos.pagonumero,ordpagos.id";

/* Consulta para relacion de egresos */

$selectEgresos = "
        SELECT egresos.id pago, bancos.nombre banco, egresos.fecha, prv.alias proveedor, 
        ordpagos.rubro, ordpagos.concepto, ordpagos.status, 
        ordpagos.total cotizado,egresos.pagoreal,egresos.otropago,
        egresos.pagoreal + egresos.otropago total
        FROM prv, ordpagos, egresos
        LEFT JOIN bancos ON egresos.cia = bancos.cia AND egresos.banco = bancos.id 
        WHERE 1 = 1 
        AND prv.cia = ordpagos.cia
        AND egresos.ordendepago = ordpagos.id 
        AND ordpagos.proveedor = prv.id 
        AND egresos.cia = " . $UsuarioSesion->getCia() . "
        AND egresos.fecha BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ";

if (!empty($Status) && $Status !== "Todos") {
    $selectEgresos .= " AND ordpagos.status LIKE '%$Status%'";
}
if (!empty($Rubro) && $Rubro !== "Todos") {
    $selectEgresos .= " AND ordpagos.rubro LIKE '%$Rubro%'";
}

$selectEgresos .= "
        ORDER BY egresos.banco, egresos.fecha";

/* Consulta para relacion de gastos - egresos */

$selectEgresosGastos = "
        SELECT egresos.id pago, LPAD(egresos.folio,4,0) folio, egresos.fecha, prv.alias proveedor, 
        ordpagos.rubro, egresos.formadepago forma, fp.descripcion, ordpagos.concepto, ordpagos.status, 
        ordpagos.importe, ordpagos.iva, ordpagos.iva_ret, ordpagos.isr,
        egresos.pagoreal + egresos.otropago total, bancos.nombre banco
        FROM prv, ordpagos, egresos
        LEFT JOIN bancos ON egresos.cia = bancos.cia AND egresos.banco = bancos.id 
        LEFT JOIN cfdi33_c_fpago fp ON egresos.formadepago = fp.clave
        WHERE 1 = 1 
        AND prv.cia = ordpagos.cia
        AND egresos.ordendepago = ordpagos.id 
        AND ordpagos.proveedor = prv.id 
        AND egresos.cia = " . $UsuarioSesion->getCia() . "
        AND egresos.fecha BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ";

if (!empty($Status) && $Status !== "Todos") {
    $selectEgresosGastos .= " AND ordpagos.status LIKE '%$Status%'";
}
if (!empty($Rubro) && $Rubro !== "Todos") {
    $selectEgresosGastos .= " AND ordpagos.rubro LIKE '%$Rubro%'";
}
if (!empty($Forma) && $Forma !== "Todos") {
    $selectEgresosGastos .= " AND egresos.formadepago = '$Forma'";
}

$selectEgresosGastos .= "
        ORDER BY egresos.banco, egresos.formadepago, egresos.fecha ASC";



/* Consulta para relacion de actividades */

$selectActividades = "
        SELECT * FROM (
            SELECT sub.*,
            CASE 
            WHEN sub.periodo = 1 THEN 'DIARIA'
            WHEN sub.periodo = 2 THEN 'SEMANAL' 
            WHEN sub.periodo = 3 THEN 'QUINCENAL'
            WHEN sub.periodo = 4 THEN 'MENSUAL'
            WHEN sub.periodo = 5 THEN 'ANUAL'
            END revision,
            CASE 
            WHEN sub.periodo = 1 THEN DATE_ADD(sub.fecha, INTERVAL sub.lapso DAY) 
            WHEN sub.periodo = 2 THEN DATE_ADD(sub.fecha, INTERVAL sub.lapso WEEK) 
            WHEN sub.periodo = 3 THEN DATE_ADD(sub.fecha, INTERVAL (sub.lapso * 2) WEEK ) 
            WHEN sub.periodo = 4 THEN DATE_ADD(sub.fecha, INTERVAL sub.lapso MONTH) 
            WHEN sub.periodo = 5 THEN DATE_ADD(sub.fecha, INTERVAL sub.lapso YEAR) 
            END promesa
            FROM (
                    SELECT act.id tarea,act.tipo, IFNULL(actd.fecha, CURRENT_DATE()) fecha, 
                    act.descripcion, act.periodo,act.lapso
                    FROM act
                    LEFT JOIN actd ON act.id = actd.id
                    WHERE 1 = 1
                    AND act.cia = " . $UsuarioSesion->getCia() . "
            ) sub
        ) sub0
        WHERE 1 = 1
        AND sub0.promesa <= DATE('$Fecha')";

if (!empty($Rubro) && $Rubro !== "Todos") {
    $selectActividades .= "AND sub0.tipo = '$Rubro'";
}

/* Consulta para relacion de notas de credito */

$selectRelacionNotasCredito = "
        SELECT CONCAT('NC-',nc.folio) folio,DATE(nc.fecha) fecha, nc.cliente, cli.nombre,
        CONCAT('F-',nc.factura) factura, nc.concepto, UPPER(nc.uuid) uuid,
        nc.importe,nc.iva,nc.total,nc.status
        FROM nc,cli
        WHERE TRUE 
        AND nc.cliente = cli.id 
        AND nc.cia = cli.cia
        AND nc.cia = " . $UsuarioSesion->getCia() . "
        AND DATE(nc.fecha) BETWEEN DATE('$FechaI') AND  DATE('$FechaF')
        ";
if (!empty($Status) && $Status !== "Todos") {
    $selectRelacionNotasCredito .= " AND nc.status LIKE '%$Status%'";
}

$selectRelacionNotasCredito .= " ORDER BY nc.id";

/* Consulta para recibo de Orden de compra*/

$selectOrdenHeader = "
        SELECT ordpagos.id,fecha,concepto,solicito,importe,ordpagos.observaciones,
        status,prv.nombre alias,ordpagos.proveedor 
        FROM ordpagos 
        LEFT JOIN prv ON ordpagos.proveedor = prv.id 
        WHERE ordpagos.id = $busca";

 $selectDetalleOrden = "
        SELECT ordpagos.id,fecha,concepto,solicito,importe,ordpagos.observaciones,
        status,prv.nombre alias,ordpagos.proveedor 
        FROM ordpagos 
        LEFT JOIN prv ON ordpagos.proveedor = prv.id 
        WHERE ordpagos.id = $busca";
 
 /* Consulta para nota de entrada de productos*/

$selectNotaEntrada = "
        SELECT LPAD(ne.id,5,0) folio, ne.factura, ne.fechafac, ne.fecha_entra, 
        IFNULL(authuser.uname,'Compras') responsable,
        prv.nombre, prv.rfc, ne.cantidad 
        FROM ne 
        LEFT JOIN prv ON ne.proveedor = prv.id
        LEFT JOIN authuser ON ne.responsable = authuser.id 
        WHERE TRUE AND ne.id = $busca;";

$selectNotaEntradaDetalle = "SELECT ned.*,inv.descripcion FROM ned,inv WHERE ned.producto = inv.id AND ned.id = $busca";

 /* Consulta para nota de salida de productos*/

$selectNotaSalida= "
        SELECT ns.factura, ns.fecha, ns.responsable, LPAD(ns.id,5,0) folio,
        cli.nombre, cli.rfc, ns.concepto 
        FROM cli, ns
        WHERE ns.cliente = cli.id AND ns.cia = cli.cia AND ns.id = $busca;";

$selectNotaSalidaDetalle = "
        SELECT nsd.producto, inv.descripcion, nsd.cantidad, nsd.costo, (nsd.cantidad * nsd.costo) total
        FROM nsd 
        LEFT JOIN inv ON nsd.producto = inv.id AND inv.cia = " . $UsuarioSesion->getCia() . " AND nsd.tipo = 1
        WHERE nsd.id = $busca";