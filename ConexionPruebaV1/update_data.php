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
    
    $response = [
        'temperatura' => isset($ultimo_dato['Temperatura']) ? $ultimo_dato['Temperatura'] : "Sin datos",
        'humedad' => isset($ultimo_dato['Humedad']) ? $ultimo_dato['Humedad'] : "Sin datos",
        'fecha_actual' => isset($ultimo_dato['fecha_actual']) ? $ultimo_dato['fecha_actual'] : "Sin datos",
        'datos' => $datos
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Falla! conexión con Base de datos']);
}
?>
