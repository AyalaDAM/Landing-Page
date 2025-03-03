<?php
/**
 * Función para recibir JSONs del front, hacer llamadas a las funciones de backend para validar los datos y mostrar en el HTML
 * mensajes de válido o no válido. Devuelve un JSON con un valor "valid" true o false según si el dato o datos son validos o
 * alguno no lo es.
 */
include_once "validadoresDatos.php";

function procesarValidacionesGenerales() {

    // Recibe un JSON
    $rawData = file_get_contents("php://input");

    // Si está vacío devuelve un JSON con un error
    if (empty($rawData)) {
        echo json_encode(['error' => 'No data received']);
        return;
    }

    // Si no está vacío decodifica el JSON y lo almacena en una variable
    $dataArray = json_decode($rawData, true);

    // Si hay algún error con el JSON devuelve un JSON con un error
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    // Variable array que contendrá el resultado de las validaciones
    $response = array();
    $response["validations"] = array();

    // Valida nombre, apellido1, apellido2
    foreach (['nombre', 'apellido1', 'apellido2'] as $campo) {
        if (isset($dataArray[$campo])) {
            $campoValue = $dataArray[$campo];
            $response["validations"][$campo] = validarNombreApellidos($campoValue);
        }
    }

    // Valida fecha de nacimiento
    if (isset($dataArray['fecNac'])) {
        $fecNac = $dataArray['fecNac'];
        $response["validations"]['fecNac'] = validarFechaNacimiento($fecNac);
    }

    // Valida domicilio
    if (isset($dataArray['domicilio'])) {
        $domicilio = $dataArray['domicilio'];
        $response["validations"]['domicilio'] = validarDomicilio($domicilio);
    }

    // Valida DNI o NIE
    if (isset($dataArray['dni'])) {
        $dni = $dataArray['dni'];
        $response["validations"]['dni'] = validarDNI($dni);
    } else if (isset($dataArray['nie'])) {
        $nie = $dataArray['nie'];
        $response["validations"]['nie'] = validarNIE($nie);
    }

    // Valida email
    if (isset($dataArray['email'])) {
        $email = $dataArray['email'];
        $response["validations"]['email'] = validarEmail($email);
    }

    // Valida telefono
    if (isset($dataArray['telefono'])) {
        $telefono = $dataArray['telefono'];
        $response["validations"]['telefono'] = validarTelefono($telefono);
    }

    // 

    /* Determina si todas las validaciones pasaron, es decir, que todas las validations sean true de forma que, si solo había
    un valor en el JSON validatios será igual a valid, mientras que si había más de un valor valid solo será true si todos los
    validations son true */
    $response["valid"] = true;
    foreach ($response["validations"] as $key => $valid) {
        if ($valid === false) {
            $response["valid"] = false;
            break;
        }
    }

    // Emite el array response como un JSON
    echo json_encode($response);
}

// Ejecuta la función
procesarValidacionesGenerales();
?>