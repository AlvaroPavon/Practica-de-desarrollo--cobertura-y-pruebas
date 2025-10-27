<?php
session_start(); // Iniciar la sesión
require 'db.php'; // Incluir la conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header('Location: index.php?login_error=Email y contraseña son requeridos');
        exit;
    }

    try {
        // Buscar al usuario por email
        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch();

        // Verificar si el usuario existe y si la contraseña es correcta
        if ($user && password_verify($password, $user['password'])) {
            // Contraseña correcta: Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            
            // Redirigir al panel de control (dashboard)
            header('Location: dashboard.php');
            exit;
        } else {
            // Credenciales incorrectas
            header('Location: index.php?login_error=Email o contraseña incorrectos');
            exit;
        }

    } catch (PDOException $e) {
        header('Location: index.php?login_error=Error de base de datos');
        exit;
    }

} else {
    header('Location: index.php');
    exit;
}
?>