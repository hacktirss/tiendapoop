<?php

include_once ("data/ListaDAO.php");

/**
 * Description of ListasCatalogo
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class ListasCatalogo {

    public static function listaNombreCatalogo($nombreSelect, $nombreCatalogo, $adicional = "", $opciones = "") {
        $listaDAO = new ListaDAO();
        $array = $listaDAO->getComboBox($nombreCatalogo);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        if ($adicional !== "") {
            $html .= "<option value='" . $adicional . "'>" . $adicional . " </option>";
        }
        foreach ($array as $key => $value) {
            $html .= "<option value='" . $key . "'>" . $value . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getProductosByInventario($nombreSelect, $rubro, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT inv.id producto,inv.descripcion FROM inv WHERE inv.rubro IN($rubro) AND inv.activo = 'Si' AND inv.cia = " . $ciaSesion->getId() . ";";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[producto] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getClientes($nombreSelect, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', tipodepago, ' | ', nombre) descripcion FROM cli WHERE activo = 'Si' AND cli.cia = " . $ciaSesion->getId() . ";";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getProveedores($nombreSelect, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', proveedorde, ' | ', nombre) descripcion FROM prv WHERE 1 = 1 AND prv.activo = 'Si' AND prv.cia = " . $ciaSesion->getId() . ";";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getBancos($nombreSelect, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', nombre) descripcion FROM bancos WHERE 1 = 1 AND bancos.cia = " . $ciaSesion->getId() . " ORDER BY id ASC;";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getFormasDePago($nombreSelect, $opciones = "", $adicional = "") {
        $mysqli = getConnection();
        $selectPosiciones = "SELECT clave value,CONCAT(clave, ' | ', descripcion) descripcion FROM cfdi33_c_fpago WHERE 1 = 1 ORDER BY clave ASC;";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        if ($adicional !== "") {
            $html .= "<option value='" . $adicional . "'>" . $adicional . " </option>";
        }
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

    public static function getTipoDeMovimiento($nombreSelect, $adicional = "", $opciones = "") {
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        if ($adicional !== "") {
            $html .= "<option value='" . $adicional . "'>" . $adicional . " </option>";
        }
        $html .= "<option value='C'>Cargo</option>";
        $html .= "<option value='H'>Abono</option>";
        $html .= "</select>";
        echo $html;
    }
    
    public static function getGrupos($nombreSelect, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', nombre) descripcion FROM grupos WHERE 1 = 1 AND grupos.cia = " . $ciaSesion->getId() . " ORDER BY id ASC;";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }
    
    public static function getCategorias($nombreSelect, $opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', nombre) descripcion FROM categorias WHERE 1 = 1 AND categorias.cia = " . $ciaSesion->getId() . " ORDER BY id ASC;";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }
    
    public static function getSubCategorias($nombreSelect, $subcategoria = 0,$opciones = "") {
        $mysqli = getConnection();
        $ciaSesion = getSessionCia();
        $selectPosiciones = "SELECT id value,CONCAT(id, ' | ', nombre) descripcion FROM subcategorias WHERE 1 = 1 AND subcategorias.categoria = " . $subcategoria . " ORDER BY id ASC;";
        $result = $mysqli->query($selectPosiciones);
        $html = "<select name='" . $nombreSelect . "' id='" . $nombreSelect . "'  $opciones>";
        while ($rg = $result->fetch_array()) {
            $html .= "<option value='" . $rg[value] . "'>" . $rg[descripcion] . " </option>";
        }
        $html .= "</select>";
        echo $html;
    }

}
