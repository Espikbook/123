<?php
session_start();

// Verificar si se pasa el ID del usuario
$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    echo "<h3>Error: No se especificó un usuario.</h3>";
    exit;
}

// Ruta del archivo JSON que contiene las salas
$roomsFile = __DIR__ . '/../data/rooms.json';

// Crear el archivo si no existe
if (!file_exists($roomsFile)) {
    file_put_contents($roomsFile, json_encode([]));
}

// Cargar las salas existentes
$rooms = json_decode(file_get_contents($roomsFile), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Sala</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #FFFFFF; /* Fondo blanco */

        background-size: cover;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    header {
        margin-top: 20px;
        text-align: center;
    }

    h1 {
    color: #000000; /* Texto negro */
    font-size: 2.5rem; /* Tamaño del texto */
    text-align: center; /* Centrado */
    text-transform: uppercase; /* Convertir el texto a mayúsculas (opcional) */
    letter-spacing: 2px; /* Espaciado entre letras */
    margin: 20px 0; /* Espaciado alrededor del título */
}


    main {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px;
        width: 90%;
        max-width: 600px;
    }

    section {
        background: #FFFFFF; /* Fondo amarillo suave */
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    h2 {
        color: #d9531e; /* Naranja intenso */
        margin-bottom: 15px;
        text-align: center;
        font-size: 1.8rem;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    input[type="text"], select {
        width: 100%;
        max-width: 500px;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #d9531e; /* Borde naranja */
        background: #FFFFFF; /* Fondo cálido claro */
        color: #4a2e10; /* Texto marrón */
        font-size: 1rem;
    }

    input[type="text"]::placeholder {
        color: #d9531e; /* Texto de placeholder en naranja */
    }

    button {
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff4e6; /* Texto claro */
        background: #d9531e; /* Fondo naranja */
        border: none;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    button:hover {
        background: #ff8c42; /* Naranja más claro */
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .back-button {
        text-decoration: none;
        color: #fff4e6; /* Texto claro */
        background: #d9531e; /* Fondo naranja */
        padding: 12px 25px;
        border-radius: 8px;
        font-size: 1rem;
        text-align: center;
        font-weight: bold;
        transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .back-button:hover {
        background: #ff8c42; /* Naranja más claro */
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    footer {
        margin-top: 20px;
        text-align: center;
        font-size: 0.9rem;
        color: #4a2e10;
    }

    @media (max-width: 600px) {
        section {
            padding: 15px;
        }

        input[type="text"], select, button {
            width: 100%;
            font-size: 1rem;
        }

        .back-button {
            width: 100%;
        }
    }
</style>

</head>
<body>
    <header>
        <h1>Salas de Videollamada</h1>
    </header>
    <main>
        <section>
            <h2>Crear una Nueva Sala</h2>
            <form action="videocall.php" method="GET" onsubmit="guardarSala(event)">
                <input type="text" id="newRoomName" name="room" placeholder="Nombre de la sala" required>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <button type="submit">Crear Sala</button>
            </form>
        </section>
        <section>
            <h2>Unirse a una Sala Existente</h2>
            <form action="videocall.php" method="GET">
                <select name="room" required id="existingRooms">
                    <option value="" disabled selected>Selecciona una sala...</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= htmlspecialchars($room) ?>"><?= htmlspecialchars($room) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <button type="submit">Unirse a Sala</button>
            </form>
        </section>
    </main>
    <footer>
        <p>Selecciona o crea una sala para empezar tu videollamada.</p>
        <a href="javascript:history.back()" class="back-button">Volver a la Página Anterior</a>
    </footer>
    <script>
        // Función para guardar una nueva sala en el archivo JSON mediante una solicitud AJAX
        function guardarSala(event) {
            event.preventDefault();
            const roomName = document.getElementById('newRoomName').value;
            const userId = <?= json_encode($user_id) ?>;

            fetch('guardar_sala.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ room: roomName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir a videocall.php con la nueva sala
                    window.location.href = `videocall.php?room=${encodeURIComponent(roomName)}&user_id=${userId}`;
                } else {
                    alert('Error al guardar la sala.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
