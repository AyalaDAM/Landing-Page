<?php

// Función para validar el correo electrónico utilizando la API censurada
function apiValidateEmail($email) {
    // Verifica si el email se encuentra vacío, en caso de que no se haya recibido
    if (empty($email)) {
        return array("response" => "error", "message" => "Email no establecido.");
    }

    // Inicializa la sesión cURL para hacer una petición HTTP
    $curl = curl_init();

    // Configuración de las opciones de la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => '',  // Especifica la URL de la API (No está porque es privada)
        CURLOPT_RETURNTRANSFER => true,  // Recibe la respuesta de la API como una cadena
        CURLOPT_ENCODING => '',         // Acepta cualquier tipo de codificación
        CURLOPT_MAXREDIRS => 10,        // Limita el número de redirecciones permitidas
        CURLOPT_TIMEOUT => 0,           // Establece el tiempo máximo de espera para la solicitud
        CURLOPT_FOLLOWLOCATION => true, // Permite seguir las redirecciones
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,  // Define la versión HTTP
        CURLOPT_CUSTOMREQUEST => 'POST',  // El método HTTP es POST
        CURLOPT_POSTFIELDS => array('validateMAIL' => $email),  // Envia el correo en la solicitud
    ));

    // Ejecuta la solicitud cURL y almacena la respuesta
    $response = curl_exec($curl);

    // Cierra la sesión cURL
    curl_close($curl);

    // Verifica si hubo un error en la solicitud
    if ($response === false) {
        return array("response" => "error", "message" => "Error en la solicitud a la API.");
    }

    // Decodifica la respuesta JSON en un array asociativo
    $responseJSON = json_decode($response, true);

    // Verifica si el campo 'dato' existe en la respuesta
    if (isset($responseJSON['dato'])) {

        // Decodifica el campo 'dato' para verificar el estado del email
        $dato = json_decode($responseJSON['dato'], true);

        // Verifica si hay errores en el campo email
        if (isset($dato['fieldErrors']['email'])) {

            return array("response" => "error");

        } else {
            // Si el campo responseStatus existe, se procesa su valor
            $responseStatus = isset($dato['responseStatus']) ? $dato['responseStatus'] : null;

            // Devuelve el resultado basado en el valor de responseStatus
            if ($responseStatus == "notused") {
                return array("response" => "notused");
            } elseif ($responseStatus == "used") {
                return array("response" => "used");
            } else {
                return array("response" => "error");
            }
        }
    } else {
        return array("response" => "error", "message" => "La respuesta no ha sido la esperada");
    }
}

// Si recibe por POST el email realiza la llamada a la función y emite el resultado, en caso contrario no hace nada para poderse incluir en recibirDatosRegistro.php
if (isset($_POST["validateMAIL"])) {
    $email = $_POST["validateMAIL"];
    $responseAPI = apiValidateEmail($email);
    
    // Emite la respuesta en formato JSON
    echo json_encode($responseAPI);
}
?>