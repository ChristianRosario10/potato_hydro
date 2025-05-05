#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>

// WiFi credentials
#define WIFI_SSID "OPPO Reno11 F 5G"
#define WIFI_PASSWORD "122333444455555"

// Firebase configuration - correctly formatted URL without trailing slashes
#define FIREBASE_URL "https://potato-hydro-default-rtdb.firebaseio.com"
// Auth if needed (you can try without auth based on your database rules)
#define FIREBASE_AUTH "fj0tEubGPvDD1RoSgaw3mNjy6coVzsyvps6IbqRR"

// Sensor pins
#define DHT11_PIN 26
#define RELAY_PIN 18
#define PIR_PIN 27
#define SOIL_MOISTURE_PIN 32
#define DHT_SENSOR_TYPE DHT11

// Temperature thresholds
#define TEMP_UPPER_THRESHOLD 30
#define TEMP_LOWER_THRESHOLD 15

// API endpoints
const char *LOCAL_API_ENDPOINT = "http://192.168.79.147:8000/api/sensor-data";
const char *API_KEY = "esp32_hydroponic_key";

// Sensor objects
DHT dht(DHT11_PIN, DHT_SENSOR_TYPE);
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Calibration constants
const int dryValue = 4095; // Value when sensor is in air
const int wetValue = 2000; // Value when sensor is in water

// Timing variables
unsigned long previousMillis = 0;
const long interval = 9000; // 9 seconds between readings
unsigned long displayPreviousMillis = 0;
const long displayInterval = 5000;
int displayState = 0;

// Error counters
int localEndpointErrors = 0;
int firebaseErrors = 0;
const int MAX_ERRORS = 5;

void setup()
{
    Serial.begin(115200);

    // Initialize I2C
    Wire.begin();

    // Initialize sensors and display
    dht.begin();
    lcd.init();
    lcd.backlight();

    // Initialize pins
    pinMode(RELAY_PIN, OUTPUT);
    pinMode(PIR_PIN, INPUT);

    // Show startup message
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connecting WiFi");

    // Connect to WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) // Add timeout
    {
        delay(500);
        Serial.print(".");
        lcd.setCursor(0, 1);
        lcd.print(".");
        attempts++;
    }
    
    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\nWiFi connected");
        Serial.print("IP: ");
        Serial.println(WiFi.localIP());

        // Show connected message
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("WiFi Connected");
        lcd.setCursor(0, 1);
        lcd.print(WiFi.localIP());
    } else {
        Serial.println("\nWiFi connection failed");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("WiFi Failed!");
    }
    delay(2000);
}

void loop()
{
    unsigned long currentMillis = millis();

    // Update sensor readings and send to API
    if (currentMillis - previousMillis >= interval)
    {
        previousMillis = currentMillis;

        float temperature = dht.readTemperature();
        float humidity = dht.readHumidity();
        int pirState = digitalRead(PIR_PIN);
        int soilMoistureValue = analogRead(SOIL_MOISTURE_PIN);
        float soilMoisturePercentage = map(soilMoistureValue, dryValue, wetValue, 0, 100);
        soilMoisturePercentage = constrain(soilMoisturePercentage, 0, 100); // Ensure value stays between 0-100
        bool relayState = false;

        if (!isnan(temperature) && !isnan(humidity))
        {
            // Control relay based on temperature
            if (temperature > TEMP_UPPER_THRESHOLD)
            {
                digitalWrite(RELAY_PIN, HIGH);
                relayState = true;
            }
            else if (temperature < TEMP_LOWER_THRESHOLD)
            {
                digitalWrite(RELAY_PIN, LOW);
                relayState = false;
            }

            // Send data to both local and Firebase endpoints
            sendSensorData(temperature, humidity, soilMoisturePercentage, pirState == HIGH, relayState);
        }
    }

    // Update LCD display
    if (currentMillis - displayPreviousMillis >= displayInterval)
    {
        displayPreviousMillis = currentMillis;
        updateDisplay();
    }
}

void updateDisplay()
{
    float temperature = dht.readTemperature();
    float humidity = dht.readHumidity();
    int pirState = digitalRead(PIR_PIN);
    int soilMoistureValue = analogRead(SOIL_MOISTURE_PIN);
    float soilMoisturePercentage = map(soilMoistureValue, dryValue, wetValue, 0, 100);
    soilMoisturePercentage = constrain(soilMoisturePercentage, 0, 100); // Ensure value stays between 0-100

    lcd.clear();

    switch (displayState)
    {
    case 0:
        lcd.setCursor(0, 0);
        lcd.print("Temp: ");
        lcd.print(temperature, 1);
        lcd.print("C");
        lcd.setCursor(0, 1);
        lcd.print("Humidity: ");
        lcd.print(humidity, 1);
        lcd.print("%");
        break;
    case 1:
        lcd.setCursor(0, 0);
        lcd.print("Motion: ");
        lcd.print(pirState == HIGH ? "YES" : "NO");
        lcd.setCursor(0, 1);
        lcd.print("Fan: ");
        lcd.print(digitalRead(RELAY_PIN) == HIGH ? "ON" : "OFF");
        break;
    case 2:
        lcd.setCursor(0, 0);
        lcd.print("Soil Moisture:");
        lcd.setCursor(0, 1);
        lcd.print(soilMoisturePercentage, 1);
        lcd.print("% ");
        lcd.print(soilMoisturePercentage > 30 ? "WET" : "DRY");
        break;
    case 3:
        lcd.setCursor(0, 0);
        lcd.print("WiFi: ");
        lcd.print(WiFi.status() == WL_CONNECTED ? "OK" : "NO");
        lcd.setCursor(0, 1);
        lcd.print("Sending Data...");
        break;
    }

    displayState = (displayState + 1) % 4;
}

void sendSensorData(float temp, float humidity, float soilMoisture, bool motion, bool relay)
{
    if (WiFi.status() == WL_CONNECTED)
    {
        // Create JSON document
        StaticJsonDocument<256> doc;
        doc["temperature"] = temp;
        doc["humidity"] = humidity;
        doc["soil_moisture"] = soilMoisture;
        doc["motion_detected"] = motion;
        doc["relay_state"] = relay;
        doc["project_id"] = "potato-hydro";
        doc["timestamp"] = millis(); // Add timestamp for history sorting

        String jsonString;
        serializeJson(doc, jsonString);

        // Send to local endpoint (with error tracking)
        if (localEndpointErrors < MAX_ERRORS) {
            HTTPClient httpLocal;
            httpLocal.begin(LOCAL_API_ENDPOINT);
            httpLocal.addHeader("Content-Type", "application/json");
            httpLocal.setTimeout(5000); // 5 second timeout

            int localResponseCode = httpLocal.POST(jsonString);
            if (localResponseCode > 0)
            {
                String response = httpLocal.getString();
                Serial.println("Data sent successfully to local endpoint");
                Serial.print("Response: ");
                Serial.println(response);
                localEndpointErrors = 0; // Reset error counter on success
            }
            else
            {
                Serial.print("Error sending data to local endpoint: ");
                Serial.println(localResponseCode);
                localEndpointErrors++;
                
                if (localEndpointErrors >= MAX_ERRORS) {
                    Serial.println("Local endpoint consistently failing. Disabling...");
                }
            }
            httpLocal.end();
        }

        // Send to Firebase (with error tracking)
        if (firebaseErrors < MAX_ERRORS) {
            HTTPClient httpFirebase;
            
            // Method 1: Try without auth parameter first
            String firebaseEndpoint = String(FIREBASE_URL) + "/sensor_data.json";
            
            // If database rules require auth, use this instead:
            // String firebaseEndpoint = String(FIREBASE_URL) + "/sensor_data.json?auth=" + FIREBASE_AUTH;
            
            httpFirebase.begin(firebaseEndpoint);
            httpFirebase.addHeader("Content-Type", "application/json");
            httpFirebase.setTimeout(10000); // Increased timeout for Firebase

            int firebaseResponseCode = httpFirebase.PUT(jsonString);
            if (firebaseResponseCode > 0)
            {
                String response = httpFirebase.getString();
                
                // Check if response contains error
                if (response.indexOf("\"error\"") >= 0) {
                    Serial.println("Firebase returned an error:");
                    Serial.println(response);
                    firebaseErrors++;
                    
                    // If we get permission denied, try with auth parameter
                    if (response.indexOf("permission_denied") >= 0) {
                        httpFirebase.end();
                        
                        // Try again with auth parameter
                        String firebaseAuthEndpoint = String(FIREBASE_URL) + "/sensor_data.json?auth=" + FIREBASE_AUTH;
                        HTTPClient httpFirebaseAuth;
                        httpFirebaseAuth.begin(firebaseAuthEndpoint);
                        httpFirebaseAuth.addHeader("Content-Type", "application/json");
                        httpFirebaseAuth.setTimeout(10000);
                        
                        int authResponseCode = httpFirebaseAuth.PUT(jsonString);
                        if (authResponseCode > 0) {
                            String authResponse = httpFirebaseAuth.getString();
                            if (authResponse.indexOf("\"error\"") < 0) {
                                Serial.println("Data sent successfully to Firebase with auth");
                                Serial.print("Response: ");
                                Serial.println(authResponse);
                                firebaseErrors = 0;
                            }
                        }
                        httpFirebaseAuth.end();
                    }
                } else {
                    Serial.println("Data sent successfully to Firebase");
                    Serial.print("Response: ");
                    Serial.println(response);
                    firebaseErrors = 0; // Reset error counter on success
                }
            }
            else
            {
                Serial.print("Error sending data to Firebase: ");
                Serial.println(firebaseResponseCode);
                firebaseErrors++;
                
                if (firebaseErrors >= MAX_ERRORS) {
                    Serial.println("Firebase connections consistently failing. Will retry later...");
                    // Reset after some time to try again
                    delay(60000);
                    firebaseErrors = 0;
                }
            }
            httpFirebase.end();

            // Also save to history
            String timestampStr = String(millis());
            HTTPClient httpHistory;
            // Method 1: Try without auth parameter first
            String historyEndpoint = String(FIREBASE_URL) + "/sensor_history/" + timestampStr + ".json";
            
            // If database rules require auth, use this instead:
            // String historyEndpoint = String(FIREBASE_URL) + "/sensor_history/" + timestampStr + ".json?auth=" + FIREBASE_AUTH;
            
            httpHistory.begin(historyEndpoint);
            httpHistory.addHeader("Content-Type", "application/json");
            httpHistory.setTimeout(10000);
            
            int historyResponseCode = httpHistory.PUT(jsonString);
            if (historyResponseCode <= 0) {
                Serial.print("Error sending data to history: ");
                Serial.println(historyResponseCode);
            }
            httpHistory.end();
        }

        // Print the actual data being sent for debugging
        Serial.println("Sent sensor data:");
        Serial.print("Temperature: ");
        Serial.println(temp);
        Serial.print("Humidity: ");
        Serial.println(humidity);
        Serial.print("Soil Moisture: ");
        Serial.println(soilMoisture);
        Serial.print("Motion: ");
        Serial.println(motion ? "Detected" : "Not Detected");
        Serial.print("Relay: ");
        Serial.println(relay ? "ON" : "OFF");
    }
    else
    {
        Serial.println("WiFi not connected");
        // Try to reconnect
        WiFi.disconnect();
        delay(1000);
        WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
        Serial.println("Attempting to reconnect to WiFi...");
    }
} 