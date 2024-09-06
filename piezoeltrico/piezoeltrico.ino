// Definición de los pines
const int RPWM_Pin = 26; // Control hacia adelante
const int LPWM_Pin = 27; // Control en reversa
const int REN_Pin = 35;  // Habilita el movimiento hacia adelante
const int LEN_Pin = 34;  // Habilita el movimiento en reversa

void setup() {
  // Inicializar los pines como salida
  pinMode(RPWM_Pin, OUTPUT);
  pinMode(LPWM_Pin, OUTPUT);
  pinMode(REN_Pin, OUTPUT);
  pinMode(LEN_Pin, OUTPUT);

  // Inicializar el monitor serie para depuración
  Serial.begin(115200);

  // Habilitar ambos canales del puente H
  digitalWrite(REN_Pin, HIGH); // Habilitar canal hacia adelante
  digitalWrite(LEN_Pin, LOW);  // Deshabilitar canal en reversa

  // Iniciar con el piezoeléctrico apagado
  analogWrite(RPWM_Pin, 0);
  analogWrite(LPWM_Pin, 0);

  Serial.println("Sistema inicializado");
}

void loop() {
  // Enviar pulsos de PWM para el piezoeléctrico
  int frecuencia = 100000; // Frecuencia de 100 kHz
  int tiempoOn = 1 / (2 * frecuencia); // Tiempo de pulso (mitad de ciclo)
  
  // Generar pulsos de alta frecuencia
  for (int i = 0; i < 5; i++) {
    digitalWrite(RPWM_Pin, HIGH); // Encender el piezoeléctrico
    delayMicroseconds(tiempoOn);  // Mantener encendido
    digitalWrite(RPWM_Pin, LOW);  // Apagar el piezoeléctrico
    delayMicroseconds(tiempoOn);  // Mantener apagado
  }

  // Pausa de 1 segundo entre ráfagas de pulsos
  delay(1000);
}
