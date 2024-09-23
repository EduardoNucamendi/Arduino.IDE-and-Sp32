<?php
include 'conexion.php';  // Incluir el archivo de conexión a la base de datos

if ($con) {
    echo "Conexión con base de datos exitosa! ";
    
    // Verificar si los datos de los sensores fueron enviados
    if (isset($_POST['temp1']) && isset($_POST['hum1']) && 
        isset($_POST['temp2']) && isset($_POST['hum2']) && 
        isset($_POST['temp3']) && isset($_POST['hum3'])) {
        
        // Capturar los datos enviados desde el ESP32
        $temp1 = $_POST['temp1'];
        $hum1 = $_POST['hum1'];
        $temp2 = $_POST['temp2'];
        $hum2 = $_POST['hum2'];
        $temp3 = $_POST['temp3'];
        $hum3 = $_POST['hum3'];

        echo "Datos recibidos de los sensores DHT22:";
        echo " Sensor 1 - Temperatura: " . $temp1 . " °C, Humedad: " . $hum1 . "% ";
        echo " Sensor 2 - Temperatura: " . $temp2 . " °C, Humedad: " . $hum2 . "% ";
        echo " Sensor 3 - Temperatura: " . $temp3 . " °C, Humedad: " . $hum3 . "% ";

        // Establecer la zona horaria
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date("Y-m-d H:i:s");

        // Insertar los datos del sensor 1 en la tabla Tb_DHT22_1
        $consulta1 = "INSERT INTO Tb_DHT22_1 (Temperatura, Humedad, fecha_actual) VALUES ('$temp1', '$hum1', '$fecha_actual')";
        $resultado1 = mysqli_query($con, $consulta1);

        // Insertar los datos del sensor 2 en la tabla Tb_DHT22_2
        $consulta2 = "INSERT INTO Tb_DHT22_2 (Temperatura, Humedad, fecha_actual) VALUES ('$temp2', '$hum2', '$fecha_actual')";
        $resultado2 = mysqli_query($con, $consulta2);

        // Insertar los datos del sensor 3 en la tabla Tb_DHT22_3
        $consulta3 = "INSERT INTO Tb_DHT22_3 (Temperatura, Humedad, fecha_actual) VALUES ('$temp3', '$hum3', '$fecha_actual')";
        $resultado3 = mysqli_query($con, $consulta3);

        }
}

?>