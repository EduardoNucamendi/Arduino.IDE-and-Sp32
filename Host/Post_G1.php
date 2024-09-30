<?php
include 'conexion.php'; // Conexión a la base de datos

if ($con) {
    echo "Conexión con base de datos exitosa! ";
    
    // Verificar si se reciben los datos por POST
    if (isset($_POST['temperatura']) && isset($_POST['humedad']) && isset($_POST['luminosidad'])) {
        
        // Obtener los valores recibidos y convertir a cadenas con un máximo de 5 caracteres
        $temperatura = substr($_POST['temperatura'], 0, 5); // Limitar a 5 caracteres
        $humedad = substr($_POST['humedad'], 0, 5);         // Limitar a 5 caracteres
        $luminosidad = substr($_POST['luminosidad'], 0, 5); // Limitar a 5 caracteres
        
        // Mostrar los datos recibidos
        echo " Temperatura: " . $temperatura;
        echo " Humedad: " . $humedad;
        echo " Luminosidad: " . $luminosidad;
        
        // Configurar la zona horaria y obtener la fecha y hora actuales
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date("Y-m-d H:i:s");
        
        // Preparar la consulta para insertar los datos en la tabla G1
        $consulta = "INSERT INTO G1 (Temperatura, Humedad, Luminosidad, fecha_actual) 
                     VALUES ('$temperatura', '$humedad', '$luminosidad', '$fecha_actual')";
        
        // Ejecutar la consulta
        $resultado = mysqli_query($con, $consulta);
        
        // Verificar si la inserción fue exitosa
        if ($resultado) {
            echo " Registro en base de datos OK!";
        } else {
            echo " Falla! Registro en base de datos.";
        }
    } else {
        echo " No se recibieron todos los datos necesarios.";
    }
} else {
    echo "Falla! Conexión con la base de datos.";
}
?>
