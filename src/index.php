<?php
session_start();
// Si la clave 'autenticado' NO está seteada o NO es verdadera,
// redirigimos al usuario al login.
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: views/login.php");
    exit;
}

$nombre = $_SESSION['nombreUsuario'];

if (isset($_GET['tema']) && in_array($_GET['tema'], ['oscuro', 'claro'], true)) {
    $valor = $_GET['tema'];
    $caducidad = time() + (86400 * 30); 
    setcookie('preferencia_tema', $valor, $caducidad, '/');
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$prefTema = isset($_COOKIE['preferencia_tema']) ? $_COOKIE['preferencia_tema'] : 'claro';
$modoOscuro = ($prefTema === 'oscuro') ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel El Gran Descanso - Inicio</title>
    <link rel="stylesheet" href="styles/indexStyle.css">
    <style>
        body {
            background-color: <?php echo $prefTema === 'oscuro' ? '#333' : '#f5f6fa'; ?>;
            color: <?php echo $prefTema === 'oscuro' ? '#fff' : '#000'; ?>;
        }
        .card {
            background-color: <?php echo $prefTema === 'oscuro' ? '#444' : '#fff'; ?>;
            color: <?php echo $prefTema === 'oscuro' ? '#fff' : '#000'; ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Hotel El Gran Descanso</h1>
            <p class="subtitle">Sistema integral de gestión hotelera: huéspedes, habitaciones, reservas y limpieza.</p>
            <p class="user-greeting">Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</p>
        </header>

        <div class="cards">
            <?php if ($nombre == 'admin'): ?>
                <a href="views/huespedes.php" class="card">
                    <div class="card-icon">👤</div>
                    <h2>Huéspedes</h2>
                    <p>Registrar, editar, eliminar y gestionar la información de tus huéspedes.</p>
                    <span class="card-link">Gestionar</span>
                </a>

                <a href="views/habitaciones.php" class="card">
                    <div class="card-icon">🏨</div>
                    <h2>Habitaciones</h2>
                    <p>Administra tus habitaciones: tipo, precio, número y disponibilidad.</p>
                    <span class="card-link">Gestionar</span>
                </a>

                <a href="views/reservas.php" class="card">
                    <div class="card-icon">📅</div>
                    <h2>Reservas</h2>
                    <p>Crea y administra reservas con validación de fechas y disponibilidad.</p>
                    <span class="card-link">Gestionar</span>
                </a>

                <a href="views/limpiezas.php" class="card">
                    <div class="card-icon">🧹</div>
                    <h2>Limpieza</h2>
                    <p>Actualiza el estado de limpieza de cada habitación en tiempo real.</p>
                    <span class="card-link">Gestionar</span>
                </a>
            <?php else: ?>
                <a href="views/reservasUsuario.php" class="card">
                    <div class="card-icon">📅</div>
                    <h2>Reservas</h2>
                    <p>Ver tus reservas. Acceso limitado a la consulta de reservas de usuario.</p>
                    <span class="card-link">Ver Reservas</span>
                </a>
            <?php endif; ?>
        </div>

        <footer>
            <p>Sistema de Gestión Hotelera</p>
        </footer>
    </div>

    <!-- botón cerrar sesión -->
    <form method="post" action="logout.php" style="position: absolute; top: 16px; right: 16px; margin: 0;">
        <button type="submit" style="background:#dc3545;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;">
            Cerrar sesión
        </button>
    </form>

    <!-- botón para alternar modo oscuro/claro (solo PHP/HTML) -->
    <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="position: absolute; top: 16px; left: 16px; margin: 0;">
        <?php if ($prefTema === 'oscuro'): ?>
            <button type="submit" name="tema" value="claro" style="background:#6c757d;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;">
                Modo Claro
            </button>
        <?php else: ?>
            <button type="submit" name="tema" value="oscuro" style="background:#007bff;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;">
                Modo Oscuro
            </button>
        <?php endif; ?>
    </form>
</body>
</html>