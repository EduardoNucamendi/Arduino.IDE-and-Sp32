#include "DHT.h"

// Definir el tipo de sensor DHT
#define DHTTYPE DHT22

// Definir los pines donde están conectados los DHT22
#define DHTPIN1 15  // Pin para el sensor 1
#define DHTPIN2 4   // Pin para el sensor 2
#define DHTPIN3 17  // Pin para el sensor 3

// Crear instancias de los sensores
DHT dht1(DHTPIN1, DHTTYPE);
DHT dht2(DHTPIN2, DHTTYPE);
DHT dht3(DHTPIN3, DHTTYPE);

void setup() {
  Serial.begin(115200);
  Serial.println("Lectura de tres sensores DHT22");

  // Inicializar los sensores
  dht1.begin();
  dht2.begin();
  dht3.begin();
}

void loop() {
  // Leer temperatura y humedad del sensor 1
  float h1 = dht1.readHumidity();
  float t1 = dht1.readTemperature();

  // Leer temperatura y humedad del sensor 2
  float h2 = dht2.readHumidity();
  float t2 = dht2.readTemperature();

  // Leer temperatura y humedad del sensor 3
  float h3 = dht3.readHumidity();
  float t3 = dht3.readTemperature();

  // Verificar si las lecturas fallaron y mostrar los valores en el monitor serie
  if (isnan(h1) || isnan(t1)) {
    Serial.println("Error al leer el sensor DHT1 (GPIO 15)");
  } else {
    Serial.print("Sensor 1 (GPIO 15): Humedad: ");
    Serial.print(h1);
    Serial.print(" %  Temperatura: ");
    Serial.print(t1);
    Serial.println(" °C");
  }

  if (isnan(h2) || isnan(t2)) {
    Serial.println("Error al leer el sensor DHT2 (GPIO 4)");
  } else {
    Serial.print("Sensor 2 (GPIO 4): Humedad: ");
    Serial.print(h2);
    Serial.print(" %  Temperatura: ");
    Serial.print(t2);
    Serial.println(" °C");
  }

  if (isnan(h3) || isnan(t3)) {
    Serial.println("Error al leer el sensor DHT3 (GPIO 17)");
  } else {
    Serial.print("Sensor 3 (GPIO 17): Humedad: ");
    Serial.print(h3);
    Serial.print(" %  Temperatura: ");
    Serial.print(t3);
    Serial.println(" °C");
  }

  // Esperar 2 segundos antes de tomar la próxima lectura
  delay(2000);
}
