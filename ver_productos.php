<?php 
session_start();
include('db.php');

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Consultar stock de productos
$query = "SELECT * FROM productos";
$result = $db->query($query);

if (!$result) {
    die('Error en la consulta: ' . $db->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Productos</title>
    <style>
        /* Estilos Generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Barra de Navegación */
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

        .navbar a:last-child {
            color: white;
        }

        /* Contenedor de Contenido */
        .content {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Títulos */
        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Tabla de Productos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
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

        /* Imagen del Producto */
        .product-image {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Mensajes */
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
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
        <h1>Ver Productos</h1>

        <!-- Mostrar stock de productos -->
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen del producto" class="product-image">
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['precio']); ?></td>
                            <td><?php echo htmlspecialchars($row['stock']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay productos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
