#include <ModbusMaster.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define MAX485_DE 19
#define MAX485_RE 19

LiquidCrystal_I2C lcd(0x27, 20, 4); 
ModbusMaster node;
float phLevel; 

const char* ssid = "test";
const char* password = "88888888";
const char* ipAddress = "192.168.31.248";

String suggestedCrops1[5]; // Global variable to store suggested crops 1
String suggestedCrops2[5]; // Global variable to store suggested crops 2
int cropCount1 = 0; // Count of suggested crops 1
int cropCount2 = 0; // Count of suggested crops 2

void preTransmission() {
  digitalWrite(MAX485_DE, HIGH);
  digitalWrite(MAX485_RE, HIGH);
}

void postTransmission() {
  digitalWrite(MAX485_DE, LOW);
  digitalWrite(MAX485_RE, LOW);
}

void setupWifi() {
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}

void postPhLevel(float phLevel) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin("http://" + String(ipAddress) + "/POST_PH");
    
    String postData = "{\n    \"pH_level\": " + String(phLevel) + "\n}"; 
    
    int httpResponseCode = http.POST(postData); 

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(httpResponseCode);
      Serial.println(response);
      
      // Parse the response to extract suggested crops
      DynamicJsonDocument doc(1024);
      deserializeJson(doc, response);
      
      cropCount1 = doc["Suggested Crops 1"].size();
      for (int i = 0; i < cropCount1 && i < 5; i++) {
        suggestedCrops1[i] = doc["Suggested Crops 1"][i]["name"].as<String>();
      }

      cropCount2 = doc["Suggested Crops 2"].size();
      for (int i = 0; i < cropCount2 && i < 5; i++) {
        suggestedCrops2[i] = doc["Suggested Crops 2"][i]["name"].as<String>();
      }
      
    } else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi not connected");
  }
}

void setup() {
  Serial.begin(115200);
  Serial2.begin(4800, SERIAL_8N1, 16, 17);

  setupWifi();

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

void soilParamsScreen(float nitrogen, float phosphorus, float potassium, float phLevel) {
  String nitrogenStatus = interpretNitrogen(nitrogen);
  String phosphorusStatus = interpretPhosphorus(phosphorus);
  String potassiumStatus = interpretPotassium(potassium);

  if (nitrogenStatus != "Low") {
    nitrogenStatus = "";
  } else {
    nitrogenStatus = " LOW";
  }
  if (phosphorusStatus != "Low") {
    phosphorusStatus = "";
  } else {
    phosphorusStatus = " LOW";
  }
  if (potassiumStatus != "Very Low" && potassiumStatus != "Low") {
    potassiumStatus = "";
  } else {
    potassiumStatus = " LOW";
  }

  lcd.setCursor(0, 0);           
  lcd.print("N: " + String(nitrogen) + " mg/kg " + nitrogenStatus);      
  lcd.setCursor(0, 1);           
  lcd.print("P: " + String(phosphorus) + " mg/kg " + phosphorusStatus); 
  lcd.setCursor(0, 2);           
  lcd.print("K: " + String(potassium) + " mg/kg " + potassiumStatus);          
  lcd.setCursor(0, 3);           
  lcd.print("pH: " + String(phLevel));   
}

void suggestedCropsScreen() {
  lcd.setCursor(0, 0); lcd.print("GM/day Based Crops");
  for (int i = 0; i < cropCount1 && i < 5; i++) {
    lcd.setCursor((i < 3) ? 0 : 10, (i < 3) ? i + 1 : i - 2); 
    lcd.print(String(i + 1) + suggestedCrops1[i]);
  }
}

void riskyWorthCropsScreen() {
  lcd.setCursor(0, 0); lcd.print("Price Based Crops");
  for (int i = 0; i < cropCount2 && i < 5; i++) {
    lcd.setCursor((i < 3) ? 0 : 10, (i < 3) ? i + 1 : i - 2); 
    lcd.print(String(i + 1) + suggestedCrops2[i]);
  }
}

void lcdLoop(float nitrogenValue, float phosphorusValue, float potassiumValue, float phLevel) {
  lcd.clear(); 
  displayTitle("Soil Parameters");
  soilParamsScreen(nitrogenValue, phosphorusValue, potassiumValue, phLevel);
  delay(5000);
  
  lcd.clear(); 
  suggestedCropsScreen();
  delay(5000);
  
  lcd.clear(); 
  riskyWorthCropsScreen();
  delay(5000);
}

void loop() {
  float nitrogenValue, phosphorusValue, potassiumValue;

  readSensor(0x04, "Nitrogen", interpretNitrogen);
  nitrogenValue = node.getResponseBuffer(0); 

  readSensor(0x05, "Phosphorus", interpretPhosphorus);
  phosphorusValue = node.getResponseBuffer(0); 

  readSensor(0x06, "Potassium", interpretPotassium);
  potassiumValue = node.getResponseBuffer(0); 

  uint8_t result = node.readHoldingRegisters(0x03, 1);
  if (result == node.ku8MBSuccess) {
    phLevel = node.getResponseBuffer(0) / 10.0; 
    Serial.print("pH: ");
    Serial.println(phLevel, 1);
  } else {
    Serial.println("Error reading pH");
  }

  postPhLevel(phLevel);

  lcdLoop(nitrogenValue, phosphorusValue, potassiumValue, phLevel);

  delay(1000);
}



