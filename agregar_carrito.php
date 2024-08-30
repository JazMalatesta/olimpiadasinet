<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['codigo']) && isset($data['cantidad'])) {
        $codigo_producto = $data['codigo'];
        $cantidad = $data['cantidad'];
        $user_id = $_SESSION['user_id'];

        // Obtener detalles del producto
        $query = "SELECT descripcion, precio FROM productos WHERE codigo = ?";
        $stmt = $db->prepare($query);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
            exit();
        }
        $stmt->bind_param('s', $codigo_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $descripcion = $product['descripcion'];
            $precio = $product['precio'];

            // Verificar si el producto ya está en el carrito
            $query = "SELECT * FROM carrito WHERE user_id = ? AND codigo_producto = ?";
            $stmt = $db->prepare($query);
            if ($stmt === false) {
                echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
                exit();
            }
            $stmt->bind_param('is', $user_id, $codigo_producto);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Actualizar la cantidad si el producto ya está en el carrito
                $query = "UPDATE carrito SET cantidad = cantidad + ? WHERE user_id = ? AND codigo_producto = ?";
                $stmt = $db->prepare($query);
                if ($stmt === false) {
                    echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
                    exit();
                }
                $stmt->bind_param('iis', $cantidad, $user_id, $codigo_producto);
            } else {
                // Insertar el producto si no está en el carrito
                $query = "INSERT INTO carrito (user_id, codigo_producto, descripcion, precio, cantidad) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                if ($stmt === false) {
                    echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
                    exit();
                }
                $stmt->bind_param('issdi', $user_id, $codigo_producto, $descripcion, $precio, $cantidad);
            }

            $stmt->execute();
            if ($stmt->error) {
                echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    }
}
?>

