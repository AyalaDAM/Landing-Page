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

// Consulta las regiones
$sql = "SELECT id_ref, nombre FROM region_fiscal";
$result = $conn->query($sql);

// Crea un array para almacenar las regiones
$regiones = array();

if ($result->num_rows > 0) {
    // Agrega cada región al array
    while($row = $result->fetch_assoc()) {
        $regiones[] = array('id' => $row['id_ref'], 'nombre' => $row['nombre']);
    }
} else {
    echo "0 resultados";
}

// Cierra la conexión
$conn->close();

// Envuelve el json en un dato padre 'regiones'
$response = array('regiones' => $regiones);

// Devuelve las regiones en formato JSON
echo json_encode($response);
?>
