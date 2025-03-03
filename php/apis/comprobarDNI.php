<?php
// Función para comprobar el DNI en la API de Pastón.es
function apiValidateDNI($dni) {

    // API censurada

    // Inicializa la sesión cURL para hacer una petición HTTP
    $curl = curl_init();

    // Configuración de las opciones de la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => '',  // Especifica la URL a la que relizar la solicitud (No está porque es privada)
        CURLOPT_RETURNTRANSFER => true, // Indica que la respuesta de la petición se debe devolver como una cadena de texto
        CURLOPT_ENCODING => '', // Vacío para indicar que cualquier tipo de codificación de respuesta será aceptado
        CURLOPT_MAXREDIRS => 10,  // Limita el número de redirecciones permitidas en la petición
        CURLOPT_TIMEOUT => 0, // Establece el tiempo máximo de espera para la ejecución de la solicitud
        CURLOPT_FOLLOWLOCATION => true, // Permite que cURL siga las redirecciones automáticamente si las hubiera
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,  // Define la versión de protocolo HTTP que se utilizará
        CURLOPT_CUSTOMREQUEST => 'POST',  // Define el tipo de solicitud HTTP a realizar
        CURLOPT_POSTFIELDS => array('validateDNI' => $dni), // Define los datos que se van a enviar en la solicitud
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

// Si recibe por POST el DNI realiza la llamada a la función y emite el resultado, en caso contrario no hace nada para poderse incluir en recibirDatosRegistro.php
if (isset($_POST["validateDNI"])) {
    $dni = $_POST["validateDNI"];
    $responseAPI = apiValidateDNI($dni);
    
    if ($responseAPI !== null) {
        // Emite el JSON con la respuesta de la API
        echo json_encode($responseAPI);
    }
}
?>