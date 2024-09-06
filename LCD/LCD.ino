#include <Wire.h>
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Hola, Mampo!!");
  lcd.setCursor(0, 1);
  lcd.print("IoT sistema de monitoreo");
}

void loop() {
  // No hay código en loop
}
