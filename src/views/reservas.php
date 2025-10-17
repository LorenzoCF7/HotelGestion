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
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="../styles/registraryListas.css">
</head>
<body>

<h1>Gestión de Reservas</h1>
<p><a href="../index.php" id="inicio">← Volver al inicio</a></p>

<?php if (!empty($mensaje)): ?>
    <div class="<?= strpos($mensaje, 'éxito') !== false ? 'mensaje' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <?php if ($reservaEditando): ?>
        <h3>Editar reserva</h3>
        <form method="POST">
            <input type="hidden" name="id_reserva" value="<?= $reservaEditando['id_reserva'] ?>">
            <label>Huésped:</label>
            <select name="id_huesped" required>
                <option value="">-- Seleccionar huésped --</option>
                <?php foreach ($listaHuespedes as $h): ?>
                    <option value="<?= $h['id_huesped'] ?>" <?= $h['id_huesped']==$reservaEditando['id_huesped']?'selected':'' ?>>
                        <?= htmlspecialchars($h['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Habitación:</label>
            <select name="id_habitacion" required>
                <option value="">-- Seleccionar habitación --</option>
                <?php foreach ($listaHabitaciones as $hab): ?>
                    <option value="<?= $hab['id_habitacion'] ?>" <?= $hab['id_habitacion']==$reservaEditando['id_habitacion']?'selected':'' ?>>
                        Nº <?= $hab['numero'] ?> - <?= htmlspecialchars($hab['tipo']) ?> ($<?= number_format($hab['precio_base'],2) ?>/noche)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Fecha de llegada:</label>
            <input type="date" name="fecha_llegada" value="<?= $reservaEditando['fecha_llegada'] ?>" required>
            <label>Fecha de salida:</label>
            <input type="date" name="fecha_salida" value="<?= $reservaEditando['fecha_salida'] ?>" required>

            <button type="submit">Actualizar Reserva</button>
            <a href="?cancelar=1" style="display: inline-block; margin-top: 10px; text-align: center; color: #6c757d;">Cancelar edición</a>
        </form>
    <?php else: ?>
        <h3>Registrar nueva reserva</h3>
        <form method="POST">
            <label>Huésped:</label>
            <select name="id_huesped" required>
                <option value="">-- Seleccionar huésped --</option>
                <?php foreach ($listaHuespedes as $h): ?>
                    <option value="<?= $h['id_huesped'] ?>"><?= htmlspecialchars($h['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Habitación:</label>
            <select name="id_habitacion" required>
                <option value="">-- Seleccionar habitación --</option>
                <?php foreach ($listaHabitaciones as $hab): ?>
                    <option value="<?= $hab['id_habitacion'] ?>">
                        Nº <?= $hab['numero'] ?> - <?= htmlspecialchars($hab['tipo']) ?> ($<?= number_format($hab['precio_base'],2) ?>/noche)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Fecha de llegada:</label>
            <input type="date" name="fecha_llegada" required>
            <label>Fecha de salida:</label>
            <input type="date" name="fecha_salida" required>

            <button type="submit">Registrar Reserva</button>
        </form>
    <?php endif; ?>
</div>

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
