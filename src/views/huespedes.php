<?php
// Mostrar errores en desarrollo (quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$controlador_path = __DIR__ . '/../controllers/huespedController.php';
if (file_exists($controlador_path)) {
    require_once $controlador_path;
} else {
    $mensaje = "Error: no se encontró el controlador (ruta esperada: $controlador_path).";
    $listaHuespedes = null;
    $huéspedEditando = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Huéspedes</title>
    <link rel="stylesheet" href="../styles/registraryListas.css">
</head>
<body>

    <h1>Gestión de Huéspedes</h1>
    <p><a href="../index.php" id="inicio">← Volver al inicio</a></p>

    <?php if (!empty($mensaje)): ?>
        <div class="<?= strpos($mensaje, 'éxito') !== false ? 'mensaje' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de REGISTRO o EDICIÓN -->
    <div class="form-container">
        <?php if ($huéspedEditando): ?>
            <h3>Editar huésped</h3>
            <form method="POST" id="formHuesped">
                <input type="hidden" name="id_huesped" value="<?= $huéspedEditando['id_huesped'] ?>">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($huéspedEditando['nombre']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($huéspedEditando['email']) ?>" required>

                <label>Documento:</label>
                <input type="text" name="documento" value="<?= htmlspecialchars($huéspedEditando['documento_identidad']) ?>" required>

                <button type="submit">Actualizar</button>
                <a href="?cancelar=1" style="display: inline-block; margin-top: 10px; text-align: center; color: #6c757d;">Cancelar edición</a>
            </form>
        <?php else: ?>
            <h3>Registrar nuevo huésped</h3>
            <form method="POST" id="formHuesped">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Documento:</label>
                <input type="text" name="documento" required>

                <button type="submit">Registrar</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>Lista de huéspedes</h2>

    <?php if (isset($listaHuespedes) && $listaHuespedes instanceof mysqli_result): ?>
        <?php if ($listaHuespedes->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Documento</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $listaHuespedes->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id_huesped'] ?></td>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['email']) ?></td>
                    <td><?= htmlspecialchars($fila['documento_identidad']) ?></td>
                    <td>
                        <a href="?editar=<?= $fila['id_huesped'] ?>" class="btn-editar">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['id_huesped'] ?>" 
                           class="btn-eliminar"
                           onclick="return confirm('¿Eliminar a <?= addslashes(htmlspecialchars($fila['nombre'])) ?>?');">
                            ❌ Eliminar
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No hay huéspedes registrados todavía.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="error">No se pudo cargar la lista de huéspedes.</div>
    <?php endif; ?>

    
</body>
</html>