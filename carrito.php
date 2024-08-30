<?php
session_start();
include('db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Procesar la finalización de la compra
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se debe eliminar un producto
    if (isset($_POST['eliminar_producto'])) {
        $producto_id = intval($_POST['producto_id']);
        
        // Eliminar el producto del carrito
        $query = "DELETE FROM carrito WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt === false) {
            die('Error al preparar la declaración: ' . $db->error);
        }
        $stmt->bind_param('ii', $producto_id, $user_id);
        $stmt->execute();
        if ($stmt->error) {
            die('Error al ejecutar la declaración: ' . $stmt->error);
        }
        $stmt->close();
        
        // Mensaje de éxito
        $message = 'Producto eliminado del carrito.';
    }

    // Procesar la finalización de la compra
    if (isset($_POST['finalizar_compra'])) {
        // Obtener los productos del carrito
        $query = "SELECT * FROM carrito WHERE user_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt === false) {
            die('Error al preparar la declaración: ' . $db->error);
        }
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result();

        // Insertar cada producto en la tabla de pedidos
        while ($item = $cart_items->fetch_assoc()) {
            // Insertar el pedido
            $query = "INSERT INTO pedidos (codigo, cantidad, clientes_id, fecha, estado) VALUES (?, ?, ?, NOW(), 'Pendiente')";
            $stmt = $db->prepare($query);
            if ($stmt === false) {
                die('Error al preparar la declaración: ' . $db->error);
            }
            $stmt->bind_param('sis', $item['codigo_producto'], $item['cantidad'], $user_id);
            $stmt->execute();
            if ($stmt->error) {
                die('Error al ejecutar la declaración: ' . $stmt->error);
            }
        }

        // Vaciar el carrito
        $query = "DELETE FROM carrito WHERE user_id = ?";
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
        $message = 'Compra finalizada con éxito.';
    }
}

// Consultar productos en el carrito
$query = "SELECT * FROM carrito WHERE user_id = ?";
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
    <title>Mi Carrito de Compras</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos para el carrito */
        .cart-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th, .cart-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .cart-table th {
            background-color: #f4f4f4;
            color: #333;
        }

        .cart-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .cart-table tr:hover {
            background-color: #f1f1f1;
        }

        .cart-actions {
            text-align: right;
        }

        .cart-actions button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        .cart-actions button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        .success-message {
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div>
            <a href="mis_pedidos.php">Mis Pedidos</a>
            <a href="index.php">Ver Productos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="cart-container">
        <h1>Mi Carrito de Compras</h1>

        <!-- Mostrar mensaje si se finaliza la compra o se elimina un producto -->
        <?php if (isset($message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                while ($row = $result->fetch_assoc()): 
                    $subtotal = $row['precio'] * $row['cantidad'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['precio']); ?></td>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td>$<?php echo htmlspecialchars($subtotal); ?></td>
                        <td>
                            <!-- Formulario para eliminar un producto del carrito -->
                            <form action="carrito.php" method="post" style="display:inline;">
                                <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" name="eliminar_producto" class="delete-button">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3"><strong>Total:</strong></td>
                    <td><strong>$<?php echo htmlspecialchars($total); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="cart-actions">
            <form action="carrito.php" method="post">
                <button type="submit" name="finalizar_compra">Finalizar Compra</button>
                <button type="button" onclick="window.location.href='index.php';">Cancelar Compra</button>
                <button type="button" onclick="window.location.href='mis_pedidos.php';">Mis Pedidos</button>
            </form>
        </div>
    </div>
</body>
</html>
