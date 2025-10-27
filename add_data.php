<?php
session_start();
require 'db.php';

// Proteger: Asegurarse de que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $fecha_registro = $_POST['fecha_registro'];
    $user_id = $_SESSION['user_id'];

    // Validaciones
    if (empty($peso) || empty($altura) || empty($fecha_registro) || $altura == 0 || $peso == 0) {
        header('Location: dashboard.php?error=Datos incompletos o inválidos');
        exit;
    }

    // Convertir a números flotantes (decimales)
    $peso = floatval($peso);
    $altura = floatval($altura);

    // Calcular el IMC (Índice de Masa Corporal)
    // Fórmula: peso / (altura * altura)
    $imc = $peso / ($altura * $altura);
    // Redondear a 2 decimales
    $imc_redondeado = round($imc, 2);

    try {
        // Preparar la consulta SQL
        $sql = "INSERT INTO metricas (user_id, peso, altura, imc, fecha_registro) 
                VALUES (:user_id, :peso, :altura, :imc, :fecha_registro)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'user_id' => $user_id,
            'peso' => $peso,
            'altura' => $altura,
            'imc' => $imc_redondeado,
            'fecha_registro' => $fecha_registro
        ]);

        // Redirigir de nuevo al dashboard (para que el gráfico se actualice)
        header('Location: dashboard.php');
        exit;

    } catch (PDOException $e) {
        // Manejar errores
        header('Location: dashboard.php?error=Error al guardar los datos');
        exit;
    }
}
?>