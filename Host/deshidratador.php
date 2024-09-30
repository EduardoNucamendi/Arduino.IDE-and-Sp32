<?php
include 'conexion.php';

if ($con) {
    // Obtener el último dato recabado para cada sensor
    $consulta_dht1 = "SELECT * FROM Tb_DHT22_1 ORDER BY fecha_actual DESC LIMIT 1";
    $resultado_dht1 = mysqli_query($con, $consulta_dht1);
    $ultimo_dato_dht1 = mysqli_fetch_assoc($resultado_dht1);

    $consulta_dht2 = "SELECT * FROM Tb_DHT22_2 ORDER BY fecha_actual DESC LIMIT 1";
    $resultado_dht2 = mysqli_query($con, $consulta_dht2);
    $ultimo_dato_dht2 = mysqli_fetch_assoc($resultado_dht2);

    $consulta_dht3 = "SELECT * FROM Tb_DHT22_3 ORDER BY fecha_actual DESC LIMIT 1";
    $resultado_dht3 = mysqli_query($con, $consulta_dht3);
    $ultimo_dato_dht3 = mysqli_fetch_assoc($resultado_dht3);
    
    // Obtener todos los datos almacenados para cada sensor
    $consulta_todos_dht1 = "SELECT * FROM Tb_DHT22_1 ORDER BY fecha_actual DESC";
    $resultado_todos_dht1 = mysqli_query($con, $consulta_todos_dht1);
    
    $consulta_todos_dht2 = "SELECT * FROM Tb_DHT22_2 ORDER BY fecha_actual DESC";
    $resultado_todos_dht2 = mysqli_query($con, $consulta_todos_dht2);
    
    $consulta_todos_dht3 = "SELECT * FROM Tb_DHT22_3 ORDER BY fecha_actual DESC";
    $resultado_todos_dht3 = mysqli_query($con, $consulta_todos_dht3);
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .card {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            color: #1e88e5;
        }
        .table-container {
            max-height: 200px; /* Ajusta la altura según lo necesites */
            overflow: auto;
            margin-bottom: 40px;
            border: 1px solid #90caf9;
            border-radius: 8px;
            background-color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #90caf9;
            text-align: center;
        }
        th {
            background-color: #90caf9;
        }
        tbody tr:nth-child(even) {
            background-color: #e1f5fe;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sensor 1 -->
        <div class="card">
            <h2>Sensor 1</h2>
            <p><strong>Temperatura:</strong> <?php echo isset($ultimo_dato_dht1['Temperatura']) ? $ultimo_dato_dht1['Temperatura'] . " °C" : "Sin datos"; ?></p>
            <p><strong>Humedad:</strong> <?php echo isset($ultimo_dato_dht1['Humedad']) ? $ultimo_dato_dht1['Humedad'] . " %" : "Sin datos"; ?></p>
        </div>

        <!-- Sensor 2 -->
        <div class="card">
            <h2>Sensor 2</h2>
            <p><strong>Temperatura:</strong> <?php echo isset($ultimo_dato_dht2['Temperatura']) ? $ultimo_dato_dht2['Temperatura'] . " °C" : "Sin datos"; ?></p>
            <p><strong>Humedad:</strong> <?php echo isset($ultimo_dato_dht2['Humedad']) ? $ultimo_dato_dht2['Humedad'] . " %" : "Sin datos"; ?></p>
        </div>

        <!-- Sensor 3 -->
        <div class="card">
            <h2>Sensor 3</h2>
            <p><strong>Temperatura:</strong> <?php echo isset($ultimo_dato_dht3['Temperatura']) ? $ultimo_dato_dht3['Temperatura'] . " °C" : "Sin datos"; ?></p>
            <p><strong>Humedad:</strong> <?php echo isset($ultimo_dato_dht3['Humedad']) ? $ultimo_dato_dht3['Humedad'] . " %" : "Sin datos"; ?></p>
        </div>

        <h2>Historial de Datos</h2>

        <!-- Tabla para Sensor 1 -->
        <h3>Sensor 1</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Temperatura</th>
                        <th>Humedad</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado_todos_dht1)): ?>
                        <tr>
                            <td><?php echo $row['Temperatura']; ?> °C</td>
                            <td><?php echo $row['Humedad']; ?> %</td>
                            <td><?php echo $row['fecha_actual']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla para Sensor 2 -->
        <h3>Sensor 2</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Temperatura</th>
                        <th>Humedad</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado_todos_dht2)): ?>
                        <tr>
                            <td><?php echo $row['Temperatura']; ?> °C</td>
                            <td><?php echo $row['Humedad']; ?> %</td>
                            <td><?php echo $row['fecha_actual']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla para Sensor 3 -->
        <h3>Sensor 3</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Temperatura</th>
                        <th>Humedad</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado_todos_dht3)): ?>
                        <tr>
                            <td><?php echo $row['Temperatura']; ?> °C</td>
                            <td><?php echo $row['Humedad']; ?> %</td>
                            <td><?php echo $row['fecha_actual']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
            function updateData() {
                // Implementación para actualizar datos de cada sensor
            }

            document.getElementById('download-button').addEventListener('click', downloadPDF);
            document.getElementById('refresh-button').addEventListener('click', updateData);

            setInterval(updateData, 900000);
            window.onload = updateData;
        </script>
    </div>
</body>
</html>
