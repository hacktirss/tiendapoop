<?php

function getConnection($host, $user, $password) {
    $omiConn = new mysqli($host, $user, $password);

    if ($omiConn->connect_errno 
            || !$omiConn->select_db("detisa")
            || !($psSetLocale = $omiConn->prepare("SET lc_time_names = 'es_MX'"))
            || !$psSetLocale->execute()) {
        throw new Exception("Error de obteniendo conexión: (" . $omiConn->connect_errno . ") " . $omiConn->connect_error);
    }
    return $omiConn;
}

/**
 * 
 * @param \mysqli $omiConn
 * @param type $sqlExp
 * @param type $paramString
 * @param type $queryParameters
 * @return \mysqli_stmt
 * @throws Exception
 */
function preparedStatement($omiConn, $sqlExp, $paramString, $queryParameters) {
    $psValues = array();
    $psFlags = "";

    if (!($preparedSmt = $omiConn->prepare($sqlExp))) {
        throw new Exception("Error de conexión ps: (" . $omiConn->errno . ") " . $omiConn->error);
    }

    $parameters = explode(",", $paramString);
    for ($i = 0; $i < count($parameters); $i++) {
        $psFlags = "s" . $psFlags;
    }
    array_push($psValues, $psFlags);
    foreach ($parameters as $parameter) {
        array_push($psValues, filter_var($queryParameters[$parameter], FILTER_SANITIZE_STRING));
    }
    call_user_func_array(array($preparedSmt, 'bind_param'), &$psValues);
    return $preparedSmt;
}   

function getFieldNames($rsMetaData) {
    $fieldNames = array();
    foreach ($rsMetaData as $key => $value) {
        foreach ($value as $key1 => $value1) {
            array_push($fieldNames, $value1);
            break;
        }
    }
    return $fieldNames;
}
