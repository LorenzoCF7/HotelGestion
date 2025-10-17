<?php
include_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
$huéspedEditando = null; // Para cargar datos si se está editando

// === EDITAR: Cargar datos si se pasa ?editar=id ===
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $stmt = $conn->prepare("SELECT id_huesped, nombre, email, documento_identidad FROM Huespedes WHERE id_huesped = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $huéspedEditando = $resultado->fetch_assoc();
    $stmt->close();
}

// === ACTUALIZAR: si se envía el formulario de edición ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_huesped'])) {
    $id = (int)$_POST['id_huesped'];
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $documento = $_POST['documento'] ?? '';

    if ($nombre && $email && $documento) {
        $stmt = $conn->prepare("UPDATE Huespedes SET nombre = ?, email = ?, documento_identidad = ? WHERE id_huesped = ?");
        $stmt->bind_param("sssi", $nombre, $email, $documento, $id);
        if ($stmt->execute()) {
            $mensaje = "Huésped actualizado con éxito.";
            // Limpiar modo edición
            $huéspedEditando = null;
        } else {
            if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'email') !== false) {
                $mensaje = "El email ya está en uso por otro huésped.";
            } else {
                $mensaje = "Error al actualizar: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// === ELIMINAR ===
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_huesped = (int)$_GET['eliminar'];
    $check = $conn->prepare("SELECT 1 FROM Reservas WHERE id_huesped = ? LIMIT 1");
    $check->bind_param("i", $id_huesped);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $mensaje = "No se puede eliminar: el huésped tiene reservas activas.";
    } else {
        $stmt = $conn->prepare("DELETE FROM Huespedes WHERE id_huesped = ?");
        $stmt->bind_param("i", $id_huesped);
        if ($stmt->execute()) {
            $mensaje = "Huésped eliminado con éxito.";
        } else {
            $mensaje = "Error al eliminar.";
        }
        $stmt->close();
    }
    $check->close();
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === REGISTRAR (solo si NO es edición) ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['id_huesped'])) {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $documento = $_POST['documento'] ?? '';

    if ($nombre && $email && $documento) {
        $stmt = $conn->prepare("INSERT INTO Huespedes (nombre, email, documento_identidad) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $documento);
        if ($stmt->execute()) {
            $mensaje = "Huésped registrado con éxito.";
        } else {
            if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'email') !== false) {
                $mensaje = "El email ya está registrado.";
            } else {
                $mensaje = "Error al registrar: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// === LISTAR SIEMPRE ===
$sql_lista = "SELECT id_huesped, nombre, email, documento_identidad FROM Huespedes ORDER BY id_huesped DESC";
$listaHuespedes = $conn->query($sql_lista);

if (!$listaHuespedes) {
    $mensaje = "Error al cargar la lista: " . $conn->error;
}
?>