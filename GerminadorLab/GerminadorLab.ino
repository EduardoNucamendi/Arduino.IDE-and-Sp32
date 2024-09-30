#include <WiFi.h>        // Librería WiFi para ESP32
#include <HTTPClient.h>   // Librería HTTP para hacer peticiones POST
#include <Wire.h>         // Librería I2C para el sensor BH1750
#include <BH1750.h>       // Librería para el sensor de luz BH1750
#include <DHT.h>

#define DHTPIN 4           // Pin conectado al DHT22
#define DHTTYPE DHT22      // Definir tipo de sensor como DHT22

// Crear objeto DHT
DHT dht(DHTPIN, DHTTYPE);

// Definir credenciales de la red WiFi
const char* ssid = "TilinesTec";
const char* password = "ConectatePa123";
//const char* ssid = "US_CICATA";
//const char* password = "USCICATA2024";

// Variables para lectura de sensores
float lux;
float t;
float h;

// Crear objeto BH1750
BH1750 lightMeter;

void setup() {
  Serial.begin(115200);
  Serial.println(F("Iniciando sistema BH1750 y DHT22..."));
  
  // Iniciar sensor DHT22
  dht.begin();
  Serial.println(F("Sensor DHT22 iniciado correctamente"));
  
  // Inicializar I2C para el BH1750
  Wire.begin(21, 22);  // Pines SDA y SCL del ESP32
  
  // Iniciar el sensor BH1750
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    Serial.println(F("Sensor BH1750 iniciado correctamente"));
  } else {
    Serial.println(F("Error al iniciar el sensor BH1750"));
    while (1);
  }

  // Conectar a la red WiFi
  WiFi.begin(ssid, password);
  Serial.print("Conectando a WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConexión WiFi establecida");
  Serial.print("IP local: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Leer luminosidad del BH1750
  LecturaLuminosidad();
  
  // Leer temperatura y humedad del DHT22
  LecturaTH();
  
  // Enviar los datos mediante POST
  EnvioDatos();
  
  delay(900000);  // 15 minutos
}

// Función para leer luminosidad del sensor BH1750
void LecturaLuminosidad() {
  lux = lightMeter.readLightLevel();
  
  if (lux == -1) {
    Serial.println(F("Error al leer el sensor BH1750"));
  } else {
    Serial.print(F("Luminosidad: "));
    Serial.print(lux);
    Serial.println(F(" lx"));
  }
}

// Función para leer temperatura y humedad del DHT22
void LecturaTH() {
  h = dht.readHumidity();    // Leer humedad
  t = dht.readTemperature(); // Leer temperatura en Celsius

  // Comprobar si las lecturas fallaron
  if (isnan(h) || isnan(t)) {
    Serial.println(F("Error: No se pudo leer el sensor DHT22 (valores NaN)"));
  } else {
    // Mostrar los valores en el monitor serie
    Serial.print(F("Humedad: "));
    Serial.print(h);
    Serial.print(F("%  Temperatura: "));
    Serial.print(t);
    Serial.println(F("°C"));
  }
}

// Función para enviar datos mediante POST a un servidor
void EnvioDatos() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;  // Crear el objeto HTTP
    String datos_a_enviar = "luminosidad=" + String(lux) + "&temperatura=" + String(t) + "&humedad=" + String(h);

    Serial.println("Datos que se enviarán: " + datos_a_enviar);

    // Cambiar por la URL de tu servidor
    http.begin("http://samayoaprojects.com.mx/Post_G1.php");
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

