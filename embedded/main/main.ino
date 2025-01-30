#include <ModbusMaster.h>
#include <LiquidCrystal_I2C.h>

#define MAX485_DE 19
#define MAX485_RE 19

LiquidCrystal_I2C lcd(0x27, 20, 4); 
ModbusMaster node;

void preTransmission() {
  digitalWrite(MAX485_DE, HIGH);
  digitalWrite(MAX485_RE, HIGH);
}

void postTransmission() {
  digitalWrite(MAX485_DE, LOW);
  digitalWrite(MAX485_RE, LOW);
}

void setup() {
  Serial.begin(115200);
  Serial2.begin(4800, SERIAL_8N1, 16, 17);

  lcd.begin(); 
  lcd.backlight();
  
  node.begin(1, Serial2);
  node.preTransmission(preTransmission);
  node.postTransmission(postTransmission);

  pinMode(MAX485_DE, OUTPUT);
  pinMode(MAX485_RE, OUTPUT);
  digitalWrite(MAX485_DE, LOW);
  digitalWrite(MAX485_RE, LOW);

  delay(1000);
}

String interpretNitrogen(int value) {
  if (value < 10) return "Low";
  if (value < 20) return "Medium";
  if (value < 30) return "High";
  return "Excessive";
}

String interpretPhosphorus(int value) {
  if (value < 20) return "Low";
  if (value < 40) return "Medium";
  if (value < 100) return "High";
  return "Excessive";
}

String interpretPotassium(int value) {
  if (value < 75) return "Very Low";
  if (value < 150) return "Low";
  if (value < 250) return "Medium";
  if (value < 800) return "High";
  return "Very High";
}

void readSensor(uint16_t reg, String name, String (*interpretFunc)(int)) {
  uint8_t result = node.readHoldingRegisters(reg, 1);
  if (result == node.ku8MBSuccess) {
    int value = node.getResponseBuffer(0);
    Serial.print(name + ": ");
    Serial.print(value);
    Serial.print(" mg/kg - ");
    Serial.println(interpretFunc(value));
  } else {
    Serial.println("Error reading " + name);
  }
}

void loop() {
  readSensor(0x04, "Nitrogen", interpretNitrogen);
  readSensor(0x05, "Phosphorus", interpretPhosphorus);
  readSensor(0x06, "Potassium", interpretPotassium);

  uint8_t result = node.readHoldingRegisters(0x03, 1);
  if (result == node.ku8MBSuccess) {
    float pH = node.getResponseBuffer(0) / 10.0;
    Serial.print("pH: ");
    Serial.println(pH, 1);
  } else {
    Serial.println("Error reading pH");
  }

  lcdLoop();

  delay(1000);
}

void lcdLoop() {
  lcd.clear(); 
  displayTitle("Soil Parameters");
  soilParamsScreen();
  delay(5000);
  
  lcd.clear(); 
  suggestedCropsScreen();
  delay(5000);
  
  lcd.clear(); 
  riskyWorthCropsScreen();
  delay(5000);
}

void displayTitle(String title) {
  lcd.clear();
  for (int i = 0; i < 20; i++) {
    lcd.setCursor(i, 0);
    lcd.print(title.substring(0, 20 - i)); 
    if (i > 0) {
      lcd.setCursor(i - 1, 0);
      lcd.print(" "); 
    }

    lcd.setCursor(i, 1);
    lcd.print(title.substring(0, 20 - i));
    if (i > 0) {
      lcd.setCursor(i - 1, 1);
      lcd.print(" "); 
    }

    lcd.setCursor(i, 2);
    lcd.print(title.substring(0, 20 - i)); 
    if (i > 0) {
      lcd.setCursor(i - 1, 2);
      lcd.print(" "); 
    }

    lcd.setCursor(i, 3);
    lcd.print(title.substring(0, 20 - i)); 
    if (i > 0) {
      lcd.setCursor(i - 1, 3);
      lcd.print(" "); 
    }
    delay(200);
  }
  lcd.clear(); 
}
void soilParamsScreen() {
  lcd.setCursor(0, 0);           
  lcd.print("Nitrogen:");      
  lcd.setCursor(0, 1);           
  lcd.print("Phosphorus:"); 
  lcd.setCursor(0, 2);           
  lcd.print("Potassium:");          
  lcd.setCursor(0, 3);           
  lcd.print("pH level:");   
}

void suggestedCropsScreen() {
  lcd.setCursor(0, 0); lcd.print("Suggested Crops");

  lcd.setCursor(0, 1); lcd.print("1");
  lcd.setCursor(10, 1); lcd.print("4");

  lcd.setCursor(0, 2); lcd.print("2");
  lcd.setCursor(10, 2); lcd.print("5");

  lcd.setCursor(0, 3); lcd.print("3");
}

void riskyWorthCropsScreen() {
  lcd.setCursor(0, 0); lcd.print("Risky Worth Crops");

  lcd.setCursor(0, 1); lcd.print("1");
  lcd.setCursor(10, 1); lcd.print("4");

  lcd.setCursor(0, 2); lcd.print("2");
  lcd.setCursor(10, 2); lcd.print("5");

  lcd.setCursor(0, 3); lcd.print("3");
}
