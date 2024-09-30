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
    
    $datos = [];
    while ($row = mysqli_fetch_assoc($resultado_todos)) {
        $datos[] = $row;
    }
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
        #refresh-button, #download-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #refresh-button:hover, #download-button:hover {
            background-color: #0056b3;
        }
        .chart-container {
            width: 90%;
            margin: 0 auto;
            max-width: 800px;
            margin-bottom: 30px;
        }
        canvas {
            background: #fff;
            border-radius: 8px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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
    <button id="download-button">Descargar PDF</button>

    <h2>Gráficas de Datos</h2>
    <div class="chart-container">
        <canvas id="temp-chart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="humidity-chart"></canvas>
    </div>

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

                    // Actualiza los gráficos
                    updateCharts(response.datos);
                }
            };
            xhr.send();
        }

        function updateCharts(datos) {
            var tempLabels = [];
            var tempData = [];
            var humidityLabels = [];
            var humidityData = [];

            datos.forEach(function(item) {
                tempLabels.push(item.fecha_actual);
                tempData.push(item.Temperatura);
                humidityLabels.push(item.fecha_actual);
                humidityData.push(item.Humedad);
            });

            var ctxTemp = document.getElementById('temp-chart').getContext('2d');
            var ctxHumidity = document.getElementById('humidity-chart').getContext('2d');

            new Chart(ctxTemp, {
                type: 'line',
                data: {
                    labels: tempLabels,
                    datasets: [{
                        label: 'Temperatura (°C)',
                        data: tempData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha y Hora'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Temperatura (°C)'
                            }
                        }
                    }
                }
            });

            new Chart(ctxHumidity, {
                type: 'line',
                data: {
                    labels: humidityLabels,
                    datasets: [{
                        label: 'Humedad (%)',
                        data: humidityData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha y Hora'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Humedad (%)'
                            }
                        }
                    }
                }
            });
        }

        function downloadPDF() {
            var doc = new jsPDF('p', 'pt', 'a4');
            doc.text("Datos de Estación Meteorológica", 20, 30);

            html2canvas(document.getElementById('temp-chart')).then(function(canvas) {
                var imgData = canvas.toDataURL('image/png');
                doc.addImage(imgData, 'PNG', 20, 50, 560, 300);
                doc.text("Temperatura", 20, 360);

                html2canvas(document.getElementById('humidity-chart')).then(function(canvas) {
                    var imgData = canvas.toDataURL('image/png');
                    doc.addImage(imgData, 'PNG', 20, 380, 560, 300);
                    doc.text("Humedad", 20, 690);

                    doc.addPage();
                    doc.text("Historial de Datos", 20, 30);

                    var table = document.querySelector('table');
                    html2canvas(table).then(function(canvas) {
                        var imgData = canvas.toDataURL('image/png');
                        doc.addImage(imgData, 'PNG', 20, 50, 560, 600);
                        doc.save('datos_estacion_meteorologica.pdf');
                    });
                });
            });
        }

        document.getElementById('download-button').addEventListener('click', downloadPDF);
        document.getElementById('refresh-button').addEventListener('click', updateData);

        setInterval(updateData, 900000);
        window.onload = updateData;
    </script>
</body>
</html>
