<?php

// Función para obtener la información del código postal
function apiValidateCodigoPostal($zipCode) {
    // Verifica si el código postal está vacío, si es así, devuelve un error
    if (empty($zipCode)) {
        return array("response" => "error", "message" => "Código postal no establecido.");
    }

    // API censurada

    // Inicializa la sesión cURL para hacer una petición HTTP
    $curl = curl_init();

    // Configuración de las opciones de la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => '' . $zipCode,  // Especifica la URL a la que realizar la solicitud (No está porque es privada)
        CURLOPT_RETURNTRANSFER => true, // Indica que la respuesta de la petición se debe devolver como una cadena de texto
        CURLOPT_ENCODING => '', // Vacío para indicar que cualquier tipo de codificación de respuesta será aceptado
        CURLOPT_MAXREDIRS => 10,  // Limita el número de redirecciones permitidas en la petición
        CURLOPT_TIMEOUT => 0, // Establece el tiempo máximo de espera para la ejecución de la solicitud
        CURLOPT_FOLLOWLOCATION => true, // Permite que cURL siga las redirecciones automáticamente si las hubiera
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,  // Define la versión de protocolo HTTP que se utilizará
        CURLOPT_CUSTOMREQUEST => 'GET', // Define el tipo de solicitud HTTP a realizar
        CURLOPT_HTTPHEADER => array(''),    // Encabezado que se enviará junto a la solicitud (No está porque es privado)
    ));

    // Ejecuta la solicitud y almacena la respuesta en la variable
    $response = curl_exec($curl);

    // Cierra la sesión cURL
    curl_close($curl);

    // Verifica si hubo un error en la solicitud cURL
    if (curl_errno($curl)) {
        return array("response" => "error", "message" => "Error en la solicitud cURL: " . curl_error($curl));
    }

    // Decodifica el JSON recibido
    $decodedResponse = json_decode($response, true);

    // Si no se pudo decodificar correctamente, devuelve un error
    if ($decodedResponse === null) {
        return array("response" => "error", "message" => "Error al decodificar la respuesta de la API.");
    }

    // Devuelve el array de la API decodificado
    return $decodedResponse;
}

// Si recibe por GET el código postal realiza la llamada a la función y emite el resultado, en caso contrario no hace nada para poderse incluir en recibirDatosRegistro.php
if (isset($_GET["codPostal"])) {
    $zipCode = $_GET["codPostal"];
    $responseAPI = apiValidateCodigoPostal($zipCode);

    if ($responseAPI !== null) {
      // Emite el JSON con la respuesta de la API
      echo json_encode($responseAPI);
  }
}
?>