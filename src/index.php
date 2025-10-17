
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel El Gran Descanso - Inicio</title>
    <link rel="stylesheet" href="styles/indexStyle.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Hotel El Gran Descanso</h1>
            <p class="subtitle">Sistema integral de gestión hotelera: huéspedes, habitaciones, reservas y limpieza.</p>
        </header>

        <div class="cards">
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
        </div>

        <footer>
            <p>Sistema de Gestión Hotelera</p>
        </footer>
    </div>
</body>
</html>