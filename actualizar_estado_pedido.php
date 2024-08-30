<?php
session_start();
include('db.php');

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Verificar si se han enviado los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idpedido'], $_POST['estado'])) {
        $idpedido = (int)$_POST['idpedido'];
        $estado = $db->real_escape_string($_POST['estado']);

        // Actualizar estado del pedido
        $query = "UPDATE pedidos SET estado = ? WHERE idpedido = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('si', $estado, $idpedido);

        if ($stmt->execute()) {
            header('Location: ver_pedidos.php');
        } else {
            die('Error al actualizar el estado: ' . $db->error);
        }
    } else {
        die('Datos del formulario no válidos.');
    }
} else {
    die('Método de solicitud no válido.');
}
?>
