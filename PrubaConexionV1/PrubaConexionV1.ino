#include <WiFi.h> // Librería WiFi para ESP32
#include <HTTPClient.h> // Librería HTTP para hacer peticiones POST

// Definir credenciales de la red WiFi
const char* ssid = "TilinesTec";
const char* password = "ConectatePa123";

// Variables para lectura del DHT22
float t;
float h;
float f;
float hif;
float hic;

#include "DHT.h"

#define DHTPIN 4       // Pin digital conectado al DHT22 (D4 en ESP32)
#define DHTTYPE DHT22  // DHT22 (AM2302)

DHT dht(DHTPIN, DHTTYPE);
WiFiClient client;

void setup() {
  Serial.begin(115200);
  Serial.println(F("DHT22 con conexión al servidor"));
  dht.begin(); // Iniciar el sensor DHT

  // Conectar a la red WiFi
  WiFi.begin(ssid, password);
  Serial.print("Conectando");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConexión WiFi establecida");
  Serial.print("IP local: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Leer temperatura y humedad del DHT22
  LecturaTH();
  
  // Enviar los datos mediante POST
  EnvioDatos();
  
  delay(60000); // Esperar 60 segundos entre envíos
}

// Función para leer temperatura y humedad
void LecturaTH() {
  h = dht.readHumidity();    // Leer humedad
  t = dht.readTemperature(); // Leer temperatura en Celsius
  f = dht.readTemperature(true); // Leer temperatura en Fahrenheit

  // Comprobar si las lecturas fallaron
  if (isnan(h) || isnan(t) || isnan(f)) {
    Serial.println(F("Error al leer el sensor DHT22"));
    return;
  }

  // Calcular el índice de calor en Fahrenheit
  hif = dht.computeHeatIndex(f, h);
  // Calcular el índice de calor en Celsius
  hic = dht.computeHeatIndex(t, h, false);

  // Mostrar los valores en el monitor serie
  Serial.print(F("Humedad: "));
  Serial.print(h);
  Serial.print(F("%  Temperatura: "));
  Serial.print(t);
  Serial.print(F("°C  Índice de calor: "));
  Serial.print(hic);
  Serial.println(F("°C"));
}

// Función para enviar datos mediante POST a un servidor
void EnvioDatos() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;  // Crear el objeto HTTP
    String datos_a_enviar = "temperatura=" + String(t) + "&humedad=" + String(h);

    // Cambiar por la URL de tu servidor
    http.begin(client, "http://germinadoripn.ddns.net/");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Definir tipo de contenido

    int codigo_respuesta = http.POST(datos_a_enviar); // Enviar los datos por POST

    // Comprobar el código de respuesta HTTP
    if (codigo_respuesta > 0) {
      Serial.println("Código HTTP: " + String(codigo_respuesta));
      if (codigo_respuesta == 200) {
        String cuerpo_respuesta = http.getString();
        Serial.println("Respuesta del servidor: ");
        Serial.println(cuerpo_respuesta);
      }
    } else {
      Serial.print("Error en el envío POST, código: ");
      Serial.println(codigo_respuesta);
    }

    http.end(); // Liberar recursos
  } else {
    Serial.println("Error en la conexión WiFi");
  }
}
