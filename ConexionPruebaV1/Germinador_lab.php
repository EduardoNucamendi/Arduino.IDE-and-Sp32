<?php
include 'conexion.php';

if ($con) {
    // Obtener el último dato de temperatura y humedad
    $consulta_dht = "SELECT Temperatura, Humedad, fecha_actual FROM Tb_DHT22_4 ORDER BY fecha_actual DESC LIMIT 1";
    $resultado_dht = mysqli_query($con, $consulta_dht);
    $ultimo_dato_dht = mysqli_fetch_assoc($resultado_dht);
    
    // Obtener el último dato de luminiscencia
    $consulta_bh = "SELECT Luz, fecha_actual FROM Tb_BH1750_1 ORDER BY fecha_actual DESC LIMIT 1"; // Cambié 'luz' a 'Luz'
    $resultado_bh = mysqli_query($con, $consulta_bh);
    $ultimo_dato_bh = mysqli_fetch_assoc($resultado_bh);
    
    // Obtener todos los datos de DHT22
    $consulta_todos_dht = "SELECT * FROM Tb_DHT22_4 ORDER BY fecha_actual DESC";
    $resultado_todos_dht = mysqli_query($con, $consulta_todos_dht);
    
    // Obtener todos los datos de BH1750
    $consulta_todos_bh = "SELECT * FROM Tb_BH1750_1 ORDER BY fecha_actual DESC";
    $resultado_todos_bh = mysqli_query($con, $consulta_todos_bh);
    
    $datos_dht = [];
    while ($row = mysqli_fetch_assoc($resultado_todos_dht)) {
        $datos_dht[] = $row;
    }

    $datos_bh = [];
    while ($row = mysqli_fetch_assoc($resultado_todos_bh)) {
        $datos_bh[] = $row;
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
            width: 22%;
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
            max-height: 300px;
            overflow-y: scroll;
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
        .scrollable {
            max-height: 200px;
            overflow-y: scroll;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="card" id="temp-card">
            <img src="https://cdn-icons-png.flaticon.com/128/1959/1959311.png" alt="Temperature Icon">
            <h2>Temperatura</h2>
            <p id="temp-value"><?php echo isset($ultimo_dato_dht['Temperatura']) ? $ultimo_dato_dht['Temperatura'] . " °C" : "Sin datos"; ?></p>
        </div>
        <div class="card" id="humidity-card">
            <img src="https://cdn-icons-png.flaticon.com/128/2828/2828582.png" alt="Humidity Icon">
            <h2>Humedad</h2>
            <p id="humidity-value"><?php echo isset($ultimo_dato_dht['Humedad']) ? $ultimo_dato_dht['Humedad'] . " %" : "Sin datos"; ?></p>
        </div>
        <div class="card" id="lumin-card">
            <img src="https://cdn-icons-png.flaticon.com/128/888/888336.png" alt="Luminiscence Icon">
            <h2>Luminiscencia</h2>
            <p id="lumin-value"><?php echo isset($ultimo_dato_bh['Luz']) ? $ultimo_dato_bh['Luz'] . " lx" : "Sin datos"; ?></p> <!-- Cambié 'luz' a 'Luz' -->
        </div>
        <div class="card" id="date-card">
            <img src="https://cdn-icons-png.flaticon.com/128/9187/9187977.png" alt="Date Icon">
            <h2>Fecha y Hora</h2>
            <p id="date-value"><?php echo isset($ultimo_dato_dht['fecha_actual']) ? $ultimo_dato_dht['fecha_actual'] : "Sin datos"; ?></p>
        </div>
    </div>

    <h2>Gráficas de Datos</h2>
    <div class="chart-container">
        <canvas id="temp-chart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="humidity-chart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="lumin-chart"></canvas>
    </div>

    <h2>Historial de Datos</h2>
    <div class="scrollable">
        <table>
            <thead>
                <tr>
                    <th>Temperatura</th>
                    <th>Humedad</th>
                    <th>Luminiscencia</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody id="data-table-body">
                <?php
                // Combinar datos de ambas tablas basándose en la fecha
                foreach ($datos_dht as $row_dht) {
                    $fecha = $row_dht['fecha_actual'];
                    $luz_value = 'Sin datos'; // Cambié 'lumin_value' a 'luz_value'
                    foreach ($datos_bh as $row_bh) {
                        if ($row_bh['fecha_actual'] === $fecha) {
                            $luz_value = $row_bh['Luz'] . " lx"; // Cambié 'luz' a 'Luz'
                            break;
                        }
                    }
                    echo "<tr>
                            <td>{$row_dht['Temperatura']} °C</td>
                            <td>{$row_dht['Humedad']} %</td>
                            <td>{$luz_value}</td>
                            <td>{$row_dht['fecha_actual']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateCharts(datos_dht, datos_bh) {
            var tempLabels = [];
            var tempData = [];
            var humidityLabels = [];
            var humidityData = [];
            var luminLabels = [];
            var luminData = [];

            datos_dht.forEach(function(item) {
                tempLabels.push(item.fecha_actual);
                tempData.push(item.Temperatura);
                humidityLabels.push(item.fecha_actual);
                humidityData.push(item.Humedad);
            });

            datos_bh.forEach(function(item) {
                luminLabels.push(item.fecha_actual);
                luminData.push(item.Luz); // Cambié 'luz' a 'Luz'
            });

            var ctxTemp = document.getElementById('temp-chart').getContext('2d');
            var ctxHumidity = document.getElementById('humidity-chart').getContext('2d');
            var ctxLumin = document.getElementById('lumin-chart').getContext('2d');

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

            new Chart(ctxLumin, {
                type: 'line',
                data: {
                    labels: luminLabels,
                    datasets: [{
                        label: 'Luminiscencia (lx)',
                        data: luminData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
                                text: 'Luminiscencia (lx)'
                            }
                        }
                    }
                }
            });
        }

        updateCharts(<?php echo json_encode($datos_dht); ?>, <?php echo json_encode($datos_bh); ?>);
    </script>
</body>
</html>

