<?php
// Conexión a la base de datos
$conn = new mysqli("localhost","root","estela123","josep");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Verificar si el correo ya está registrado
    $checkEmail = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $checkEmail->bind_param('s', $email);
    $checkEmail->execute();
    $resultado = $checkEmail->get_result();

    if ($resultado->num_rows > 0) {
        echo "El correo ya está registrado.";
    } else {
        // Insertar usuario en la tabla 'user'
        $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; // Obtener el ID del usuario insertado

            // Insertar información adicional en la tabla 'user_info'
            $stmt_info = $conn->prepare("INSERT INTO user_info (user_id, first_name, last_name, birthdate, gender, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_info->bind_param('issssss', $user_id, $first_name, $last_name, $birthdate, $gender, $phone, $address);

            if ($stmt_info->execute()) {
                echo "Registro exitoso. Ahora puede iniciar sesión.";
            } else {
                echo "Error al registrar la información adicional.";
            }
        } else {
            echo "Error al registrar el usuario.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
// Formulario de registro generado con PHP
echo '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        <form action="registro.php" method="POST">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="first_name">Nombre:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Apellido:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="birthdate">Fecha de Nacimiento:</label>
            <input type="date" id="birthdate" name="birthdate" required>

            <label for="gender">Género:</label>
            <select id="gender" name="gender" required>
                <option value="male">Masculino</option>
                <option value="female">Femenino</option>
                <option value="other">Otro</option>
            </select>

            <label for="phone">Teléfono:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address">

            <button type="submit">Registrar</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
</body>
</html>';
?>