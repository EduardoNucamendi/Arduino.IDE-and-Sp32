<!DOCTYPE html>
<html>
<head>
    <title>Conexión Wi-Fi y URL para ESP32</title>
</head>
<body>
    <h2>Introduce las credenciales de Wi-Fi y URL del servidor</h2>
    <form id="wifiForm">
        <label for="ssid">SSID (Nombre de la red Wi-Fi):</label><br>
        <input type="text" id="ssid" name="ssid" required><br><br>

        <label for="password">Contraseña de Wi-Fi:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="serverURL">URL del Servidor:</label><br>
        <input type="text" id="serverURL" name="serverURL" required><br><br>

        <button type="button" onclick="sendBluetooth()">Enviar al ESP32</button>
    </form>

    <script>
        function sendBluetooth() {
            const ssid = document.getElementById('ssid').value;
            const password = document.getElementById('password').value;
            const serverURL = document.getElementById('serverURL').value;

            // Formato de los datos enviados
            const dataToSend = ssid + ';' + password + ';' + serverURL;

            // Aquí es donde entra la conexión Bluetooth con JavaScript
            if (navigator.bluetooth) {
                navigator.bluetooth.requestDevice({
                    acceptAllDevices: true, // Puedes filtrar por nombre del ESP32 si lo necesitas
                    optionalServices: ['0000ffe0-0000-1000-8000-00805f9b34fb'] // UUID del servicio Bluetooth
                })
                .then(device => device.gatt.connect())
                .then(server => server.getPrimaryService('0000ffe0-0000-1000-8000-00805f9b34fb'))
                .then(service => service.getCharacteristic('0000ffe1-0000-1000-8000-00805f9b34fb'))
                .then(characteristic => {
                    // Convertir los datos a un formato adecuado para enviar (en este caso, a un ArrayBuffer)
                    const encoder = new TextEncoder();
                    const data = encoder.encode(dataToSend);

                    // Enviar los datos
                    return characteristic.writeValue(data);
                })
                .then(() => {
                    alert('Datos enviados al ESP32 correctamente.');
                })
                .catch(error => {
                    console.log('Error en la conexión Bluetooth: ', error);
                    alert('No se pudo conectar con el ESP32.');
                });
            } else {
                alert('El navegador no soporta Web Bluetooth.');
            }
        }
    </script>
</body>
</html>
