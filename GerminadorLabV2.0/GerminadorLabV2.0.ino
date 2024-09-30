#include <WiFi.h>        // Librería WiFi para ESP32
#include <HTTPClient.h>   // Librería HTTP para hacer peticiones POST
#include <Wire.h>         // Librería I2C para el sensor BH1750
#include <BH1750.h>       // Librería para el sensor de luz BH1750
#include <DHT.h>
#include <LiquidCrystal_I2C.h> // Biblioteca para el LCD I2C

#define DHTPIN 4           // Pin conectado al DHT22
#define DHTTYPE DHT22      // Definir tipo de sensor como DHT22

// Crear objeto DHT
DHT dht(DHTPIN, DHTTYPE);

// Crear objeto para el LCD (dirección I2C, columnas, filas)
LiquidCrystal_I2C lcd(0x27, 16, 2); // Cambia 0x27 por la dirección de tu LCD si es necesario

// Definir credenciales de la red WiFi
const char* ssid = "US_CICATA";
const char* password = "USCICATA2024";

// Variables para lectura de sensores
float t;
float h;
float lux;

// Crear objeto BH1750
BH1750 lightMeter;

// Variables para la lectura de humedad del suelo
const int HUMSUEL_PIN = 34; // Pin de la humedad del suelo
float humedadSuavizada = 0;  // Variable para almacenar humedad suavizada
float factorSuavizado = 0.1;  // Factor de suavizado

// Variables para el control de la pantalla
unsigned long tiempoAnterior = 0; // Tiempo anterior para la actualización de pantalla
const long intervalo = 2000; // Intervalo de 2 segundos
int pantallaActual = 0; // Estado de la pantalla para alternar entre datos

// Variables para el envío de datos
unsigned long tiempoEnvioDatos = 0; // Tiempo anterior para el envío de datos
const long intervaloEnvio = 900000; // Intervalo de 15 minutos en milisegundos

bool primerEnvioRealizado = false; // Bandera para controlar el primer envío inmediato

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

  // Esperar 2 segundos para dar tiempo al sensor a estabilizarse
  delay(2000);

  // Realizar una lectura inicial para descartar el primer dato
  lux = lightMeter.readLightLevel();
  Serial.print("Lectura inicial de luz descartada: ");
  Serial.println(lux);

  // Iniciar el LCD
  lcd.begin(16, 2); // Iniciar el LCD con 16 columnas y 2 filas
  lcd.backlight();  // Encender la luz de fondo del LCD

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

  // Enviar datos inmediatamente después de iniciar
  EnvioDatos();
  primerEnvioRealizado = true;

  // Esperar un segundo antes de la primera lectura
  delay(1000);
}

void loop() {
  // Obtener el tiempo actual
  unsigned long tiempoActual = millis();

  // Verifica si ha pasado el intervalo deseado para actualizar la pantalla
  if (tiempoActual - tiempoAnterior >= intervalo) {
    // Actualiza el tiempo anterior
    tiempoAnterior = tiempoActual;

    // Alterna entre mostrar temperatura, humedad, humedad del suelo y luminosidad
    if (pantallaActual == 0) {
      // Leer y mostrar temperatura
      float temp = dht.readTemperature();
      if (!isnan(temp)) {
        lcd.clear();
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
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Hum: ");
        lcd.print(humidity);
        lcd.print("%  ");

        Serial.print("Humedad: ");
        Serial.print(humidity);
        Serial.println(" %");
      }
      pantallaActual = 2; // Mostrar luminosidad después de la humedad del aire
    } else if (pantallaActual == 2) {
      // Leer y mostrar luminosidad
      lux = lightMeter.readLightLevel();
      if (lux > 0) { // Asegúrate de que la lectura no sea 0
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Luz: ");
        lcd.print(lux);
        lcd.print(" lx");

        Serial.print("Luz: ");
        Serial.print(lux);
        Serial.println(" lx");
      } else {
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Luz: No data");
        Serial.println("Error: Luz no disponible");
      }
      pantallaActual = 0; // Reiniciar el ciclo de pantallas
    }
  }

  // Enviar datos mediante POST a un servidor cada 15 minutos
  if (primerEnvioRealizado && tiempoActual - tiempoEnvioDatos >= intervaloEnvio) {
    EnvioDatos();
    tiempoEnvioDatos = tiempoActual; // Actualizar tiempo de envío de datos
  }
}

// Función para enviar datos mediante POST a un servidor
void EnvioDatos() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;  // Crear el objeto HTTP
    String datos_a_enviar = "luminosidad=" + String(lux) + "&temperatura=" + String(dht.readTemperature()) + "&humedad=" + String(dht.readHumidity());

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
