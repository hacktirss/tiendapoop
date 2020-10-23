<?php

#Librerias
include_once ("data/ProductoDAO.php");

use com\softcoatl\utils as utils;

$mysqli = utils\IConnection::getConnection();
$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();
$Return = "productos.php?";
$usuarioSesion = getSessionUsuario();
$CiaSesion = getSessionCia();

$objectDAO = new ProductoDAO();

if ($request->hasAttribute("Boton") && $request->getAttribute("Boton") !== utils\Messages::OP_NO_OPERATION_VALID) {
    $Msj = utils\Messages::MESSAGE_NO_OPERATION;

    $objectVO = new ProductoVO();
    $objectVO->setCia($usuarioSesion->getCia());
    if (is_numeric($sanitize->sanitizeInt("busca"))) {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
    } else {
        $objectVO->setId(IncrementaId($objectDAO::TABLA));
    }
    $objectVO->setRfc($CiaSesion->getRfc());
    $objectVO->setDescripcion($sanitize->sanitizeString("Descripcion"));
    $objectVO->setUmedida($sanitize->sanitizeString("Umedida"));
    $objectVO->setPrecio($sanitize->sanitizeFloat("Precio"));
    $objectVO->setMenudeo($sanitize->sanitizeFloat("Menudeo"));
    $objectVO->setMayoreo($sanitize->sanitizeFloat("Mayoreo"));
    $objectVO->setCostopromedio($sanitize->sanitizeString("Costopromedio"));
    $objectVO->setObservaciones($sanitize->sanitizeString("Observaciones"));
    $objectVO->setExistencia($sanitize->sanitizeString("Existencia"));
    $objectVO->setGrupo($sanitize->sanitizeInt("Grupo"));
    $objectVO->setCategoria($sanitize->sanitizeInt("Categoria"));
    $objectVO->setSubcategoria($sanitize->sanitizeInt("Subcategoria"));
    $objectVO->setActivo($sanitize->sanitizeString("Activo"));
    $objectVO->setCodigo($sanitize->sanitizeString("Codigo"));

    //error_log(print_r($objectVO, TRUE));
    try {
        if ($request->getAttribute("Boton") === utils\Messages::OP_ADD) {
            if (($id = $objectDAO->create($objectVO)) > 0) {
                $Msj = utils\Messages::RESPONSE_VALID_CREATE;
            } else {
                $Msj = utils\Messages::RESPONSE_ERROR;
            }
        } elseif ($request->getAttribute("Boton") === utils\Messages::OP_UPDATE) {
            if ($objectDAO->update($objectVO)) {
                $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
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


if ($request->hasAttribute("Upload") && $request->getAttribute("Upload") !== utils\Messages::OP_NO_OPERATION_VALID) {

    try {
        $objectVO = $objectDAO->retrieve($sanitize->sanitizeInt("busca"), "id", $usuarioSesion->getCia());
        $Return = "productose.php?busca=" . $objectVO->getId();
        error_log("Load image...");
        $image = $_FILES['Imagen']['name'];
        error_log(print_r($_FILES["Imagen"], TRUE));
        $imgContent = file_get_contents($_FILES["Imagen"]["tmp_name"]);
        //error_log(print_r($imgContent, TRUE));
        if ($objectDAO->updateImage($objectVO, $imgContent)) {
            $Msj = utils\Messages::RESPONSE_VALID_UPDATE;
        } else {
            $Msj = utils\Messages::RESPONSE_ERROR;
        }
        $Return .= "&Msj=" . urlencode($Msj);
    } catch (Exception $ex) {
        error_log("Error: " . $ex);
    }


    header("Location: $Return");
}