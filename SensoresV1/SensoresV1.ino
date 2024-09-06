  #include <Wire.h>
  #include <LiquidCrystal_I2C.h>
  #include "DHT.h"

  // Definición de pines y variables
  #define DHTPIN 4       // Pin donde está conectado el DHT22
  #define DHTTYPE DHT22  // Definir tipo de sensor DHT
  #define HUMSUEL_PIN 33 // Pin para la humedad del suelo
  #define RELAY_PIN 15   // Pin para el relé

  DHT dht(DHTPIN, DHTTYPE);
  LiquidCrystal_I2C lcd(0x27, 16, 2);

  float humedadSuavizada = 0.0;
  float factorSuavizado = 0.1; // Entre 0 (cambio lento) y 1 (cambio rápido)
  unsigned long tiempoAnterior = 0;
  const long intervalo = 2000; // Intervalo de 2000 ms

  int pantallaActual = 0;

  void setup() {
    // Inicialización del LCD
    lcd.init();
    lcd.backlight();
    lcd.setCursor(0, 0);
    lcd.print("Bienvenido mampo...");

    // Inicialización del DHT22
    dht.begin();

    // Inicialización del monitor serie
    Serial.begin(115200);
    delay(2000);  // Tiempo para la inicialización

    pinMode(HUMSUEL_PIN, INPUT);
    pinMode(RELAY_PIN, OUTPUT);
  }

  void loop() {
    // Obtén el tiempo actual
    unsigned long tiempoActual = millis();

    // Verifica si ha pasado el intervalo deseado
    if (tiempoActual - tiempoAnterior >= intervalo) {
      // Actualiza el tiempoAnterior
      tiempoAnterior = tiempoActual;

      // Alterna entre mostrar temperatura, humedad y humedad del suelo
      if (pantallaActual == 0) {
        // Leer y mostrar temperatura
        float temp = dht.readTemperature();
        if (!isnan(temp)) {
          lcd.setCursor(0, 0);
          lcd.print("Temp: ");
          lcd.print(temp);
          lcd.print("C  ");

          Serial.print("Temperatura: ");
          Serial.print(temp);
          Serial.println(" *C");
        }
        pantallaActual = 1;
      } else if (pantallaActual == 1) {
        // Leer y mostrar humedad del aire
        float humidity = dht.readHumidity();
        if (!isnan(humidity)) {
          lcd.setCursor(0, 0);
          lcd.print("Hum: ");
          lcd.print(humidity);
          lcd.print("%  ");

          Serial.print("Humedad: ");
          Serial.print(humidity);
          Serial.println(" %");
        }
        pantallaActual = 2;
      } else if (pantallaActual == 2) {
        // Leer y mostrar humedad del suelo
        int valHumsuelo = map(analogRead(HUMSUEL_PIN), 4092, 0, 0, 100);
        humedadSuavizada = (factorSuavizado * valHumsuelo) + ((1 - factorSuavizado) * humedadSuavizada);

        lcd.setCursor(0, 0);
        lcd.print("Hum suelo: ");
        lcd.print(humedadSuavizada);
        lcd.print("%  ");

        Serial.print("Humedad del suelo: ");
        Serial.print(humedadSuavizada);
        Serial.println(" %");

        // Activar el relé si la humedad del suelo es menor o igual al 20%
        if (humedadSuavizada <= 20) {
          digitalWrite(RELAY_PIN, HIGH); // Activa el relé
          Serial.println("Relé activado.");
        } else {
          digitalWrite(RELAY_PIN, LOW); // Desactiva el relé
          Serial.println("Relé desactivado.");
        }

        pantallaActual = 0; // Reinicia el ciclo de pantallas
      }
    }
  }
