# Firebase Setup Guide for ESP32 Hydroponic Monitoring System

This guide will help you set up your Firebase Realtime Database to work with the ESP32 hydroponic monitoring system.

## 1. Firebase Database Structure

The ESP32 code is designed to work with the following Firebase Realtime Database structure:

```
potato-hydro-default-rtdb
├── sensor_data
│   ├── temperature: float
│   ├── humidity: float
│   ├── soil_moisture: float
│   ├── motion_detected: boolean
│   ├── relay_state: boolean
│   ├── project_id: "potato-hydro"
│   └── timestamp: number
└── sensor_history
    ├── [timestamp1]
    │   ├── temperature: float
    │   ├── humidity: float
    │   ├── soil_moisture: float
    │   ├── motion_detected: boolean
    │   ├── relay_state: boolean
    │   ├── project_id: "potato-hydro"
    │   └── timestamp: number
    ├── [timestamp2]
    │   └── ...
    └── ...
```

## 2. Firebase Database Rules

For your ESP32 to write to Firebase, you need to set appropriate database rules:

### Option 1: Public Rules (for testing only)
```json
{
  "rules": {
    ".read": true,
    ".write": true
  }
}
```

### Option 2: Using Authentication
```json
{
  "rules": {
    ".read": "auth != null",
    ".write": "auth != null"
  }
}
```

To set these rules:
1. Go to your Firebase console: https://console.firebase.google.com/
2. Navigate to your project
3. Select "Realtime Database" from the left menu
4. Click on the "Rules" tab
5. Update the rules and click "Publish"

## 3. Initialization Script

You can use the provided `firebase_setup.ino` script to initialize your database structure. This will create the necessary nodes:

1. Upload the `firebase_setup.ino` file to your ESP32
2. Open the Serial Monitor to verify the nodes were created successfully
3. Once you see "Firebase database nodes setup complete!", your database is ready

## 4. Common Issues & Solutions

### "404 Not Found" Error
This is usually caused by incorrect Firebase URL formatting. Make sure:
- You're using the correct format: `https://potato-hydro-default-rtdb.firebaseio.com`
- You're including the `-default-rtdb` part in the URL
- Your database exists and is properly set up

### "Permission Denied" Error
This means your database rules don't allow writing. Either:
- Set public rules as shown in Option 1 (for testing only)
- Use authentication parameters in your requests with `?auth=YOUR_AUTH_TOKEN`

### ESP32 Code Not Connecting
1. Ensure your WiFi credentials are correct
2. Verify the Firebase URL is correct
3. Check that your database exists and is initialized

## 5. Using the Main ESP32 Code

After setting up your Firebase database, you can upload the main `esp32_hydroponic_monitor.ino` code to your ESP32. The code now includes:

- Correct Firebase URL formatting
- Error handling and retry logic
- Authentication support if needed
- Automatic reconnection if WiFi connection is lost

Your ESP32 will now send sensor data to both the `/sensor_data` node (current readings) and to `/sensor_history/[timestamp]` nodes (historical data) every 9 seconds.

## 6. Verifying Data Transmission

To verify your ESP32 is sending data correctly:
1. Open the Serial Monitor after uploading the code
2. Check for "Data sent successfully to Firebase" messages
3. Go to your Firebase console and watch the `sensor_data` node update in real-time
4. Verify that new entries are being added to the `sensor_history` node

With this setup, your ESP32 hydroponic monitoring system will reliably send sensor data to Firebase! 