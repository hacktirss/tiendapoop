<?php

if (filter_input(INPUT_GET, 'sTable', FILTER_SANITIZE_STRING) !== null
        && filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING) !== null
        && filter_input(INPUT_GET, 'sSearch', FILTER_SANITIZE_STRING) !== null) {

    $jsonResult = array();

    foreach($_GET as $key=>$value){
        //error_log($key . ' => ' . $value);
    }

    $sTable     = filter_input(INPUT_GET, 'sTable', FILTER_SANITIZE_STRING);
    $sText      = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING);
    $sSearch    = filter_input(INPUT_GET, 'sSearch', FILTER_SANITIZE_STRING);

    $query = "SELECT DISTINCT " .  $sSearch . " data, " .  $sSearch . " value FROM " . $sTable . " WHERE " . $sSearch . " REGEXP '" . str_replace(' ', '.*', $sText) . "' ORDER BY " . $sSearch;
    //error_log($query);

    include './lib/lib.php';
    $connection = conectarse();
    
    $result  = mysqli_query($connection,$query);

    if (!$result) {
        error_log('Invalid query: ' . $query . ' ' . mysqli_error($connection));
        die('Invalid query: ' . $query . ' ' . mysqli_error($connection));
    }

    while($rg=mysqli_fetch_array($result)) {
        $jsonResult[] = $rg;
    }

    if ($connection) {
        mysqli_close($connection);
    }

    $jsonString = json_encode(array('suggestions'=>$jsonResult));
    //error_log($jsonString);

    if ($jsonString==null) {
        error_log(json_last_error());
    }

    echo $jsonString;
}// if valid parameters
?>
