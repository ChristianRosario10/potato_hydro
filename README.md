# Potato Hydro Monitoring System

A real-time monitoring system for hydroponic potato cultivation using Firebase Realtime Database.

## Features

- Real-time sensor data display
- Historical data visualization using Chart.js
- Responsive design using Bootstrap
- Firebase Realtime Database integration

## Setup

1. Clone this repository
2. Open `index.html` in a web browser
3. The system will automatically connect to the Firebase Realtime Database

## Firebase Structure

The system expects the following Firebase Realtime Database structure:

```json
{
  "sensor_data": {
    // Real-time sensor readings
  },
  "sensor_history": {
    // Historical sensor data with timestamps
  }
}
```

## Security

Firebase security rules are configured to allow read/write access to sensor data and history nodes.

## Live Dashboard

The live dashboard is accessible at:
- **[https://christianrosario10.github.io/potato/](https://christianrosario10.github.io/potato/)**

## Technologies Used

- ESP32 microcontroller
- DHT11 temperature and humidity sensor
- Soil moisture sensor
- PIR motion sensor
- Relay module
- Firebase Realtime Database
- HTML/CSS/JavaScript
- TailwindCSS

## Setup Instructions

### Hardware Setup

Connect the sensors to your ESP32 according to the following pin configuration:
- DHT11: Pin 26
- Relay: Pin 18
- PIR: Pin 27
- Soil Moisture: Pin 32

### Software Setup

1. Upload the ino.file to your ESP32 using the Arduino IDE
2. Configure your WiFi credentials in the code
3. Host the index.html file (or deploy it to a web server)
4. Enjoy your hydroponic monitoring system!

### GitHub Pages Deployment

This dashboard is automatically deployed to GitHub Pages:

1. Repository Settings → Pages should show:
   - Source: Deploy from a branch
   - Branch: gh-pages

2. Any pushes to the main branch automatically update the gh-pages branch via GitHub Actions
3. The site will be available at: **[https://christianrosario10.github.io/potato/](https://christianrosario10.github.io/potato/)**

## Troubleshooting GitHub Pages

If the GitHub Pages site is not updating:

1. Go to your repository on GitHub
2. Click on "Actions" tab to see if workflows are running successfully
3. Check repository Settings → Pages to verify the correct branch (gh-pages) is selected
4. You may need to manually trigger the workflow by going to Actions → "Deploy to GitHub Pages" → "Run workflow"

## Team Members

- Christian Rosario
- Oliver Narvaza
- Hener Lorenzana
- Shekeina Karylle Dabalos

## License

MIT
