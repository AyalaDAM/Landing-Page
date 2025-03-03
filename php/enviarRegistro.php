<?php
include_once("ENV.php");

// Configuración de la base de datos
$servername = HOST;  // Servidor
$username = "proyecto";  // Usuario
$password = "admin";      // Contraseña
$dbname = "registro"; // Nombre de la base de datos
$port = 3307;   // Puerto mediante el que se conecta

// Crea conexión
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Verifica conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibe los datos en formato JSON desde el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Verifica si los datos se recibieron correctamente
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

// Asigna los valores del JSON a las variables correspondientes
$nombre = $data['nombre'] ?? '';
$apellido1 = $data['apellido1'] ?? '';
$apellido2 = $data['apellido2'] ?? '';
$identificacion = $data['identificacion'] ?? '';
$sexo = $data['sexo'] ?? '';
$dni = $data['dni'] ?? '';
$nacionalidad = $data['nacionalidad'] ?? '';
$fecNac = $data['fecNac'] ?? '';
$domicilio = $data['domicilio'] ?? '';
$email = $data['email'] ?? '';
$telefono = $data['telefono'] ?? '';
$codPostal = $data['codPostal'] ?? '';
$localidad = $data['localidad'] ?? '';
$provincia = $data['provincia'] ?? '';
$regionFiscal = $data['regionFiscal'] ?? '';
$pais = (int) ($data['pais'] ?? 0);  // Convierte a entero para que coincida con la base de datos
$usuario = $data['usuario'] ?? '';
$password = $data['password'] ?? '';
$preguntaSeguridad = $data['preguntaSeguridad'] ?? '';
$respuestaSeguridad = $data['respuestaSeguridad'] ?? '';
$mayor18 = (int) ($data['mayor18'] ?? 0);  // Convierte a entero (0 o 1)
$recibirOfertas = (int) ($data['recibirOfertas'] ?? 0);  // Convierte a entero (0 o 1)

// Prepara la consulta SQL para insertar los datos en la base de datos
$sql = "INSERT INTO usuarios (
    nombre, apellido1, apellido2, identificacion, sexo, dni, nacionalidad, fec_nac, domicilio,
    email, telefono, cod_postal, localidad, provincia, region_fiscal, pais, usuario, password, 
    pregunta_seguridad, respuesta_seguridad, mayor18, recibir_ofertas
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

// Prepara la declaración
$stmt = $conn->prepare($sql);

// Vincular los parámetros
$stmt->bind_param('ssssssssssssssssssssii',  // Asegura que haya 22 parámetros
$nombre, $apellido1, $apellido2, $identificacion, $sexo, $dni, $nacionalidad, $fecNac, $domicilio,
$email, $telefono, $codPostal, $localidad, $provincia, $regionFiscal, $pais, $usuario, $password,
$preguntaSeguridad, $respuestaSeguridad, $mayor18, $recibirOfertas
);

// Ejecuta la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Datos guardados correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar los datos: ' . $stmt->error]);
}

// Cierra la declaración y la conexión
$stmt->close();
$conn->close();
?>
