<?php
include_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $stmt = $conn->prepare('SELECT contrasena FROM Cuentas WHERE nombre = ?');
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hash);
        $stmt->fetch();
        if ($contrasena === $hash) { 
            $_SESSION['autenticado'] = true;
            $_SESSION['nombreUsuario'] = $nombre;
            header('Location: ../index.php');
            exit();
        } else {
            $mensaje = 'Contraseña incorrecta.';
        }
    } else {
        $mensaje = 'Usuario no encontrado.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            background: #f5f6fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            min-width: 320px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.3rem;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            background: #fafbfc;
        }
        button {
            width: 100%;
            padding: 0.7rem;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #0056b3;
        }
        .mensaje {
            text-align: center;
            margin-bottom: 1rem;
            color: #28a745;
        }
        .error {
            text-align: center;
            margin-bottom: 1rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, 'exitoso') !== false ? 'mensaje' : 'error' ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
        <?php endif; ?>
        <form method="post">
            <label for="nombre">Usuario:</label>
            <input type="text" name="nombre" id="nombre" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
