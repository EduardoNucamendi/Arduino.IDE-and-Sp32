<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";
    if(isset($_POST['temperatura'])) {
        $temperatura = $_POST['temperatura'];
        echo "Estación meteorológica";
        echo " Temperaura : ".$temperatura;
    }
 
    if(isset($_POST['humedad'])) { 
        $humedad = $_POST['humedad'];
        echo " humedad : ".$humedad;
        date_default_timezone_set('america/Mexico_City');
        $fecha_actual = date("Y-m-d H:i:s");
        
        $consulta = "INSERT INTO Tb_DHT22_4(Temperatura, Humedad, fecha_actual) VALUES ('$temperatura','$humedad', '$fecha_actual')";
       // $consulta = "UPDATE DHT11 SET Temperatura='$temperatura',Humedad='$humedad' WHERE Id = 1";
        $resultado = mysqli_query($con, $consulta);
        if ($resultado){
            echo " Registo en base de datos OK! ";
        } else {
            echo " Falla! Registro BD";
        }
    }
    
    if(isset($_POST['luminosidad'])) {
        $luminosidad = $_POST['luminosidad'];
        echo "Sensor de Luminosidad: ";
        echo " Luz: " . $luminosidad;

        // Establecer zona horaria y obtener la fecha y hora actual
        date_default_timezone_set('America/Mexico_City');
        $fecha_actual = date("Y-m-d H:i:s");
        
        // Consulta SQL para insertar los datos en la tabla Tb_BH1750
        $consulta = "INSERT INTO Tb_BH1750_1(Luz, fecha_actual) VALUES ('$luminosidad', '$fecha_actual')";
        
        // Ejecutar la consulta
        $resultado = mysqli_query($con, $consulta);
        
        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla! Registro en base de datos";
        }
    }
    
} else {
    echo "Falla! Conexion con base de datos";   
}

?>
