<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Contenedor para hacer el video responsivo */
        .video-container {
            position: relative;
            padding-top: 56.25%; /* Aspect Ratio 16:9 */
            height: 0;
            overflow: hidden;
            max-width: 100%;
            margin: auto;
            background-color: #000; /* Fondo negro para el contenedor */
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ajusta el video para cubrir el contenedor */
        }
        button {
            display: block;
            margin: 10px;
        }
        a {
            display: block;
            margin: 10px;
            color: blue;
        }
        #counter {
            font-size: 1.5em;
            color: white;
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.5);
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    
    <div class="video-container">
        <video id="video" autoplay></video>
        <div id="counter">00:00</div>
    </div>
    <button id="start">Iniciar Grabación</button>
    <button id="stop" disabled>Detener Grabación</button>
    <button id="upload" style="display:none;">Subir Video</button>
    <a id="download" href="" download="video.webm" style="display:none;">Descargar Video</a>


    <script>
        const videoElement = document.getElementById('video');
        const startButton = document.getElementById('start');
        const stopButton = document.getElementById('stop');
        const uploadButton = document.getElementById('upload');
        const downloadLink = document.getElementById('download');
        const counterElement = document.getElementById('counter');

        let mediaRecorder;
        let recordedChunks = [];
        let recordingTimeout;
        let secondsElapsed = 0;
        let counterInterval;

        // Solicita acceso a la cámara y al micrófono
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                videoElement.srcObject = stream;
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = event => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    clearInterval(counterInterval);
                    const blob = new Blob(recordedChunks, { type: 'video/webm' });
                    recordedChunks = [];
                    const url = URL.createObjectURL(blob);
                    downloadLink.href = url;
                    downloadLink.style.display = 'block';
                    // Habilita el botón para subir el video
                    uploadButton.style.display = 'block';
                    uploadButton.onclick = () => uploadVideo(blob);
                };

                startButton.addEventListener('click', () => {
                    mediaRecorder.start();
                    startButton.disabled = true;
                    stopButton.disabled = false;
                    secondsElapsed = 0;
                    counterElement.textContent = formatTime(secondsElapsed);

                    // Inicia el contador
                    counterInterval = setInterval(() => {
                        secondsElapsed++;
                        counterElement.textContent = formatTime(secondsElapsed);
                    }, 1000);

                    // Establece un límite de tiempo de grabación de 10 segundos
                    recordingTimeout = setTimeout(() => {
                        mediaRecorder.stop();
                        startButton.disabled = false;
                        stopButton.disabled = true;
                        clearTimeout(recordingTimeout);
                    }, 10000); // 10000 milisegundos = 10 segundos
                });

                stopButton.addEventListener('click', () => {
                    mediaRecorder.stop();
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    clearInterval(counterInterval);
                    clearTimeout(recordingTimeout);
                });
            })
            .catch(err => {
                console.error('Error al acceder a la cámara o al micrófono: ', err);
            });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
        }

        function uploadVideo(blob) {
            const formData = new FormData();
            formData.append('video', blob, 'video.webm');

            console.log(blob);

            fetch('https://tu-api-endpoint.com/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Éxito:', data);
                // Puedes agregar lógica aquí para manejar la respuesta de la API
            })
            .catch(error => {
                console.error('Error al subir el video:', error);
            });
        }
    </script>
</body>
</html>