<?php
include_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
$habitacionEditando = null;

// === CANCELAR EDICIÓN ===
if (isset($_GET['cancelar'])) {
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === CARGAR PARA EDITAR ===
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $stmt = $conn->prepare("SELECT id_habitacion, numero, tipo, precio_base FROM Habitaciones WHERE id_habitacion = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $habitacionEditando = $resultado->fetch_assoc();
    $stmt->close();
}

// === ACTUALIZAR HABITACIÓN ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_habitacion'])) {
    $id = (int)$_POST['id_habitacion'];
    $numero = $_POST['numero'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $precio_base = $_POST['precio_base'] ?? '';

    if ($numero && $tipo && $precio_base) {
        if (!is_numeric($numero) || intval($numero) != $numero || $numero <= 0) {
            $mensaje = "El número de habitación debe ser un entero positivo.";
        } else {
            $stmt = $conn->prepare("UPDATE Habitaciones SET numero = ?, tipo = ?, precio_base = ? WHERE id_habitacion = ?");
            $stmt->bind_param("isdi", $numero, $tipo, $precio_base, $id);
            if ($stmt->execute()) {
                $mensaje = "Habitación actualizada con éxito.";
                $habitacionEditando = null;
            } else {
                if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'numero') !== false) {
                    $mensaje = "Ya existe una habitación con ese número.";
                } else {
                    $mensaje = "Error al actualizar: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// === ELIMINAR HABITACIÓN ===
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_habitacion = (int)$_GET['eliminar'];

    // Verificar si tiene reservas o tareas de mantenimiento
    $check1 = $conn->prepare("SELECT 1 FROM Reservas WHERE id_habitacion = ? LIMIT 1");
    $check1->bind_param("i", $id_habitacion);
    $check1->execute();
    
    $check2 = $conn->prepare("SELECT 1 FROM Tareas_Mantenimiento WHERE id_habitacion = ? LIMIT 1");
    $check2->bind_param("i", $id_habitacion);
    $check2->execute();

    if ($check1->get_result()->num_rows > 0 || $check2->get_result()->num_rows > 0) {
        $mensaje = "No se puede eliminar: la habitación tiene reservas o tareas de mantenimiento asociadas.";
    } else {
        $stmt = $conn->prepare("DELETE FROM Habitaciones WHERE id_habitacion = ?");
        $stmt->bind_param("i", $id_habitacion);
        if ($stmt->execute()) {
            $mensaje = "Habitación eliminada con éxito.";
        } else {
            $mensaje = "Error al eliminar.";
        }
        $stmt->close();
    }
    $check1->close();
    $check2->close();
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === REGISTRAR NUEVA HABITACIÓN ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['id_habitacion'])) {
    $numero = $_POST['numero'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $precio_base = $_POST['precio_base'] ?? '';

    if ($numero && $tipo && $precio_base) {
        if (!is_numeric($numero) || intval($numero) != $numero || $numero <= 0) {
            $mensaje = "El número de habitación debe ser un entero positivo.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Habitaciones (numero, tipo, precio_base) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $numero, $tipo, $precio_base);
            if ($stmt->execute()) {
                $mensaje = "Habitación registrada con éxito.";
            } else {
                if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'numero') !== false) {
                    $mensaje = "Ya existe una habitación con ese número.";
                } else {
                    $mensaje = "Error al registrar: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// === LISTAR SIEMPRE ===
$sql_lista = "SELECT id_habitacion, numero, tipo, precio_base, estado_limpieza FROM Habitaciones ORDER BY numero";
$listaHabitaciones = $conn->query($sql_lista);

if (!$listaHabitaciones) {
    $mensaje = "Error al cargar la lista: " . $conn->error;
}
?>