<?php
include_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
$reservaEditando = null;
$listaHuespedes = [];
$listaHabitaciones = [];

// === Cargar listas para selects (siempre) ===
$huespedes_res = $conn->query("SELECT id_huesped, nombre, email FROM Huespedes ORDER BY nombre");
if ($huespedes_res) {
    while ($fila = $huespedes_res->fetch_assoc()) {
        $listaHuespedes[] = $fila;
    }
}

$habitaciones_res = $conn->query("SELECT id_habitacion, numero, tipo, precio_base FROM Habitaciones ORDER BY numero");
if ($habitaciones_res) {
    while ($fila = $habitaciones_res->fetch_assoc()) {
        $listaHabitaciones[] = $fila;
    }
}

// === CANCELAR EDICIÓN ===
if (isset($_GET['cancelar'])) {
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === CARGAR PARA EDITAR ===
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $stmt = $conn->prepare("SELECT id_reserva, id_huesped, id_habitacion, fecha_llegada, fecha_salida FROM Reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $res = $stmt->get_result();
    $reservaEditando = $res->fetch_assoc();
    $stmt->close();
}

// === ACTUALIZAR RESERVA ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_reserva'])) {
    $id_reserva    = (int)$_POST['id_reserva'];
    $id_huesped    = $_POST['id_huesped'] ?? null;
    $id_habitacion = $_POST['id_habitacion'] ?? null;
    $fecha_llegada = $_POST['fecha_llegada'] ?? null;
    $fecha_salida  = $_POST['fecha_salida'] ?? null;

    if ($id_huesped && $id_habitacion && $fecha_llegada && $fecha_salida) {
        $fecha_llegada_obj = DateTime::createFromFormat('Y-m-d', $fecha_llegada);
        $fecha_salida_obj  = DateTime::createFromFormat('Y-m-d', $fecha_salida);

        if (!$fecha_llegada_obj || !$fecha_salida_obj || 
            $fecha_llegada_obj->format('Y-m-d') !== $fecha_llegada || 
            $fecha_salida_obj->format('Y-m-d') !== $fecha_salida) {
            $mensaje = "Formato de fecha inválido.";
        } else {
            if ($fecha_salida <= $fecha_llegada) {
                $mensaje = "La fecha de salida debe ser posterior a la de llegada.";
            } else {
                $precio_base = 0;
                foreach ($listaHabitaciones as $hab) {
                    if ($hab['id_habitacion'] == $id_habitacion) {
                        $precio_base = $hab['precio_base'];
                        break;
                    }
                }
                $noches = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / (60 * 60 * 24);
                $precio_total = $precio_base * $noches;

                $stmt = $conn->prepare("
                    UPDATE Reservas 
                    SET id_huesped = ?, id_habitacion = ?, fecha_llegada = ?, fecha_salida = ?, precio_total = ?, fecha_reserva = CURDATE()
                    WHERE id_reserva = ?
                ");
                // Corrección clave: usar "iissdi" (i=int, s=string, d=double)
                $stmt->bind_param("iissdi", $id_huesped, $id_habitacion, $fecha_llegada, $fecha_salida, $precio_total, $id_reserva);

                if ($stmt->execute()) {
                    $mensaje = "Reserva actualizada con éxito.";
                    $reservaEditando = null;
                } else {
                    $mensaje = "Error al actualizar: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// === REGISTRAR NUEVA RESERVA ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['id_reserva'])) {
    $id_huesped    = $_POST['id_huesped'] ?? null;
    $id_habitacion = $_POST['id_habitacion'] ?? null;
    $fecha_llegada = $_POST['fecha_llegada'] ?? null;
    $fecha_salida  = $_POST['fecha_salida'] ?? null;

    if (!$id_huesped || !$id_habitacion || !$fecha_llegada || !$fecha_salida) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        $fecha_llegada_obj = DateTime::createFromFormat('Y-m-d', $fecha_llegada);
        $fecha_salida_obj  = DateTime::createFromFormat('Y-m-d', $fecha_salida);

        if (!$fecha_llegada_obj || !$fecha_salida_obj || 
            $fecha_llegada_obj->format('Y-m-d') !== $fecha_llegada || 
            $fecha_salida_obj->format('Y-m-d') !== $fecha_salida) {
            $mensaje = "Formato de fecha inválido.";
        } else {
            $hoy = date('Y-m-d');

            if ($fecha_llegada < $hoy) {
                $mensaje = "La fecha de llegada no puede ser anterior a hoy.";
            } elseif ($fecha_salida <= $fecha_llegada) {
                $mensaje = "La fecha de salida debe ser posterior a la de llegada.";
            } else {
                $precio_base = 0;
                foreach ($listaHabitaciones as $hab) {
                    if ($hab['id_habitacion'] == $id_habitacion) {
                        $precio_base = $hab['precio_base'];
                        break;
                    }
                }

                $noches = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / (60 * 60 * 24);
                $precio_total = $precio_base * $noches;

                $stmt = $conn->prepare("
                    INSERT INTO Reservas (id_huesped, id_habitacion, fecha_llegada, fecha_salida, precio_total, fecha_reserva)
                    VALUES (?, ?, ?, ?, ?, CURDATE())
                ");
                // Corrección clave: usar "iissd" (5 parámetros)
                $stmt->bind_param("iissd", $id_huesped, $id_habitacion, $fecha_llegada, $fecha_salida, $precio_total);

                if ($stmt->execute()) {
                    $mensaje = "Reserva registrada con éxito.";
                } else {
                    $mensaje = "Error al registrar: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}

// === ELIMINAR RESERVA ===
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_reserva = (int)$_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM Reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_reserva);
    if ($stmt->execute()) {
        $mensaje = "Reserva eliminada con éxito.";
    } else {
        $mensaje = "Error al eliminar: " . $stmt->error;
    }
    $stmt->close();
    header("Location: " . basename($_SERVER['PHP_SELF']));
    exit();
}

// === LISTAR RESERVAS ===
$sql = "
    SELECT 
        r.id_reserva,
        r.fecha_llegada,
        r.fecha_salida,
        r.precio_total,
        r.estado,
        h.nombre AS nombre_huesped,
        hab.numero AS numero_habitacion,
        hab.tipo AS tipo_habitacion
    FROM Reservas r
    INNER JOIN Huespedes h ON r.id_huesped = h.id_huesped
    INNER JOIN Habitaciones hab ON r.id_habitacion = hab.id_habitacion
    ORDER BY r.fecha_reserva DESC, r.fecha_llegada DESC
";
$listaReservas = $conn->query($sql);
if (!$listaReservas) {
    $mensaje = "Error al cargar las reservas: " . $conn->error;
}
?>