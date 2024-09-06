#include <Wire.h>
#include <BH1750.h>

// Pines para el sensor BH1750
#define SDA_PIN 18  // SDA
#define SCL_PIN 5   // SCL

BH1750 lightMeter;

void setup() {
  Serial.begin(115200);
  
  // Inicializar comunicación I2C con pines personalizados
  Wire.begin(SDA_PIN, SCL_PIN);
  
  if (lightMeter.begin()) {
    Serial.println(F("BH1750 iniciado correctamente."));
  } else {
    Serial.println(F("Error al iniciar BH1750. Verifica las conexiones."));
  }
}

void loop() {
  // Leer el valor de luz en lux
  float lux = lightMeter.readLightLevel();
  
  if (lux >= 0) {
    Serial.print("Luz: ");
    Serial.print(lux);
    Serial.println(" lx");
  } else {
    Serial.println("Error leyendo BH1750.");
  }

  delay(1000); // Leer cada 1 segundo
}

