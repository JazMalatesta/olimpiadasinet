<?php 
session_start();
include('db.php');

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener el ID del pedido desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de pedido inválido.');
}

$id_pedido = (int)$_GET['id'];

// Consultar la información del pedido
$query_pedido = "SELECT idpedido, fecha, clientes_id, estado, descripcion, total 
                 FROM pedidos 
                 WHERE idpedido = ?";
$stmt_pedido = $db->prepare($query_pedido);
if (!$stmt_pedido) {
    die('Error en la preparación de la consulta del pedido: ' . $db->error);
}
$stmt_pedido->bind_param('i', $id_pedido);
$stmt_pedido->execute();
$result_pedido = $stmt_pedido->get_result();

if (!$result_pedido) {
    die('Error en la consulta del pedido: ' . $db->error);
}

$pedido = $result_pedido->fetch_assoc();
$pedido_found = $pedido !== null;

// Consultar los detalles del pedido
$query_detalles = "SELECT dp.id_producto, p.descripcion AS producto_descripcion, dp.precio, dp.cantidad, dp.total
                   FROM detalle_pedido dp
                   JOIN productos p ON dp.id_producto = p.idproducto
                   WHERE dp.id_pedido = ?";
$stmt_detalles = $db->prepare($query_detalles);
if (!$stmt_detalles) {
    die('Error en la preparación de la consulta de detalles: ' . $db->error);
}
$stmt_detalles->bind_param('i', $id_pedido);
$stmt_detalles->execute();
$result_detalles = $stmt_detalles->get_result();

if (!$result_detalles) {
    die('Error en la consulta de detalles: ' . $db->error);
}

$details_found = $result_detalles->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Barra de navegación */
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

        /* Contenido principal */
        .content {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-links">
            <a href="admin.php">Agregar Productos</a>
            <a href="ver_productos.php">Ver Productos</a>
            <a href="ver_pedidos.php">Ver Pedidos</a>
        </div>
        <a href="login.php">Cerrar Sesión</a>
    </div>
    
    <div class="content">
        <h1>Detalles del Pedido</h1>

        <!-- Mostrar información del pedido -->
        <?php if ($pedido_found): ?>
            <p><strong>ID del Pedido:</strong> <?php echo htmlspecialchars($pedido['idpedido']); ?></p>
            <p><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars($pedido['fecha']); ?></p>
            <p><strong>ID del Cliente:</strong> <?php echo htmlspecialchars($pedido['clientes_id']); ?></p>
            <p><strong>Estado del Pedido:</strong> <?php echo htmlspecialchars($pedido['estado']); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($pedido['descripcion']); ?></p>
            <p><strong>Total:</strong> $<?php echo number_format((float)$pedido['total'], 2, '.', ','); ?></p>

            <!-- Mostrar detalles del pedido -->
            <h2>Productos en el Pedido</h2>
            <?php if ($details_found): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Producto</th>
                            <th>Descripción del Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_detalles->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_producto']); ?></td>
                                <td><?php echo htmlspecialchars($row['producto_descripcion']); ?></td>
                                <td>$<?php echo number_format((float)$row['precio'], 2, '.', ','); ?></td>
                                <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                                <td>$<?php echo number_format((float)$row['total'], 2, '.', ','); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron productos para el pedido con ID <?php echo htmlspecialchars($id_pedido); ?>.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No se encontró el pedido con ID <?php echo htmlspecialchars($id_pedido); ?>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
