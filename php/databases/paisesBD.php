<?php
include_once("../ENV.php");

// Configuración de la base de datos
$servername = HOST;  // Servidor
$username = "proyecto";  // Usuario
$password = "admin";      // Contraseña
$dbname = "registro"; // Nombre de la base de datos
$port = 3307;   // Puerto mediante el que se conecta

// Crea la conexión
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Verifica conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta los países
$sql = "SELECT id_ref, nombre FROM nacionalidades";
$result = $conn->query($sql);

// Crea un array para almacenar los países
$paises = array();

if ($result->num_rows > 0) {
    // Agrega cada país al array
    while($row = $result->fetch_assoc()) {
        $paises[] = array('id' => $row['id_ref'], 'nombre' => $row['nombre']);
    }
} else {
    echo "0 resultados";
}

// Cierra la conexión
$conn->close();

// Envuelve el json en un dato padre 'paises'
$response = array('paises' => $paises);

// Devuelve los países en formato JSON
echo json_encode($response);
?>
