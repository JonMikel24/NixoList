<?php
session_start(); // Iniciamos la sesión para poder destruirla

// Vaciamos todas las variables de sesión
$_SESSION = array();

// Destruimos la sesión completamente
session_destroy();

// Redirigimos al usuario a la página de inicio o al login
// Ajusta esta ruta a donde quieras que vaya al salir
header("Location: Index.php"); 
exit();
?>