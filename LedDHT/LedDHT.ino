#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include "DHT.h"

#define DHTPIN 4       // Pin donde está conectado el DHT22
#define DHTTYPE DHT22  // Definir tipo de sensor DHT

DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
  // Inicialización del LCD
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Bienvido mampo...");
  
  // Inicialización del DHT22
  dht.begin();
  
  // Inicialización del monitor serie
  Serial.begin(115200);
  delay(2000);  // Tiempo para la inicialización
}

void loop() {
  // Leer la temperatura y humedad del DHT22
  float temp = dht.readTemperature();
  float humidity = dht.readHumidity();

  // Verificar si la lectura fue exitosa
  if (isnan(temp) || isnan(humidity)) {
    lcd.setCursor(0, 0);
    lcd.print("Error lectura  ");
    Serial.println("Error al leer el DHT22");
  } else {
    // Mostrar temperatura y humedad en el LCD
    lcd.setCursor(0, 0);
    lcd.print("Temp: ");
    lcd.print(temp);
    lcd.print("C  ");
    
    lcd.setCursor(0, 1);
    lcd.print("Hum: ");
    lcd.print(humidity);
    lcd.print("%  ");
    
    // Enviar temperatura y humedad al monitor serie
    Serial.print("Temperatura: ");
    Serial.print(temp);
    Serial.println(" *C");
    
    Serial.print("Humedad: ");
    Serial.print(humidity);
    Serial.println(" %");
  }
  
  delay(2000);  // Espera 2 segundos antes de la próxima lectura
}
