<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$controlador_path = __DIR__ . '/../controllers/habitacionController.php';
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
    <title>Gestión de Habitaciones</title>
    <link rel="stylesheet" href="../styles/registraryListas.css">
</head>
<body>

    <h1>Gestión de Habitaciones</h1>
    <p><a href="../index.php" style="color: #007bff; text-decoration: none;">← Volver al inicio</a></p>

    <?php if (!empty($mensaje)): ?>
        <div class="<?= strpos($mensaje, 'éxito') !== false ? 'mensaje' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de REGISTRO o EDICIÓN -->
    <div class="form-container">
        <?php if ($habitacionEditando): ?>
            <h3>Editar habitación</h3>
            <form method="POST">
                <input type="hidden" name="id_habitacion" value="<?= $habitacionEditando['id_habitacion'] ?>">
                <label>Número:</label>
                <input type="number" name="numero" value="<?= htmlspecialchars($habitacionEditando['numero']) ?>" min="1" required>

                <label>Tipo:</label>
                <select name="tipo" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="Sencilla" <?= $habitacionEditando['tipo'] === 'Sencilla' ? 'selected' : '' ?>>Sencilla</option>
                    <option value="Doble" <?= $habitacionEditando['tipo'] === 'Doble' ? 'selected' : '' ?>>Doble</option>
                    <option value="Suite" <?= $habitacionEditando['tipo'] === 'Suite' ? 'selected' : '' ?>>Suite</option>
                </select>

                <label>Precio base ($):</label>
                <input type="number" name="precio_base" step="0.01" min="0" 
                       value="<?= htmlspecialchars($habitacionEditando['precio_base']) ?>" required>

                <button type="submit">Actualizar</button>
                <a href="?cancelar=1" style="display: inline-block; margin-top: 10px; text-align: center; color: #6c757d;">Cancelar edición</a>
            </form>
        <?php else: ?>
            <h3>Registrar nueva habitación</h3>
            <form method="POST">
                <label>Número:</label>
                <input type="number" name="numero" min="1" required>

                <label>Tipo:</label>
                <select name="tipo" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="Sencilla">Sencilla</option>
                    <option value="Doble">Doble</option>
                    <option value="Suite">Suite</option>
                </select>

                <label>Precio base ($):</label>
                <input type="number" name="precio_base" step="0.01" min="0" required>

                <button type="submit">Registrar</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>Lista de habitaciones</h2>

    <?php if (isset($listaHabitaciones) && $listaHabitaciones instanceof mysqli_result): ?>
        <?php if ($listaHabitaciones->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Precio Base</th>
                    <th>Estado Limpieza</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $listaHabitaciones->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id_habitacion'] ?></td>
                    <td><?= htmlspecialchars($fila['numero']) ?></td>
                    <td><?= htmlspecialchars($fila['tipo']) ?></td>
                    <td>$<?= number_format($fila['precio_base'], 2) ?></td>
                    <td><?= htmlspecialchars($fila['estado_limpieza']) ?></td>
                    <td>
                        <a href="?editar=<?= $fila['id_habitacion'] ?>" class="btn-editar">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['id_habitacion'] ?>" 
                           class="btn-eliminar"
                           onclick="return confirm('¿Eliminar la habitación Nº <?= addslashes($fila['numero']) ?>?');">
                            ❌ Eliminar
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No hay habitaciones registradas todavía.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="error">No se pudo cargar la lista de habitaciones.</div>
    <?php endif; ?>
</body>
</html>