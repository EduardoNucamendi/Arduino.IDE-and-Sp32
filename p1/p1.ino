// Definimos los pines GPIO
const int pinGPIO5 = 5;  // LED para la luz verde
const int pinGPIO4 = 4;  // LED para la luz amarilla (parpadeante)
const int pinGPIO2 = 2;  // LED para la luz roja

void setup() {
  Serial.begin(115200);  // Iniciar la comunicación serial

  // Configura los pines GPIO 5, GPIO 4 y GPIO 2 como salidas
  pinMode(pinGPIO5, OUTPUT);
  pinMode(pinGPIO4, OUTPUT);
  pinMode(pinGPIO2, OUTPUT);
}

void loop() {
  // Luz verde (GPIO 5)
  Serial.println("Luz verde encendida (GPIO 5)");
  digitalWrite(pinGPIO5, HIGH);
  digitalWrite(pinGPIO4, LOW);
  digitalWrite(pinGPIO2, LOW);
  countDown(5);  // Cuenta regresiva de 5 segundos
  digitalWrite(pinGPIO5, LOW);  // Apagar luz verde

  // Luz amarilla (GPIO 4)
  Serial.println("Luz amarilla encendida (GPIO 4)");
  unsigned long startMillis = millis();    // Guardar el tiempo de inicio
  int timeLeft = 3;  // Tiempo inicial para la cuenta regresiva
  while (millis() - startMillis < 3000) {  // Mientras pasen menos de 3 segundos
    digitalWrite(pinGPIO4, HIGH);
    Serial.println(timeLeft);  // Imprimir el tiempo restante
    delay(500);
    timeLeft--;
    digitalWrite(pinGPIO4, LOW);
    delay(500);
  }

  // Luz roja (GPIO 2)
  Serial.println("Luz roja encendida (GPIO 2)");
  digitalWrite(pinGPIO2, HIGH);
  countDown(5);  // Cuenta regresiva de 5 segundos
  digitalWrite(pinGPIO2, LOW);  // Apagar luz roja

  // Repetir el ciclo
}

// Función para contar regresivamente e imprimir en el Monitor Serial
void countDown(int seconds) {
  for (int i = seconds; i > 0; i--) {
    Serial.println(i);
    delay(1000);  // Esperar 1 segundo entre cada número
  }
}
