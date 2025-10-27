<?php
session_start();
require 'db.php';

// Asegurarse de que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Consultar todas las métricas del usuario, ordenadas por fecha
    $sql = "SELECT fecha_registro, imc FROM metricas WHERE user_id = :user_id ORDER BY fecha_registro ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    
    $datos = $stmt->fetchAll();

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($datos);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>