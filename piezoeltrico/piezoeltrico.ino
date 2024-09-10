#define ledcSetup
#define ledcAttachPin

const int pinN4 = 22;    // Pin GPIO del ESP32 para N4
const int pinM3 = 2;     // Pin GPIO del ESP32 para M3
const int freq = 25000;  // Frecuencia deseada (25 kHz)
const int pulseWidth = 20; // Ancho del pulso en microsegundos (simulando 50% del ciclo de trabajo)
const int period = 1000000 / freq; // Periodo total en microsegundos (40 µs)

void setup() {
  // Configura los pines como salidas
  pinMode(pinN4, OUTPUT);
  pinMode(pinM3, OUTPUT);
}

void loop() {
  // Simula la señal PWM para N4
  digitalWrite(pinN4, HIGH);
  delayMicroseconds(pulseWidth); // Tiempo alto del pulso
  digitalWrite(pinN4, LOW);
  delayMicroseconds(period - pulseWidth); // Tiempo bajo del pulso

  // Simula la señal PWM para M3
  digitalWrite(pinM3, HIGH);
  delayMicroseconds(pulseWidth); // Tiempo alto del pulso
  digitalWrite(pinM3, LOW);
  delayMicroseconds(period - pulseWidth); // Tiempo bajo del pulso
}
