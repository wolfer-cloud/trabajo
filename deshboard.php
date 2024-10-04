<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost","root","estela123","josep");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Obtener información del usuario
$stmt = $conn->prepare("SELECT u.username, ui.first_name, ui.last_name, ui.phone, ui.address 
                        FROM user u 
                        JOIN user_info ui ON u.id = ui.user_id 
                        WHERE u.id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>

<?php
// Página de bienvenida generada con PHP
echo '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
</head>
<body>
    <h1>Bienvenido, ' . $usuario['first_name'] . ' ' . $usuario['last_name'] . '</h1>
    <p>Usuario: ' . $usuario['username'] . '</p>
    <p>Teléfono: ' . $usuario['phone'] . '</p>
    <p>Dirección: ' . $usuario['address'] . '</p>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>';
?>