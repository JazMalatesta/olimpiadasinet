<?php
session_start();
include('db.php');

// Consultar lista de productos
$query = "SELECT * FROM productos";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="style.css"> <!-- Asegúrate de que el nombre del archivo sea correcto -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <style>
        /* Styles for navigation bar */
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

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 200px;
            text-align: center;
            background-color: #fff;
        }
        
        .product-image {
            width: 100%;
            height: auto;
        }
        
        .product-info {
            padding: 10px;
        }
        
        .product-title {
            font-size: 1.2em;
            margin: 10px 0;
        }
        
        .product-price {
            font-size: 1.1em;
            color: #333;
        }
        
        .product-card button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
        }
        
        .product-card button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
 
        <div>
            <a href="mis_pedidos.php">Mis compras</a>
            <a href="carrito.php">Comprar</a>
            <a href="index.php">Ver Productos</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="login.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <h1>Lista de Productos</h1>
        <div class="container">
        <?php while ($row = $result->fetch_assoc()): 
            $imagen = htmlspecialchars($row['imagen']); // Recuperar la URL o nombre de la imagen desde la base de datos
            $imagenPath = "uploads/" . $imagen; // Ajusta la ruta según tu estructura de carpetas
        ?>
            <div class="product-card">
                <img src="<?php echo $imagenPath; ?>" alt="<?php echo htmlspecialchars($row['descripcion']); ?>" class="product-image">
                <div class="product-info">
                    <h2 class="product-title"><?php echo htmlspecialchars($row['descripcion']); ?></h2>
                    <p class="product-price">Precio: $<?php echo htmlspecialchars($row['precio']); ?></p>
                    <button onclick="agregarAlCarrito('<?php echo htmlspecialchars($row['codigo']); ?>')">Agregar al carrito</button>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>

    <script>
        function agregarAlCarrito(codigo) {
            fetch('agregar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codigo: codigo,
                    cantidad: 1 // Puedes permitir que el usuario seleccione la cantidad
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    // Actualizar el carrito o redirigir según sea necesario
                    window.location.reload(); // Opcional, actualiza la página para reflejar cambios
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al agregar el producto al carrito.');
            });
        }
    </script>
</body>
</html>
