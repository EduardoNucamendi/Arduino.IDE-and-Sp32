const int humsuelo = 33;
int valHumsuelo;
float humedadSuavizada = 0.0;
float factorSuavizado = 0.1; // Entre 0 (cambio lento) y 1 (cambio rápido)
unsigned long tiempoAnterior = 0;
const long intervalo = 500; // Intervalo de 500 ms

void setup() {
  Serial.begin(115200);
  pinMode(humsuelo, INPUT);
}

void loop() {
  // Obtén el tiempo actual
  unsigned long tiempoActual = millis();

  // Verifica si ha pasado el intervalo deseado
  if (tiempoActual - tiempoAnterior >= intervalo) {
    // Actualiza el tiempoAnterior
    tiempoAnterior = tiempoActual;
    
    // Lee el valor analógico y lo mapea a un porcentaje de humedad
    valHumsuelo = map(analogRead(humsuelo), 4092, 0, 0, 100);
    valHumsuelo = valHumsuelo * (100.0 / 95);
    if (valHumsuelo > 100) {
    valHumsuelo = 100;
}
    // Suaviza el valor de la humedad utilizando un filtro exponencial
    humedadSuavizada = (factorSuavizado * valHumsuelo) + ((1 - factorSuavizado) * humedadSuavizada);

    // Imprime la humedad suavizada en el monitor serie
    Serial.print("Humedad: ");
    Serial.print(humedadSuavizada);
    Serial.println("%");
  }
}
