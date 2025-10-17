<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$controlador_path = __DIR__ . '/../controllers/limpiezaController.php';
if (file_exists($controlador_path)) {
    require_once $controlador_path;
} else {
    $mensaje = "Error: no se encontró el controlador.";
    $listaHabitaciones = null;
    $habitacionEditando = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Limpieza</title>
    <link rel="stylesheet" href="../styles/registraryListas.css">
</head>
<body>

    <h1>Gestión de Limpieza</h1>
    <p><a href="../index.php" id="inicio">← Volver al inicio</a></p>

    <?php if (!empty($mensaje)): ?>
        <div class="<?= strpos($mensaje, 'éxito') !== false ? 'mensaje' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de EDICIÓN de estado de limpieza -->
    <div class="form-container">
        <?php if ($habitacionEditando): ?>
            <h3>Actualizar estado de limpieza</h3>
            <form method="POST">
                <input type="hidden" name="id_habitacion" value="<?= $habitacionEditando['id_habitacion'] ?>">
                <p><strong>Habitación:</strong> Nº <?= $habitacionEditando['numero'] ?> (<?= htmlspecialchars($habitacionEditando['tipo']) ?>)</p>

                <label>Estado de limpieza:</label>
                <select name="estado_limpieza" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="Limpia" <?= $habitacionEditando['estado_limpieza'] === 'Limpia' ? 'selected' : '' ?>>Limpia</option>
                    <option value="Sucia" <?= $habitacionEditando['estado_limpieza'] === 'Sucia' ? 'selected' : '' ?>>Sucia</option>
                    <option value="En Limpieza" <?= $habitacionEditando['estado_limpieza'] === 'En Limpieza' ? 'selected' : '' ?>>En Limpieza</option>
                </select>

                <button type="submit">Actualizar Estado</button>
                <a href="?cancelar=1" style="display: inline-block; margin-top: 10px; text-align: center; color: #6c757d;">Cancelar</a>
            </form>
        <?php else: ?>
            <h3>Selecciona una habitación para actualizar su estado de limpieza</h3>
        <?php endif; ?>
    </div>

    <h2>Estado de limpieza de habitaciones</h2>

    <?php if (isset($listaHabitaciones) && $listaHabitaciones instanceof mysqli_result): ?>
        <?php if ($listaHabitaciones->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Estado Limpieza</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $listaHabitaciones->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['numero'] ?></td>
                    <td><?= htmlspecialchars($fila['tipo']) ?></td>
                    <td>
                        <?php
                        $estado = $fila['estado_limpieza'];
                        $clase = 'estado-' . strtolower(str_replace(' ', '-', $estado));
                        echo "<span class=\"$clase\">" . htmlspecialchars($estado) . "</span>";
                        ?>
                    </td>
                    <td>
                        <a href="?editar=<?= $fila['id_habitacion'] ?>" class="btn-editar">✏️ Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No hay habitaciones registradas.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="error">No se pudo cargar la lista de habitaciones.</div>
    <?php endif; ?>

</body>
</html>