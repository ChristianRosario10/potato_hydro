#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// WiFi credentials - use your own
#define WIFI_SSID "OPPO Reno11 F 5G"
#define WIFI_PASSWORD "122333444455555"

// Firebase URL - this is your database URL
#define FIREBASE_URL "https://potato-hydro-default-rtdb.firebaseio.com"

void setup() {
  Serial.begin(115200);
  Serial.println("Firebase Node Setup Utility");
  
  // Connect to WiFi
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Connecting to WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println();
  Serial.print("Connected with IP: ");
  Serial.println(WiFi.localIP());
  
  // Create initial sensor_data node
  createSensorDataNode();
  
  // Create sensor_history node
  createSensorHistoryNode();
  
  Serial.println("Firebase database nodes setup complete!");
}

void createSensorDataNode() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // Create JSON data
    StaticJsonDocument<256> doc;
    doc["temperature"] = 0.0;
    doc["humidity"] = 0.0;
    doc["soil_moisture"] = 0.0;
    doc["motion_detected"] = false;
    doc["relay_state"] = false;
    doc["project_id"] = "potato-hydro";
    doc["timestamp"] = 0;
    
    String jsonString;
    serializeJson(doc, jsonString);
    
    // Create sensor_data node
    String endpoint = String(FIREBASE_URL) + "/sensor_data.json";
    http.begin(endpoint);
    http.addHeader("Content-Type", "application/json");
    
    int httpResponseCode = http.PUT(jsonString);
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("sensor_data node created successfully");
      Serial.print("Response: ");
      Serial.println(response);
    } else {
      Serial.print("Error creating sensor_data node: ");
      Serial.println(httpResponseCode);
    }
    
    http.end();
  }
}

void createSensorHistoryNode() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // Create an empty object for the history node
    String jsonString = "{}";
    
    // Create sensor_history node
    String endpoint = String(FIREBASE_URL) + "/sensor_history.json";
    http.begin(endpoint);
    http.addHeader("Content-Type", "application/json");
    
    int httpResponseCode = http.PUT(jsonString);
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("sensor_history node created successfully");
      Serial.print("Response: ");
      Serial.println(response);
    } else {
      Serial.print("Error creating sensor_history node: ");
      Serial.println(httpResponseCode);
    }
    
    http.end();
  }
}

void loop() {
  // This utility only runs once
  Serial.println("Setup complete. You can now upload your main code.");
  delay(10000);
} 