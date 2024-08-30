<?php 
session_start();
include('db.php');

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Consultar pedidos de manera segura
$query = "SELECT idpedido, clientes_id, fecha, total, estado 
          FROM pedidos";
$result = $db->query($query);

if (!$result) {
    echo '<p>Error en la consulta: ' . htmlspecialchars($db->error) . '</p>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pedidos</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navigation Bar Styles */
        .navbar {
            background-color: #007bff;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-links a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .navbar-links a:hover {
            background-color: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Content Styles */
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

        /* Table Styles */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                width: 95%;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            table {
                border: 0;
            }

            th, td {
                padding: 10px;
                text-align: right;
                position: relative;
            }

            th {
                background: #f4f4f4;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }

            td:nth-of-type(1)::before { content: "ID Pedido"; }
            td:nth-of-type(2)::before { content: "ID Cliente"; }
            td:nth-of-type(3)::before { content: "Fecha"; }
            td:nth-of-type(4)::before { content: "Total"; }
            td:nth-of-type(5)::before { content: "Estado"; }
            td:nth-of-type(6)::before { content: "Detalles"; }
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
        <h1>Ver Pedidos</h1>

        <!-- Mostrar pedidos -->
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>ID Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['idpedido']); ?></td>
                            <td><?php echo htmlspecialchars($row['clientes_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                            <td>$<?php echo number_format((float)$row['total'], 2, '.', ','); ?></td>
                            <td>
                                <form method="post" action="actualizar_estado_pedido.php">
                                    <input type="hidden" name="idpedido" value="<?php echo htmlspecialchars($row['idpedido']); ?>">
                                    <select name="estado">
                                        <option value="Pendiente" <?php echo ($row['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="Enviado" <?php echo ($row['estado'] === 'Enviado') ? 'selected' : ''; ?>>Enviado</option>
                                        <option value="Completado" <?php echo ($row['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
                                    </select>
                                    <button type="submit">Actualizar</button>
                                </form>
                            </td>
                            <td>
                                <a href="ver_detalles_pedido.php?id=<?php echo htmlspecialchars($row['idpedido']); ?>">Ver Detalles</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay pedidos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
