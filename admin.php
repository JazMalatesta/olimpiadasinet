<?php 
session_start();
include('db.php');

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Procesar la adición de nuevos productos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];

    // Manejar la carga de la imagen
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check === false) {
            $message = "El archivo no es una imagen.";
            $uploadOk = 0;
        }

        // Verificar el tamaño del archivo
        if ($_FILES["imagen"]["size"] > 500000) {
            $message = "El archivo es demasiado grande.";
            $uploadOk = 0;
        }

        // Permitir ciertos formatos de archivo
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $message = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
            $uploadOk = 0;
        }

        // Verificar si $uploadOk es 0 por un error
        if ($uploadOk == 0) {
            $message = "El archivo no se ha subido.";
        } else {
            // Intentar subir el archivo
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                $imagen = basename($_FILES["imagen"]["name"]);
            } else {
                $message = "Hubo un error al subir el archivo.";
            }
        }
    }

    // Preparar la declaración SQL para insertar productos
    $stmt = $db->prepare("INSERT INTO productos (codigo, descripcion, precio, imagen, stock) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die('Error al preparar la declaración: ' . $db->error);
    }

    // Vincular los parámetros y ejecutar
    $stmt->bind_param('ssdss', $codigo, $descripcion, $precio, $imagen, $stock);
    $result = $stmt->execute();
    if (!$result) {
        die('Error al ejecutar la declaración: ' . $stmt->error);
    }

    $message = 'Producto agregado con éxito.';
}

// Procesar la eliminación de todos los productos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_all_products'])) {
    $query = "DELETE FROM productos";
    $stmt = $db->prepare($query);
    if ($stmt === false) {
        die('Error al preparar la declaración: ' . $db->error);
    }
    $result = $stmt->execute();
    if (!$result) {
        die('Error al ejecutar la declaración: ' . $stmt->error);
    }
    $stmt->close();
    $message = 'Todos los productos han sido eliminados.';
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
    <title>Panel de Administración</title>
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

        h1, h2 {
            color: #333;
        }

        /* Container for content */
        .content {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Form Styles */
        form {
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
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

        /* Alert and Success Messages */
        p {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
        }

        /* Button Styles */
        a.buy-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        a.buy-button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        .product-image {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
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
        <h1>Panel de Administración</h1>
        <p>Bienvenido, administrador. Aquí puedes gestionar los productos y pedidos.</p>

        <!-- Mostrar mensaje si se agrega un producto o se elimina un pedido -->
        <?php if (isset($message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Formulario para agregar nuevos productos -->
        <h2>Agregar Nuevo Producto</h2>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" required>
            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" required>
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" required>
            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>
            <button type="submit" name="add_product">Agregar Producto</button>
        </form>

        <!-- Formulario para borrar todos los productos -->
        <form action="admin.php" method="post">
            <button type="submit" name="delete_all_products" class="delete-button">Borrar Todos los Productos</button>
        </form>

        <!-- Mostrar stock de productos -->
        <h2>Stock de Productos</h2>
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
            </tbody>
        </table>
    </div>
</body>
</html>
