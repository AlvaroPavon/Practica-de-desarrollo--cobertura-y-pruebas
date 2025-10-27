<?php
session_start(); // Unirse a la sesión existente

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al formulario de login (index.php)
header('Location: index.php');
exit;
?>