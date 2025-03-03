<?php
/**
 * Funciones de validación de datos.
 */

// Función para devolver true si un nombre o apellido contiene algun caracter no pérmitido.
function validarNombreApellidos($campo) {
    $caracteres = "1234567890$&¿?¡!|()@#¬+*{}<>%\/";

    // Si el String contiene caracteres no permitidos devuelve false
    return strpbrk($campo, $caracteres) === false;
}

// Función para devolver true si un DNI tiene un formato válido.
function validarDNI($dni) {
    $dni = strtoupper($dni);

    if (!preg_match("/^\d{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/", $dni)) {
        return false;
    }

    $numeros = substr($dni, 0, 8);
    $resto = (int)$numeros % 23;
    $letra = substr($dni, 8, 1);
    $tabla = "TRWAGMYFPDXBNJZSQVHLCKE";

    return ($letra == $tabla[$resto]);
}

// Función para devolver true si un NIE tiene un formato válido.
function validarNIE($nie) {
    $nie = strtoupper($nie);

    if (!preg_match("/^[XYZ]\d{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/", $nie)) {
        return false;
    }

    $nie = preg_replace_callback("/^[XYZ]/", function($matches) {
        return ($matches[0] == 'X') ? '0' : (($matches[0] == 'Y') ? '1' : '2');
    }, $nie);
    
    $numeros = substr($nie, 0, 8);
    $resto = $numeros % 23;
    $letra = substr($nie, 8, 1);
    $tabla = "TRWAGMYFPDXBNJZSQVHLCKE";

    return ($letra == $tabla[$resto]);
}

// Función para devolver true si una nacionalidad está en el JSON que devuelve paisesBD.php, que es extraído de la base de dato.
function validarNacionalidad($nacionalidad) {
    $jsonPaises = file_get_contents('http://' . "localhost" . '/Landing/php/databases/paisesBD.php');
    $paises = json_decode($jsonPaises, true);
    
    foreach ($paises['paises'] as $pais) {
        if (strcasecmp($pais['id'], $nacionalidad) === 0) {
            return true;
        }
    }
    return false;
}

// Función para devolver true si una fecha de nacimiento está entre el mínimo (hace 80 años desde la fecha actual) y el máximo (hace 18 años).
function validarFechaNacimiento($fecNac) {
    
    if (empty($fecNac)) {
        return ['valid' => false];
    }

    $hoy = new DateTime();

    $fechaMinima = (clone $hoy)->modify('-80 years');
    $fechaMaxima = (clone $hoy)->modify('-18 years');

    $fechaMinima = $fechaMinima->format('Y-m-d');
    $fechaMaxima = $fechaMaxima->format('Y-m-d');

    try {
        $fechaNacimiento = new DateTime($fecNac);
    } catch (Exception $e) {
        return ['valid' => false];
    }

    if ($fechaNacimiento > new DateTime($fechaMaxima) || $fechaNacimiento < new DateTime($fechaMinima)) {
        return ['valid' => false];
    } else {
        return ['valid' => true];
    }
}

// Función para devolver true cuando un domicilio no contiene ningún caracter no permitido.
function validarDomicilio($domicilio) {
    $caracteres = ",-º";

    if (strpbrk($domicilio, $caracteres)) {
        return false;
    } else {
        return true;
    }
}

// Función para devolver true si un email tiene un formato válido.
function validarEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }else{
        return false;
    }
}

// Función para devolver true si un número de teléfono tiene un formato válido.
function validarTelefono($telefono) {
    $patron = '/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/';

    if (preg_match($patron, $telefono)) {
        return true;
    } else {
        return false;
    }
}

// Función para devolver true si una región fiscal está en el JSON que devuelve regionesFiscalesBD.php, que es extraído de la base de dato.
function validarRegionFiscal($regionFiscal) {
    // Obtiene el contenido JSON desde el archivo PHP
    $jsonRegiones = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/Landing/php/databases/regionesFiscalesBD.php');
    
    // Decodifica el JSON en un array asociativo
    $regiones = json_decode($jsonRegiones, true);
    
    // Recorre el array de regiones
    foreach ($regiones['regiones'] as $region) {
        // Compara la región fiscal con el id de cada región
        if (strcasecmp($region['id'], $regionFiscal) === 0) {
            return true;
        }
    }

    return false;
}

// Función para devolver true si los dos primeros dígitos del código postal coinciden con los de la provincia proporcionada
function validarCodigo($codigo, $provincia) {
    // Carga el contenido del archivo JSON
    $json = file_get_contents('../json/codigosPostales.json');

    // Decodifica el JSON a un array asociativo
    $data = json_decode($json, true);

    // Recorre el array buscando la provincia proporcionada
    foreach ($data['codigo'] as $item) {
        // Comprueba si el nombre de la provincia coincide (ignorando mayúsculas y minúsculas)
        if (strtolower($item['nombre']) == strtolower($provincia)) {
            // Compara los dos primeros dígitos del código postal proporcionado con los de la provincia
            if (substr($codigo, 0, 2) == $item['cp']) {
                return true;
            }
            return false;  // Los códigos no coinciden
        }
    }
    return false;  // La provincia no se encuentra en el archivo JSON
}

// Función para devolver true si la provincia se encuentra en su región fiscal
function validarRegionProvincia($provincia, $regionID) {
    // Carga el contenido del archivo de regiones fiscales con ID
    $regionesJson = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/Landing/php/databases/regionesFiscalesBD.php');
    $regionesData = json_decode($regionesJson, true);

    // Verifica si la decodificación fue exitosa
    if ($regionesData === null) {
        return false;  // Si no se puede leer el archivo JSON, retorna false
    }

    // Convierte el array de regiones fiscales en un array de 'id' => 'nombre'
    $regionesPorID = [];
    foreach ($regionesData['regiones'] as $region) {
        $regionesPorID[$region['id']] = $region['nombre'];
    }

    // Carga el contenido del archivo JSON de provincias
    $provinciasJson = file_get_contents('../json/regionesFiscales.json');
    $provinciasData = json_decode($provinciasJson, true);

    // Verifica si la decodificación fue exitosa
    if ($provinciasData === null) {
        return false;  // Si no se puede leer el archivo JSON, retorna false
    }

    // Busca la región fiscal por el ID proporcionado
    $regionNombre = '';
    foreach ($regionesData['regiones'] as $region) {
        if ($region['id'] == $regionID) {
            $regionNombre = $region['nombre'];
            break;  // Sale del ciclo cuando encuentra la región
        }
    }

    // Si no se encuentra la región con el ID proporcionado, retorna false
    if ($regionNombre === '') {
        return false;
    }

    // Recorre todas las regiones fiscales en el archivo de provincias
    foreach ($provinciasData['regionFiscal'] as $regionFiscal => $info) {
        // Comprueba si la región fiscal corresponde al nombre de la región fiscal
        if (strtolower($regionFiscal) == strtolower($regionNombre)) {
            // Si la provincia está en la lista de provincias de esta región fiscal, devuelve true
            if (in_array($provincia, $info['provincias'])) {
                return true;
            }
        }

        // Comprueba si la provincia coincide exactamente con el nombre de la región fiscal
        if (strtolower($regionFiscal) == strtolower($provincia)) {
            return true;
        }
    }

    // Si no se encuentra coincidencia en ninguna de las regiones fiscales devuelve false
    return false;
}

// Función para devolver true si un nombre de usuario cumple con las restricciones
function validarUsuario($username) {
    $palabrasSoeces = json_decode(file_get_contents('../json/palabrasSoeces.json'), true)['palabra'];

    if (!preg_match('/[a-zA-Z]/', $username) || preg_match('/^\d+$/', $username)) {
        return false;
    }

    if (strlen($username) > 30) {
        return false;
    }

    $usernameLower = strtolower($username);

    foreach ($palabrasSoeces as $palabra) {
        if (strpos($usernameLower, strtolower($palabra)) !== false) {
            return false;
        }
    }
    
    return true;
}

// Función para devolver true si una contraseña cumple con las restricciones por ley
function validarPassword($password) {
    
    $contieneLetra = preg_match('/[a-zA-Z]/', $password);
    $contieneNumero = preg_match('/[0-9]/', $password);
    $contieneCaracterEspecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

    return $contieneLetra && $contieneNumero && $contieneCaracterEspecial;
}

// Función para devolver true si una contraseña no contiene datos personales
function noContieneDatosPersonales($password, $nombre, $apellido1, $apellido2, $usuario, $fecNac) {
    // Convertir la contraseña y los parámetros a minúsculas para hacer la comparación insensible a mayúsculas
    $passwordLower = strtolower($password);
    $nombreLower = strtolower($nombre);
    $apellido1Lower = strtolower($apellido1);
    $apellido2Lower = strtolower($apellido2);
    $usuarioLower = strtolower($usuario);
    
    // Extraer solo el año (YYYY) de la fecha de nacimiento
    $fecNacLower = strtolower($fecNac);
    $anioNacimiento = substr($fecNacLower, 0, 4);

    // Verifica que la contraseña no contenga ninguna de las palabras o números
    if (strpos($passwordLower, $nombreLower) !== false || 
        strpos($passwordLower, $apellido1Lower) !== false || 
        strpos($passwordLower, $apellido2Lower) !== false || 
        strpos($passwordLower, $usuarioLower) !== false ||
        strpos($passwordLower, $anioNacimiento) !== false) {
        return false;
    }

    // Comprueba si alguno de los parámetros es un número
    if (is_numeric($nombre) || is_numeric($apellido1) || is_numeric($apellido2) || is_numeric($usuario) || is_numeric($fecNac)) {
        // Comprueba si la contraseña contiene los números
        if (strpos($password, $nombre) !== false || 
            strpos($password, $apellido1) !== false || 
            strpos($password, $apellido2) !== false || 
            strpos($password, $usuario) !== false || 
            strpos($password, $anioNacimiento) !== false) {
            return false;
        }
    }

    // Si no se encuentra ninguna coincidencia
    return true;
}

?>