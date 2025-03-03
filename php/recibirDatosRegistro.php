<?php
/**
 * Funciones para recoger los datos de cada parte del formulario, validarlas emitiendo su correspondiente JSON con Success true
 * o false y generar un log tanto si es correcto como si no.
 */

include_once "validadoresDatos.php";
include_once "apis/comprobarDNI.php";
include_once "apis/comprobarMAIL.php";
include_once "apis/codigosPostales.php";
include_once "apis/comprobarALIAS.php";

// Función para generar un log de la fecha, IP, datos introducidos y resultado producido cada vez que se intentan introducir datos
function generaLog($jsonSuccess, $fechaHora, $ip, $jsonData) {

    $logData = "Fecha y Hora: " . $fechaHora . "\n";
    $logData .= "IP: " . $ip . "\n";
    $logData .= "Datos: " . $jsonData . "\n";
    $logData .= "Success: " . $jsonSuccess . "\n";
    $logData .= "-------------------------------------------------------------\n";
    $logFile = "../logs/log_registros.txt";

    if (!file_exists($logFile)) {
        touch($logFile);
    }

    file_put_contents($logFile, $logData, FILE_APPEND);
}

// Función para enviar JSON a otro archivo externo que insertará los datos del registro en una base de datos
function enviarRegistro($data) {
    $ch = curl_init();

    // Construye la URL completa basada en el script que se está ejecutando
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/enviarRegistro.php';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));

    $response = curl_exec($ch);

    curl_close($ch);

    if ($response === false) {
        // Error en la solicitud cURL
        return false;
    }

    // Decodifica la respuesta JSON
    $responseData = json_decode($response, true);

    // Verifica si la decodificación fue exitosa y si 'success' es true
    if ($responseData !== null && isset($responseData['success']) && $responseData['success'] === true) {
        return true;
    } else {
        // Respuesta no válida o 'success' es false
        return false;
    }
}


// Función para obtener los datos del formulario 1
function obtenerDatosFormulario1() {
    return [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido1' => $_POST['apellido1'] ?? '',
        'apellido2' => $_POST['apellido2'] ?? '',
        'identificacion' => $_POST['identificacion'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'dni' => $_POST['dni'] ?? '',
        'nacionalidad' => $_POST['nacionalidad'] ?? '',
        'fecNac' => $_POST['fecNac'] ?? '',
        'domicilio' => $_POST['domicilio'] ?? '',
    ];
}

// Función para obtener los datos del formulario 2
function obtenerDatosFormulario2() {
    return [
        'email' => $_POST['email'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'codPostal' => $_POST['codPostal'] ?? '',
        'localidad' => $_POST['localidad'] ?? '',
        'provincia' => $_POST['provincia'] ?? '',
        'regionFiscal' => $_POST['regionFiscal'] ?? '',
        'pais' => $_POST['pais'] ?? '',
    ];
}

// Función para obtener los datos de los tres formularios
function obtenerDatosFormulario3() {
    $data = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido1' => $_POST['apellido1'] ?? '',
        'apellido2' => $_POST['apellido2'] ?? '',
        'identificacion' => $_POST['identificacion'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'dni' => $_POST['dni'] ?? '',
        'nacionalidad' => $_POST['nacionalidad'] ?? '',
        'fecNac' => $_POST['fecNac'] ?? '',
        'domicilio' => $_POST['domicilio'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'codPostal' => $_POST['codPostal'] ?? '',
        'localidad' => $_POST['localidad'] ?? '',
        'provincia' => $_POST['provincia'] ?? '',
        'regionFiscal' => $_POST['regionFiscal'] ?? '',
        'pais' => $_POST['pais'] ?? '',
        'usuario' => $_POST['usuario'] ?? '',
        'password' => $_POST['password'] ?? '',
        'preguntaSeguridad' => $_POST['preguntaSeguridad'] ?? '',
        'respuestaSeguridad' => $_POST['respuestaSeguridad'] ?? '',
        'mayor18' => isset($_POST['mayor18']) && $_POST['mayor18'] === 'mayor18',
        'recibirOfertas' => isset($_POST['recibirOfertas']) && $_POST['recibirOfertas'] === 'recibirOfertas',
    ];

    return $data;
}

// Función para validar los datos del formulario 1 mediante los validadores de validadoresDatos.php
function validarDatos1($data) {
    // Destructura los datos para poder acceder a sus variables
    extract($data);

    // Validación de campos
    $valid1 = validarNombreApellidos($nombre);
    $valid2 = validarNombreApellidos($apellido1);
    $valid3 = validarNombreApellidos($apellido2);
    $valid4 = $identificacion == "dni" || $identificacion == "nie";
    $valid5 = $sexo == "hombre" || $sexo == "mujer";
    $valid6 = (validarDNI($dni) && $identificacion == "dni") || (validarNIE($dni) && $identificacion == "nie");
    $valid7 = validarNacionalidad($nacionalidad);
    $valid8 = validarFechaNacimiento($fecNac);
    $valid9 = validarDomicilio($domicilio);
    $valid10 = false;

    /*
    // Valida que DNI o NIE con la API censurada
    if ($valid6){
        $response = apiValidateDNI($dni);
        if ($response["response"] == "notused") {
            $valid10 = true;
        } else if ($response["response"] == "used") {
            $valid10 = false;
        }
    }
    */

    return $valid1 && $valid2 && $valid3 && $valid4 && $valid5 && $valid6 && $valid7 && $valid8 && $valid9 /* && $valid10 */;
}

// Función para validar los datos del formulario 2 mediante los validadores de validadoresDatos.php
function validarDatos2($data) {
    // Destructura los datos para poder acceder a sus variables
    extract($data);

    // Validación de campos
    $valid1 = validarEmail($email);
    $valid2 = validarTelefono($telefono);
    $valid3 = preg_match("/^(?:0[1-9]\d{3}|[1-4]\d{4}|5[0-2]\d{3})$/", $codPostal);
    $valid4 = !empty($localidad);
    $valid5 = !empty($provincia);
    $valid6 = validarRegionFiscal($regionFiscal);
    $valid7 = ($pais == "199");
    $valid8 = validarCodigo($codPostal,$provincia); // Valida que el código postal pertenezca a la provincia
    $valid9 = validarRegionProvincia($provincia,$regionFiscal); // Valida que la provincia pertenenezca o sea igual a la región fiscal

    /*
    $valid10 = false;

    // Valida que código postal con la API censurada
    if ($valid3) {
        $response = apiValidateCodigoPostal($codPostal);
        
        foreach ($response as $item) {
            if ($item['zipCode'] == $codPostal) {
                $valid10 = true;
            }
        }
    }
    */

    return $valid1 && $valid2 && $valid3 && $valid4 && $valid5 && $valid6 && $valid7 && $valid8 && $valid9 /* && $valid10 */;
}

// Función para validar los datos del formulario 3 mediante los validadores de validadoresDatos.php
function validarDatos3($data) {
    // Destructura los datos para poder acceder a sus variables
    extract($data);

    // Validación de campos
    $valid1 = validarUsuario($usuario);
    $valid2 = validarPassword($password);
    $valid3 = $preguntaSeguridad == "madre" || $preguntaSeguridad == "padre" || $preguntaSeguridad == "mascota" || $preguntaSeguridad == "abuelo" ||
        $preguntaSeguridad == "cancion" || $preguntaSeguridad == "equipo";
    $valid4 = !empty($respuestaSeguridad);
    $valid5 = $mayor18 == true;
    $valid6 = $recibirOfertas == true || $recibirOfertas == false;

    /*
    // Valida el usuario en la API censurada
    if ($valid1) {
        $response = apiValidateALIAS($usuario);
        if ($response["response"] == "notused") {
            $valid7 = true;
        } else if ($response["response"] == "used") {
            $valid7 = false;
        }
    }
    */

    // Valida que el usuario no contenga datos personales
    if ($valid2) {
        if (noContieneDatosPersonales($password,$nombre,$apellido1,$apellido2,$usuario,$fecNac)) {
            $valid8 = true;
        } else {
            $valid8 = false;
        }
    } else {
        $valid8 = false;
    }

    return $valid1 && $valid2 && $valid3 && $valid4 && $valid5 && $valid6 /* && $valid7 */ && $valid8;
}


// Obtiene la fecha y la IP
$fechaHora = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];

// Variable para almacenar el id del formulario recibido
$formulario = $_POST['formulario'] ?? '';

// If que ejecuta la obtención, validación, log y envío de respuesta JSON para el formulario 1
if ($formulario === 'form1') {

    // Almacena los campos del formulario 1 en un array asociativo
    $form1Data = obtenerDatosFormulario1();

    // Si alguno de los campos está vacío devuelve un JSON con Success false y mensaje de error
    if (empty($form1Data['nombre']) || empty($form1Data['apellido1']) || empty($form1Data['apellido2']) || empty($form1Data['identificacion']) || empty($form1Data['sexo']) || empty($form1Data['dni']) || empty($form1Data['nacionalidad']) || empty($form1Data['fecNac']) || empty($form1Data['domicilio'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos correctamente en el formulario 1.']);
        exit();
    }

    // Variable que será true o false según el retorno de la función de validación del primer formulario
    $isValid1 = validarDatos1($form1Data);

    // Variable que almacenará un JSON con los datos del primer formulario
    $jsonData1 = json_encode($form1Data);

    // Si el retorno de la función de validación fue true generará el log y emitirá un JSON con Success true, si no generará el log y emitirá el JSON con false
    if ($isValid1) {
        $jsonSuccess = json_encode(['success' => true]);
        generaLog($jsonSuccess, $fechaHora, $ip, $jsonData1);
        echo $jsonSuccess;
    } else {
        $jsonSuccess = json_encode(['success' => false]);
        generaLog($jsonSuccess, $fechaHora, $ip, $jsonData1);
        echo $jsonSuccess;
    }
}

// If que ejecuta la obtención, validación, log y envío de respuesta JSON para el formulario 2
if ($formulario === 'form2') {

    // Almacena los campos del formulario 2 en un array asociativo
    $form2Data = obtenerDatosFormulario2();

    // Si alguno de los campos está vacío devuelve un JSON con Success false y mensaje de error
    if (empty($form2Data['email']) || empty($form2Data['telefono']) || empty($form2Data['codPostal']) || empty($form2Data['localidad']) || empty($form2Data['provincia']) || empty($form2Data['regionFiscal']) || empty($form2Data['pais'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos correctamente en el formulario 2.']);
        exit();
    }

    // Variable que será true o false según el retorno de la función de validación del segundo formulario
    $isValid2 = validarDatos2($form2Data);

    // Variable que almacenará un JSON con los datos del segundo formulario
    $jsonData2 = json_encode($form2Data);

    // Si el retorno de la función de validación fue true generará el log y emitirá un JSON con Success true, si no generará el log y emitirá el JSON con false
    if ($isValid2) {
        $jsonSuccess = json_encode(['success' => true]);
        generaLog($jsonSuccess, $fechaHora, $ip, $jsonData2);
        echo $jsonSuccess;
    } else {
        $jsonSuccess = json_encode(['success' => false]);
        generaLog($jsonSuccess, $fechaHora, $ip, $jsonData2);
        echo $jsonSuccess;
    }
}

// If que ejecuta la obtención, validación, log y envío de respuesta JSON para el formulario 2
if ($formulario === 'form3') {

    // Almacena los campos del formulario 2 en un array asociativo
    $form3Data = obtenerDatosFormulario3();

    // Si alguno de los campos está vacío devuelve un JSON con Success false y mensaje de error
    if (empty($form3Data['nombre']) || empty($form3Data['apellido1']) || empty($form3Data['apellido2']) || empty($form3Data['identificacion']) ||
            empty($form3Data['sexo']) || empty($form3Data['dni']) || empty($form3Data['nacionalidad']) || empty($form3Data['fecNac']) || empty($form3Data['domicilio'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos correctamente en el formulario 1.']);
        exit();
    }
    if (empty($form3Data['email']) || empty($form3Data['telefono']) || empty($form3Data['codPostal']) || empty($form3Data['localidad']) ||
            empty($form3Data['provincia']) || empty($form3Data['regionFiscal']) || empty($form3Data['pais'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos correctamente en el formulario 2.']);
        exit();
    }
    if (empty($form3Data['usuario']) || empty($form3Data['password']) || empty($form3Data['preguntaSeguridad']) || empty($form3Data['respuestaSeguridad'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos correctamente en el formulario 3.']);
        exit();
    }

    // Valida todos los datos con sus respectivas funciones de validación
    if (validarDatos1($form3Data) && validarDatos2($form3Data) && validarDatos3($form3Data)) {
        $isValid3 = true;
    } else {
        $isValid3 = false;
    }

    // Variable que almacenará un JSON con los datos del tercer formulario
    $jsonData3 = json_encode($form3Data);

    // Si todos los datos son válidos los pasa por parámetros a la función que los envía a enviarRegistro.php que los insertará en la base de datos
    if ($isValid3) {
        
        // Si el envío ha sido satisfactorio la función devuelve true con lo que se genera un log y se emite un Success true. En caso contrario igual pero con Success false
        if (enviarRegistro($jsonData3)) {
            $jsonSuccess = json_encode(['success' => true]);
            generaLog($jsonSuccess, $fechaHora, $ip, $jsonData3);
            echo $jsonSuccess;
        } else {
            $jsonSuccess = json_encode(['success' => false]);
            generaLog($jsonSuccess, $fechaHora, $ip, $jsonData3);
            echo $jsonSuccess;
        }

    } else {    // En caso de que no todos los datos sean válidos se genera un log y se emite un Success false
        $jsonSuccess = json_encode(['success' => false]);
        generaLog($jsonSuccess, $fechaHora, $ip, $jsonData3);
        echo $jsonSuccess;
    }
}
?>