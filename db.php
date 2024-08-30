<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'olimpiadasjaz';

try {
    // Establecer la conexión
    $db = new mysqli($host, $user, $password, $database);

    // Verificar si hay errores en la conexión
    if ($db->connect_error) {
        throw new Exception("Conexión fallida: " . $db->connect_error);
    }

    // Establecer el conjunto de caracteres para la conexión
    $db->set_charset("utf8mb4");

    // Aquí puedes continuar con las operaciones de la base de datos

} catch (Exception $e) {
    // Manejar el error de conexión
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit();
}
?>
