<?php
session_start();

// Inicialización de variables para manejar mensajes de error
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Contraseña predeterminada para el administrador
    $admin_password = 'admin'; // Cambiar por una contraseña más segura en producción

    // Verificar las credenciales
    if ($password === $admin_password) {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['role'] = 'admin';
        header('Location: admin.php'); // Redirigir al panel de administración
        exit();
    } else {
        // Para clientes, cualquier contraseña es aceptable
        $_SESSION['user_id'] = uniqid(); // Generar un ID único para el cliente
        $_SESSION['role'] = 'cliente';
        header('Location: index.php'); // Redirigir a la página principal
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos generales para la página de inicio de sesión */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Contenedor principal del formulario */
.content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

/* Título del formulario */
h1 {
    margin-top: 0;
    font-size: 24px;
    color: #333;
    text-align: center;
}

/* Estilos para el formulario */
form {
    display: flex;
    flex-direction: column;
}

/* Etiquetas y campos de entrada */
label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

input[type="text"], input[type="password"] {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

/* Botón de envío */
button {
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

/* Mensajes de error */
.error {
    color: #d9534f;
    font-size: 14px;
    margin-bottom: 15px;
    text-align: center;
}

    </style>
</head>
<body>
    <div class="content">
        <h1>Iniciar Sesión</h1>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" required><br>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br>
            <button type="submit" class="buy-button">Entrar</button>
        </form>
    </div>
</body>
</html>


