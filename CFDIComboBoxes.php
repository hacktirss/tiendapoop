<?php

/* 
 * CFDIComboBoxes
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jun 2017
 */

class ComboboxUsoCFDI {
    static function generate($comboID) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_uso WHERE status = 1");

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE USO CFDI</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . str_replace(' ' , '&nbsp;', $rs['clave']) . " | " . $rs['descripcion']. "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxUnidades {
    static function generate($comboID) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT LPAD(clave, 3, ' ') clave, nombre FROM cfdi33_c_unidades WHERE status = 1");


        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE UNIDAD</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . trim($rs['clave']) . "\">" . str_replace(' ' , '&nbsp;', $rs['clave']) . " | " . $rs['nombre']. "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxDivison {
    static function generate($comboID, $tipo) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_categorias WHERE clave_padre = '0' " . ($tipo==='' ? "" : " AND tipo = '" . $tipo . "'"));

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE DIVISIÓN</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['descripcion'] . "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxGrupo {
    static function generate($comboID, $division) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_categorias WHERE clave_padre = '" . $division . "'");

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE GRUPO</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['descripcion'] . "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxClase {
    static function generate($comboID, $grupo) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_categorias WHERE clave_padre = '" . $grupo . "'");

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE CLASE</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['descripcion'] . "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxProductoServicio {
    static function generate($comboID, $clase) {
        $link = conectarse();

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE CONCEPTO</option>";
        $html = $html . "<option value=\"01010101\">No existe en el catálogo</option>";
        if ($clase!='') {
            $qry = mysqli_query($link, "SELECT clave, nombre FROM cfdi33_c_conceptos WHERE clave LIKE '" . substr($clase, 0, 6) . "%'");

            while(($rs = mysqli_fetch_array($qry))) {
                $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['nombre'] . "</option>";
            }
        }
        $html = $html . "</select>";
        echo $html;
    }
}
class ComboboxCommonProductoServicio {
    static function generate($comboID) {
        $link = conectarse();

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\"><option value=\"\">SELECCIONE CONCEPTO</option>";
        $html = $html . "<option value=\"01010101\">No existe en el catálogo</option>";
        $qry = mysqli_query($link, "SELECT C.clave, C.nombre FROM cfdi33_c_conceptos C WHERE status = 1");

        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['nombre'] . "</option>";
        }

        $html = $html . "</select>";
        echo $html;
    }
}
class ComboboxFormaDePago {
    static function generate($comboID, $complements = "" ) {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_fpago WHERE status = 1");

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\" $complements><option value=\"\">SELECCIONE FORMA DE PAGO</option>";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['descripcion']. "</option>";
        }
        $html = $html . "<option value=\"98\">NA | No Aplica</option>";
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxMetodoDePago {
    static function generate($comboID, $version = '3.3') {
        $link = conectarse();

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_mpago WHERE status = 1");

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\">";
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['descripcion']. "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}

class ComboboxTipoRelacion {
    static function generate($comboID, $options = "") {
        $link = conectarse();

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\" $options>";
        $html = $html . "<option value=\"\">SELECCIONE EL TIPO DE RELACI&Oacute;N</option>";

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_trelacion WHERE status = 1");
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['descripcion']. "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}
class ComboboxRegimen {
    static function generate($comboID) {
        $link = conectarse();

        $html = "<select name=\"" . $comboID . "\" id=\"" . $comboID . "\">";
        $html = $html . "<option value=\"\">SELECCIONE EL TIPO DE REGIMEN</option>";

        $qry = mysqli_query($link, "SELECT clave, descripcion FROM cfdi33_c_regimenes WHERE status = 1");
        while(($rs = mysqli_fetch_array($qry))) {
            $html = $html . "<option value=\"" . $rs['clave'] . "\">" . $rs['clave'] . " | " . $rs['descripcion']. "</option>";
        }
        $html = $html . "</select>";

        echo $html;
    }
}

