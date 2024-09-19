<?php
include 'conexion.php';

if ($con) {
    // Obtener el último dato recabado
    $consulta = "SELECT * FROM Tb_DHT22 ORDER BY fecha_actual DESC LIMIT 1";
    $resultado = mysqli_query($con, $consulta);
    $ultimo_dato = mysqli_fetch_assoc($resultado);
    
    // Obtener todos los datos almacenados
    $consulta_todos = "SELECT * FROM Tb_DHT22 ORDER BY fecha_actual DESC";
    $resultado_todos = mysqli_query($con, $consulta_todos);
} else {
    echo "Falla! conexión con Base de datos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Estación Meteorológica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 30%;
        }
        .card img {
            width: 50px;
            height: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .image-icon {
            vertical-align: middle;
            margin-right: 8px;
        }
        #refresh-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #refresh-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card" id="temp-card">
            <img src="https://cdn-icons-png.flaticon.com/128/1959/1959311.png" alt="Temperature Icon">
            <h2>Temperatura</h2>
            <p id="temp-value"><?php echo isset($ultimo_dato['Temperatura']) ? $ultimo_dato['Temperatura'] . " °C" : "Sin datos"; ?></p>
        </div>
        <div class="card" id="humidity-card">
            <img src="https://cdn-icons-png.flaticon.com/128/2828/2828582.png" alt="Humidity Icon">
            <h2>Humedad</h2>
            <p id="humidity-value"><?php echo isset($ultimo_dato['Humedad']) ? $ultimo_dato['Humedad'] . " %" : "Sin datos"; ?></p>
        </div>
        <div class="card" id="date-card">
            <img src="https://cdn-icons-png.flaticon.com/128/9187/9187977.png" alt="Date Icon">
            <h2>Fecha y Hora</h2>
            <p id="date-value"><?php echo isset($ultimo_dato['fecha_actual']) ? $ultimo_dato['fecha_actual'] : "Sin datos"; ?></p>
        </div>
    </div>

    <button id="refresh-button">Actualizar Datos</button>

    <h2>Historial de Datos</h2>
    <table>
        <thead>
            <tr>
                <th>Temperatura</th>
                <th>Humedad</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>
        <tbody id="data-table-body">
            <?php while ($row = mysqli_fetch_assoc($resultado_todos)): ?>
                <tr>
                    <td><?php echo $row['Temperatura']; ?> °C</td>
                    <td><?php echo $row['Humedad']; ?> %</td>
                    <td><?php echo $row['fecha_actual']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        // Función para actualizar los datos sin recargar la página
        function updateData() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "update_data.php", true); // Llama a un archivo PHP que devuelve los datos más recientes
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    
                    // Actualiza los valores en la página
                    document.getElementById('temp-value').textContent = response.temperatura + " °C";
                    document.getElementById('humidity-value').textContent = response.humedad + " %";
                    document.getElementById('date-value').textContent = response.fecha_actual;

                    // Actualiza la tabla
                    var tableBody = document.getElementById('data-table-body');
                    tableBody.innerHTML = "";
                    response.datos.forEach(function(item) {
                        var row = tableBody.insertRow();
                        row.insertCell(0).textContent = item.Temperatura + " °C";
                        row.insertCell(1).textContent = item.Humedad + " %";
                        row.insertCell(2).textContent = item.fecha_actual;
                    });
                }
            };
            xhr.send();
        }

        // Evento para el botón de actualización
        document.getElementById('refresh-button').addEventListener('click', updateData);

        // Actualiza automáticamente cada 15 minutos (900000 ms)
        setInterval(updateData, 900000);

        // Llamada inicial para cargar datos al cargar la página
        window.onload = updateData;
    </script>
</body>
</html>