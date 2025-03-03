<?php
function apiValidateALIAS($usuario) {

    // API censurada

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => '',  // (No está porque es privada)
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('validateALIAS' => $usuario),
    ));

    // Ejecuta la solicitud y almacena la respuesta en formato JSON en la variable
    $response = curl_exec($curl);

    // Verifica si hay errores en la solicitud cURL
    if (curl_errno($curl)) {
        echo 'Error cURL: ' . curl_error($curl);
        return null;
    }

    // Decodifica el JSON recibido y lo almacena en un array asociativo
    $responseJSON = json_decode($response, true);

    // Decodifica el JSON que contiene toda la respuesta y almacena el contenido de "dato" en un array asociativo
    $dato = json_decode($responseJSON['dato'], true);
    
    // Almacena el contenido de responseStatus en una variable
    $responseStatus = $dato['responseStatus'];

    // Array que contendrá la respuesta a emitir
    $responseAPI = array();

    // Almacena el valor de responseStatus en la variable que se emitirá como JSON
    if ($responseStatus == "notused") {
        $responseAPI["response"] = "notused";
    } else if ($responseStatus == "used") {
        $responseAPI["response"] = "used";
    } else {
        $responseAPI["response"] = "error";
    }

    // Cierra la sesión cURL
    curl_close($curl);

    // Devuelve la respuesta API como un array
    return $responseAPI;
}

// Si recibe por POST el usuario realiza la llamada a la función y emite el resultado, en caso contrario no hace nada para poderse incluir en recibirDatosRegistro.php
if (isset($_POST["validateALIAS"])) {
    $usuario = $_POST["validateALIAS"];
    $responseAPI = apiValidateALIAS($usuario);
    
    if ($responseAPI !== null) {
        // Emite el JSON con la respuesta de la API
        echo json_encode($responseAPI);
    }
}
?>