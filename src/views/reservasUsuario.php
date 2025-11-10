<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$controlador_path = __DIR__ . '/../controllers/reservaController.php';
if (file_exists($controlador_path)) {
    require_once $controlador_path;
} else {
    $mensaje = "Error: no se encontró el controlador.";
    $listaReservas = null;
    $reservaEditando = null;
    $listaHuespedes = [];
    $listaHabitaciones = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Reservas</title>
    <link rel="stylesheet" href="../styles/registraryListas.css">
</head>
<body>

<h1>Lista de Reservas</h1>
<p><a href="../index.php" id="inicio">← Volver al inicio</a></p>

<h2>Lista de reservas</h2>
<?php if ($listaReservas && $listaReservas->num_rows > 0): ?>
<table>
    <tr>
        <th>ID</th><th>Huésped</th><th>Habitación</th><th>Llegada</th><th>Salida</th><th>Precio Total</th><th>Acciones</th>
    </tr>
    <?php while ($fila = $listaReservas->fetch_assoc()): ?>
    <tr>
        <td><?= $fila['id_reserva'] ?></td>
        <td><?= htmlspecialchars($fila['nombre_huesped']) ?></td>
        <td>Nº <?= $fila['numero_habitacion'] ?> (<?= htmlspecialchars($fila['tipo_habitacion']) ?>)</td>
        <td><?= $fila['fecha_llegada'] ?></td>
        <td><?= $fila['fecha_salida'] ?></td>
        <td>$<?= number_format($fila['precio_total'],2) ?></td>
        <td>
            <a href="?editar=<?= $fila['id_reserva'] ?>" class="btn-editar">✏️ Editar</a>
            <a href="?eliminar=<?= $fila['id_reserva'] ?>" onclick="return confirm('¿Eliminar esta reserva?');" class="btn-eliminar">❌ Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>No hay reservas registradas todavía.</p>
<?php endif; ?>

</body>
</html>
