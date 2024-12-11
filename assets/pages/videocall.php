<?php
session_start();
if (!isset($_GET['room'])) {
    echo "<h3>Error: No se especificó una sala en la URL.</h3>";
    exit;
}
$room = htmlspecialchars($_GET['room']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videollamada - Sala <?php echo $room; ?></title>
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

    h1 {
        font-size: 2.5rem;
        margin: 20px;
        text-align: center;
        color: #000000; /* Texto blanco */
        padding-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .video-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        width: 100%;
        max-width: 900px;
        margin-bottom: 30px;
    }

    video {
        background: #d9531e; /* Fondo naranja cuando no hay video */
        border-radius: 10px; /* Bordes redondeados */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Sombra */
        width: 100%;
        max-width: 400px;
        height: 225px; /* Altura fija para mantener proporción */
        object-fit: cover; /* Ajusta el contenido del video */
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    video:hover {
        transform: scale(1.03); /* Suave zoom */
        box-shadow: 0 6px 15px rgba(230, 130, 0, 0.5); /* Sombra más intensa */
    }

    .control-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }

    button {
        padding: 12px 30px;
        font-size: 1rem;
        font-weight: 500;
        color: #ffffff; /* Texto blanco */
        background: #d9531e; /* Naranja intermedio */
        border: none;
        border-radius: 5px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    }

    button:hover {
        background: #f69100; /* Naranja más claro */
        box-shadow: 0 4px 8px rgba(246, 145, 0, 0.4);
        transform: translateY(-2px);
    }

    button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .back-button {
        margin-top: 30px;
        text-decoration: none;
        color: #ffffff; /* Texto blanco */
        background: #d9531e; /* Naranja intermedio */
        padding: 12px 25px;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 500;
        text-align: center;
        display: inline-block;
        transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    }

    .back-button:hover {
        background: #f69100; /* Naranja más claro */
        box-shadow: 0 4px 8px rgba(246, 145, 0, 0.4);
        transform: translateY(-2px);
    }

    .back-button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        video {
            width: 90%;
        }

        button {
            width: 90%;
            padding: 15px;
        }

        .control-buttons {
            flex-direction: column;
        }

        .back-button {
            width: 90%;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        h1 {
            font-size: 1.8rem;
        }

        button {
            font-size: 0.9rem;
        }
    }
</style>





</head>
<body>
    <h1>Sala: <?php echo $room; ?></h1>
    <div class="video-container">
        <video id="localVideo" autoplay muted></video>
        <video id="remoteVideo" autoplay></video>
    </div>
    <button id="startButton">Iniciar Llamada</button>
    <div class="control-buttons">
        <button id="toggleCameraButton">Desactivar Cámara</button>
        <button id="toggleMicButton">Desactivar Micrófono</button>
    </div>
    <!-- Botón para volver al menú principal -->
    <a href="javascript:history.back()" class="back-button">Finalizar Llamada</a>
    <script>
        const toggleCameraButton = document.getElementById('toggleCameraButton');
        const toggleMicButton = document.getElementById('toggleMicButton');
        const localVideo = document.getElementById('localVideo');

        let cameraEnabled = true;
        let micEnabled = true;

        // Alternar cámara
        toggleCameraButton.addEventListener('click', () => {
            if (localVideo.srcObject) {
                const videoTracks = localVideo.srcObject.getVideoTracks();
                if (videoTracks.length > 0) {
                    cameraEnabled = !cameraEnabled;
                    videoTracks[0].enabled = cameraEnabled;
                    toggleCameraButton.textContent = cameraEnabled ? 'Desactivar Cámara' : 'Activar Cámara';
                }
            }
        });

        // Alternar micrófono
        toggleMicButton.addEventListener('click', () => {
            if (localVideo.srcObject) {
                const audioTracks = localVideo.srcObject.getAudioTracks();
                if (audioTracks.length > 0) {
                    micEnabled = !micEnabled;
                    audioTracks[0].enabled = micEnabled;
                    toggleMicButton.textContent = micEnabled ? 'Desactivar Micrófono' : 'Activar Micrófono';
                }
            }
        });
    </script>
    <script src="../js/videocall.js"></script>
</body>
</html>
