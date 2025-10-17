<?php
include_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
$habitacionEditando = null;

// === CANCELAR EDICIÓN ===
if (isset($_GET['cancelar'])) {
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === CARGAR HABITACIÓN PARA EDITAR ESTADO DE LIMPIEZA ===
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $stmt = $conn->prepare("SELECT id_habitacion, numero, tipo, estado_limpieza FROM Habitaciones WHERE id_habitacion = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $habitacionEditando = $resultado->fetch_assoc();
    $stmt->close();
}

// === ACTUALIZAR ESTADO DE LIMPIEZA ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_habitacion'])) {
    $id_habitacion = (int)$_POST['id_habitacion'];
    $estado_limpieza = $_POST['estado_limpieza'] ?? '';

    $estados_validos = ['Limpia', 'Sucia', 'En Limpieza'];
    if (in_array($estado_limpieza, $estados_validos)) {
        $stmt = $conn->prepare("UPDATE Habitaciones SET estado_limpieza = ? WHERE id_habitacion = ?");
        $stmt->bind_param("si", $estado_limpieza, $id_habitacion);
        if ($stmt->execute()) {
            $mensaje = "Estado de limpieza actualizado con éxito.";
            $habitacionEditando = null;
        } else {
            $mensaje = "Error al actualizar el estado.";
        }
        $stmt->close();
    } else {
        $mensaje = "Estado de limpieza no válido.";
    }
}

// === LISTAR HABITACIONES CON ESTADO DE LIMPIEZA ===
$sql = "SELECT id_habitacion, numero, tipo, estado_limpieza FROM Habitaciones ORDER BY numero";
$listaHabitaciones = $conn->query($sql);

if (!$listaHabitaciones) {
    $mensaje = "Error al cargar las habitaciones: " . $conn->error;
}
?>