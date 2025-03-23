#include <ModbusMaster.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define MAX485_DE 4
#define MAX485_RE 4

LiquidCrystal_I2C lcd(0x27, 20, 4); 
ModbusMaster node;
float phLevel; 

// CUSTOMIZEABLE - START
const char* ssid = "test";
const char* password = "88888888";
const char* ipAddress = "192.168.60.248";
// CUSTOMIZEABLE - END

String suggestedCrops1[5]; 
String suggestedCrops2[5]; 
int cropCount1 = 0;
int cropCount2 = 0;

bool apiConnectionSuccessful = true; 

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
    Serial.println("Connecting to WiFi");
    delay(500);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connecting to WiFi");
    delay(500);
    lcd.setCursor(0, 1);
    lcd.print(".");
    delay(500);
    lcd.setCursor(0, 2);
    lcd.print("..");
    delay(500);
    lcd.setCursor(0, 3);
    lcd.print("...");
  }
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connected to WiFi!");
  delay(500); 
}

void reconnectWifi() {
 
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    Serial.println("Reconnecting WiFi");
    delay(500);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Reconnecting WiFi");
    delay(500);
    lcd.setCursor(0, 1);
    lcd.print(".");
    delay(500);
    lcd.setCursor(0, 2);
    lcd.print("..");
    delay(500);
    lcd.setCursor(0, 3);
    lcd.print("...");
  }
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connected to WiFi!");
  delay(500); 
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
      apiConnectionSuccessful = true; 
      

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
      apiConnectionSuccessful = false; 
    }
    http.end();
  } else {
    Serial.println("WiFi not connected");
    apiConnectionSuccessful = false; 
  }
}

void postSensorData(float nitrogen, float phosphorus, float potassium, float phLevel) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin("http://" + String(ipAddress) + "/POST_SENSOR_DATA");
    
    DynamicJsonDocument doc(2048);
    doc["nitrogen"] = nitrogen;
    doc["phosphorus"] = phosphorus;
    doc["potassium"] = potassium;
    doc["pH_level"] = phLevel;
    
    JsonArray crops1 = doc.createNestedArray("suggestedCrops1");
    for (int i = 0; i < cropCount1 && i < 5; i++) {
      JsonObject crop = crops1.createNestedObject();
      crop["name"] = suggestedCrops1[i];
    }
    
    JsonArray crops2 = doc.createNestedArray("suggestedCrops2");
    for (int i = 0; i < cropCount2 && i < 5; i++) {
      JsonObject crop = crops2.createNestedObject();
      crop["name"] = suggestedCrops2[i];
    }
    
    String postData;
    serializeJson(doc, postData);
    
    int httpResponseCode = http.POST(postData); 

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(httpResponseCode);
      Serial.println(response);
      apiConnectionSuccessful = true;
    } else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
      apiConnectionSuccessful = false;
    }
    http.end();
  } else {
    Serial.println("WiFi not connected");
    apiConnectionSuccessful = false;
  }
}

void setup() {
  Serial.begin(115200);
  Serial2.begin(4800, SERIAL_8N1, 16, 17);

  lcd.begin(); 
  lcd.backlight();

  setupWifi();
 
  node.begin(1, Serial2);
  node.preTransmission(preTransmission);
  node.postTransmission(postTransmission);

  pinMode(MAX485_DE, OUTPUT);
  pinMode(MAX485_RE, OUTPUT);
  digitalWrite(MAX485_DE, LOW);
  digitalWrite(MAX485_RE, LOW);

  delay(500);
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
  lcd.setCursor(0, 0); 
  lcd.print("GM/day Based Crops");

  if (!apiConnectionSuccessful) {
    lcd.setCursor(0, 2);
    lcd.print("API Conn Failed");
  } else if (cropCount1 == 0) {
    lcd.setCursor(0, 2);
    lcd.print("N/A Crops");
  } else {
    for (int i = 0; i < cropCount1 && i < 5; i++) {
      int row = (i < 3) ? i + 1 : i - 2; 
      int col = (i < 3) ? 0 : 10;       
      
      String cropName = suggestedCrops1[i];
      String prefix = String(i + 1) + "."; 

      lcd.setCursor(col, row);
      lcd.print(prefix);

      int textStart = col + prefix.length();
      
      if (cropName.length() > 8) {
        for (int shift = 0; shift <= cropName.length() - 8; shift++) {
          lcd.setCursor(textStart, row);
          lcd.print(cropName.substring(shift, shift + 8)); 
          delay(400); 
        }
      } else {
        lcd.setCursor(textStart, row);
        lcd.print(cropName);
      }
    }
  }
}

void riskyWorthCropsScreen() {
  lcd.setCursor(0, 0); 
  lcd.print("Price Based Crops");

  if (!apiConnectionSuccessful) {
    lcd.setCursor(0, 2);
    lcd.print("API Conn Failed");
  } else if (cropCount2 == 0) {
    lcd.setCursor(0, 2);
    lcd.print("N/A Crops");
  } else {
    for (int i = 0; i < cropCount2 && i < 5; i++) {
      int row = (i < 3) ? i + 1 : i - 2;
      int col = (i < 3) ? 0 : 10;

      String cropName = suggestedCrops2[i];
      String prefix = String(i + 1) + ".";

      lcd.setCursor(col, row);
      lcd.print(prefix); 

      int textStart = col + prefix.length();

      if (cropName.length() > 8) {
        for (int shift = 0; shift <= cropName.length() - 8; shift++) {
          lcd.setCursor(textStart, row);
          lcd.print(cropName.substring(shift, shift + 8));
          delay(400);
        }
      } else {
        lcd.setCursor(textStart, row);
        lcd.print(cropName);
      }
    }
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

bool isWiFiConnected() {
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi Disconnected");
    Serial.println("WiFi Disconnected");
    return false;
  } 
  return true;
}

void loop() {

  if (!isWiFiConnected()) {
    reconnectWifi();
  }

  uint8_t result;
  float pH;
  float nitrogen;
  float phosphorus;
  float potassium;

  result = node.readHoldingRegisters(0x04, 1);
  if (result == node.ku8MBSuccess) {
    nitrogen = node.getResponseBuffer(0);
    Serial.print("Nitrogen: ");
    Serial.print(node.getResponseBuffer(0));
    Serial.println(" mg/kg");
  } else {
    Serial.println("Error reading Nitrogen");
  }

  // Read Phosphorus from register 0x05 (40006 in decimal)
  result = node.readHoldingRegisters(0x05, 1);
  if (result == node.ku8MBSuccess) {
    phosphorus = node.getResponseBuffer(0);
    Serial.print("Phosphorus: ");
    Serial.print(node.getResponseBuffer(0));
    Serial.println(" mg/kg");
  } else {
    Serial.println("Error reading Phosphorus");
  }

  // Read Potassium from register 0x06 (40007 in decimal)
  result = node.readHoldingRegisters(0x06, 1);
  if (result == node.ku8MBSuccess) {
    potassium = node.getResponseBuffer(0);
    Serial.print("Potassium: ");
    Serial.print(node.getResponseBuffer(0));
    Serial.println(" mg/kg");
  } else {
    Serial.println("Error reading Potassium");
  }

  // Read pH from register 0x03 (40004 in decimal)
  result = node.readHoldingRegisters(0x03, 1);
  if (result == node.ku8MBSuccess) {
    pH = node.getResponseBuffer(0) / 10.0;
    Serial.print("pH: ");
    Serial.println(pH, 1);
  } else {
    Serial.println("Error reading pH");
  }

  postPhLevel(pH);
  postSensorData(nitrogen, phosphorus, potassium, pH);

  lcdLoop(nitrogen, phosphorus, potassium, pH);

  delay(500);
}



