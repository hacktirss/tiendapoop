<?php

//carpeta donde almacenaremos las imegenes que subiremos
$output_dir = "archivos/";
$station = $_REQUEST['station'];

if (isset($_FILES["myfile"])) {
    $ret = array();
    //myfile es el valor fileName que establecimos en el JS
    $error = $_FILES["myfile"]["error"];
    //You need to handle  both cases
    //If Any browser does not support serializing of multiple files using FormData() 
    if (!is_array($_FILES["myfile"]["name"])) { //Single file
        $fileName = $_FILES["myfile"]["name"];
        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $station . $fileName);
        $ret[] = $fileName;
    } else { //Multiple files, file[]
        //Cuando subimos multiples archivos
        $fileCount = count($_FILES["myfile"]["name"]);
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $_FILES["myfile"]["name"][$i];
            move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $output_dir . $station . $fileName);
            $ret[] = $fileName;
        }
    }
    echo json_encode($ret);
}
?>