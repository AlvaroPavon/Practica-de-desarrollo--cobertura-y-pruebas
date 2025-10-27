<?php
// Configuración de la base de datos
$host = 'localhost';      // Tu servidor de base de datos (usualmente localhost)
$dbname = 'proyecto_imc'; // El nombre de la base de datos que creaste
$username = 'root';       // Tu usuario de MySQL (root por defecto en XAMPP)
$password = '';           // Tu contraseña de MySQL (vacía por defecto en XAMPP)

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Crear la instancia de PDO (la conexión)
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Manejar errores de conexión
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>