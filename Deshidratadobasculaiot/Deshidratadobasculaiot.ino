#include <WiFi.h> // Librería WiFi para ESP32
#include <HTTPClient.h> // Librería HTTP para hacer peticiones POST
#include "DHT.h" // Librería DHT para los sensores
#include "HX711.h" // Librería HX711 para la balanza

// Definir credenciales de la red WiFi
const char* ssid = "TilinesTec";
const char* password = "ConectatePa123";

// Pines de los tres sensores DHT22
#define DHTPIN1 15
#define DHTPIN2 4
#define DHTPIN3 17
#define DHTTYPE DHT22  // Tipo de sensor DHT22

// Pines para la balanza HX711
const int LOADCELL_DOUT_PIN = 19;
const int LOADCELL_SCK_PIN = 18;

// Crear instancias de los tres sensores DHT22
DHT dht1(DHTPIN1, DHTTYPE);
DHT dht2(DHTPIN2, DHTTYPE);
DHT dht3(DHTPIN3, DHTTYPE);

// Crear instancia del sensor HX711
HX711 balanza;

WiFiClient client;

void setup() {
  Serial.begin(115200);
  Serial.println(F("Lectura de tres DHT22 y HX711 con conexión al servidor"));

  // Iniciar los sensores DHT22
  dht1.begin();
  dht2.begin();
  dht3.begin();

  // Iniciar la balanza
  balanza.begin(LOADCELL_DOUT_PIN, LOADCELL_SCK_PIN);
  balanza.set_scale(397);  // Ajustar la escala
  balanza.tare(10);  // Hacer 10 lecturas para tarar

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
  // Leer y enviar datos de los tres sensores DHT22 y de la balanza HX711
  LecturaYEnvioDatos();

  // Esperar 15 minutos antes de tomar nuevas lecturas
  delay(900000);  // 15 minutos
}

// Función para leer los datos de los sensores y enviarlos al servidor
void LecturaYEnvioDatos() {
  // Leer los datos del sensor 1
  float h1 = dht1.readHumidity();
  float t1 = dht1.readTemperature();

  // Leer los datos del sensor 2
  float h2 = dht2.readHumidity();
  float t2 = dht2.readTemperature();

  // Leer los datos del sensor 3
  float h3 = dht3.readHumidity();
  float t3 = dht3.readTemperature();

  // Leer datos de la balanza HX711
  float peso = 0;
  if (balanza.is_ready()) {
    peso = balanza.get_units(10);  // Promedio de 10 lecturas
  } else {
    Serial.println("HX711 no encontrado.");
    return;
  }

  // Verificar si las lecturas fallaron
  if (isnan(h1) || isnan(t1) || isnan(h2) || isnan(t2) || isnan(h3) || isnan(t3)) {
    Serial.println(F("Error al leer uno o más sensores DHT22"));
    return;
  }

  // Mostrar los valores leídos en el monitor serie
  Serial.print("Sensor 1 - Humedad: "); Serial.print(h1); Serial.print(" %  Temperatura: "); Serial.print(t1); Serial.println(" °C");
  Serial.print("Sensor 2 - Humedad: "); Serial.print(h2); Serial.print(" %  Temperatura: "); Serial.print(t2); Serial.println(" °C");
  Serial.print("Sensor 3 - Humedad: "); Serial.print(h3); Serial.print(" %  Temperatura: "); Serial.print(t3); Serial.println(" °C");
  Serial.print("Peso: "); Serial.println(peso);

  // Enviar los datos al servidor
  EnviarDatos(t1, h1, t2, h2, t3, h3, peso);
}

// Función para enviar los datos mediante POST a un servidor
void EnviarDatos(float temp1, float hum1, float temp2, float hum2, float temp3, float hum3, float peso) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;  // Crear el objeto HTTP
    
    // Crear el cuerpo de datos a enviar, incluyendo los tres sensores y el peso
    String datos_a_enviar = "temp1=" + String(temp1) + "&hum1=" + String(hum1) +
                            "&temp2=" + String(temp2) + "&hum2=" + String(hum2) +
                            "&temp3=" + String(temp3) + "&hum3=" + String(hum3) +
                            "&peso=" + String(peso);

    // Cambiar por la URL de tu servidor
    http.begin(client, "http://samayoaprojects.com.mx/PostDeshidratador.php");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Definir tipo de contenido

    // Enviar los datos por POST
    int codigo_respuesta = http.POST(datos_a_enviar);

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