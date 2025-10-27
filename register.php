<?php
require 'db.php'; // Incluir la conexión a la base de datos

// Verificar que se recibieron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar y limpiar datos
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
        header('Location: index.php?reg_error=Todos los campos son obligatorios');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?reg_error=Email no válido');
        exit;
    }

    // Hashear la contraseña (¡Importante para la seguridad!)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Preparar la consulta SQL para insertar el usuario
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, password) VALUES (:nombre, :apellidos, :email, :password)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'password' => $hashed_password
        ]);

        // Redirigir al index con mensaje de éxito
        header('Location: index.php?success=Registro completado. Por favor, inicia sesión.');
        exit;

    } catch (PDOException $e) {
        // Manejar error si el email ya existe (basado en la restricción UNIQUE)
        if ($e->getCode() == 23000) {
            header('Location: index.php?reg_error=El email ya está registrado');
        } else {
            header('Location: index.php?reg_error=Error en el registro');
        }
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>