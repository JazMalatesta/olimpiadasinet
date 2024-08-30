<?php
session_start();
include('db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Procesar la eliminación de todos los pedidos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrar_pedidos'])) {
    // Eliminar todos los pedidos del usuario
    $query = "DELETE FROM pedidos WHERE clientes_id = ?";
    $stmt = $db->prepare($query);
    if ($stmt === false) {
        die('Error al preparar la declaración: ' . $db->error);
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->error) {
        die('Error al ejecutar la declaración: ' . $stmt->error);
    }
    $stmt->close();
    $message = 'Todos los pedidos han sido eliminados con éxito.';
}

// Consultar pedidos del usuario
$query = "SELECT idpedido, codigo, cantidad, fecha, estado FROM pedidos WHERE clientes_id = ?";
$stmt = $db->prepare($query);
if ($stmt === false) {
    die('Error al preparar la declaración: ' . $db->error);
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Estilo para el botón de borrar */
        .delete-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        .delete-button:hover {
            background-color: #e60000;
            transform: scale(1.05);
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Navbar */
        .navbar {
            background-color: #007bff;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #0056b3;
        }

        .navbar img {
            height: 40px;
        }

        /* Contenedor principal */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Botón de compras */
        .buy-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        .buy-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div>
            <a href="index.php">Ver Productos</a>
            <a href="mis_pedidos.php">Mis Pedidos</a>
            <a href="carrito.php">Compras</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <h1>Mis Pedidos</h1>
        
        <!-- Mostrar mensaje si se eliminan los pedidos -->
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Código</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['idpedido']); ?></td>
                            <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($row['estado']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No tienes pedidos pendientes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Formularios para borrar todos los pedidos y comprar -->
        <form action="mis_pedidos.php" method="post" style="display: inline;">
            <button type="submit" name="borrar_pedidos" class="delete-button">Borrar Todos los Pedidos</button>
        </form>
        <button class="buy-button" onclick="window.location.href='carrito.php';">Ir al Carrito</button>
    </div>
    
    <?php $stmt->close(); ?>
</body>
</html>

