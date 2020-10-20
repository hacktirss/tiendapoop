<?php

include_once ("lib/lib.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$subcategorias = array();

if ($request->hasAttribute("menu")) {
    $categoria = $request->getAttribute("menu");

    $selectSubcategorias = "SELECT * FROM subcategorias WHERE categoria = " . $categoria . " ORDER BY id;";
    $rows_ = utils\ConnectionUtils::getRowsFromQuery($selectSubcategorias);
    
    foreach ($rows_ as $value) {
        $subcategorias[] = array("id" => $value["id"], "nombre" => $value["nombre"]);
    }
}

echo json_encode($subcategorias);