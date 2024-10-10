// Definición de pines
const int sensorNivelAguaPin = 34;  // Pin del sensor de nivel de agua
const int ledNivelBajoPin = 5;     // LED para nivel bajo
const int ledNivelAltoPin = 32;     // LED para nivel alto

// Variable para almacenar el tiempo cuando se detecta el nivel alto
unsigned long tiempoInicioNivelAlto = 0;
// Variable para controlar si el temporizador de 5 segundos ya se ha iniciado
bool nivelAltoDetectado = false;

void setup() {
  // Configurar los pines de los LEDs como salida
  pinMode(ledNivelBajoPin, OUTPUT);
  pinMode(ledNivelAltoPin, OUTPUT);
  
  // Configurar el pin del sensor como entrada
  pinMode(sensorNivelAguaPin, INPUT);
  
  // Inicializar LEDs apagados
  digitalWrite(ledNivelBajoPin, LOW);
  digitalWrite(ledNivelAltoPin, LOW);

  Serial.begin(115200);  // Iniciar comunicación serial para monitorear datos
}

void loop() {
  // Leer el valor del sensor de nivel de agua
  int nivelAgua = digitalRead(sensorNivelAguaPin);
  
  // Si el nivel de agua está bajo (sensor no detecta agua)
  if (nivelAgua == LOW) {
    // Reiniciar el temporizador
    tiempoInicioNivelAlto = 0;
    nivelAltoDetectado = false;
    
    // Encender LED de nivel bajo y apagar LED de nivel alto
    digitalWrite(ledNivelBajoPin, HIGH);  
    digitalWrite(ledNivelAltoPin, LOW);   
    Serial.println("Nivel de agua bajo. LED de nivel bajo encendido.");
  }
  // Si el nivel de agua está alto (sensor detecta agua)
  else {
    // Apagar el LED de nivel bajo
    digitalWrite(ledNivelBajoPin, LOW);   
    
    // Si el nivel de agua ha sido detectado como alto y no se ha iniciado el temporizador
    if (!nivelAltoDetectado) {
      // Registrar el tiempo cuando se detecta nivel alto
      tiempoInicioNivelAlto = millis();
      nivelAltoDetectado = true;
      Serial.println("Nivel de agua alto detectado. Esperando 5 segundos...");
    }

    // Verificar si han pasado 5 segundos desde que se detectó el nivel alto
    if (nivelAltoDetectado && (millis() - tiempoInicioNivelAlto >= 5000)) {
      // Encender el LED de nivel alto después de 5 segundos
      digitalWrite(ledNivelAltoPin, HIGH);
      Serial.println("Nivel de agua alto. LED de nivel alto encendido.");
    }
  }

  delay(500);  // Retardo para estabilizar las lecturas
}
