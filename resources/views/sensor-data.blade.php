<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onion Hydroponic System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 text-white dark">
    <!-- Sidebar -->
    <div class="flex h-screen">
        <aside class="w-64 bg-gray-800 shadow-lg fixed h-full z-10">
            <div class="p-6 text-center border-b border-gray-700">
                <h1 class="text-xl font-bold">🥔 Potato Hydroponics</h1>
                <!-- About Us Dropdown -->
                <label for="profile-select" class="block mt-4 text-sm font-semibold">Team Members:</label>
                <select id="profile-select" class="w-full p-2 mt-2 bg-gray-700 text-white rounded focus:outline-none text-sm">
                    <option value="">Select</option>
                    <option value="christian">Christian Rosario</option>
                    <option value="oliver">Oliver Narvaza</option>
                    <option value="hener">Hener Lorenzana</option>
                    <option value="shekeina">Shekeina Karylle Dabalos</option>
                </select>
            </div>
            <nav class="mt-6 p-4 space-y-2">
                <button onclick="showSection('temperature')" class="w-full text-left px-4 py-2 rounded hover:bg-blue-600 transition-colors bg-blue-500 text-white">🌡️ Temperature</button>
                <button onclick="showSection('humidity')" class="w-full text-left px-4 py-2 rounded hover:bg-green-600 transition-colors bg-green-500 text-white">💧 Humidity</button>
                <button onclick="showSection('soil_moisture')" class="w-full text-left px-4 py-2 rounded hover:bg-purple-600 transition-colors bg-purple-500 text-white">🌱 Soil Moisture</button>
                <button onclick="showSection('motion_detected')" class="w-full text-left px-4 py-2 rounded hover:bg-orange-600 transition-colors bg-orange-500 text-white">🔴 Motion Detector</button>
                <button onclick="showSection('relay_state')" class="w-full text-left px-4 py-2 rounded hover:bg-red-600 transition-colors bg-red-500 text-white">🔌 Relay State</button>
                <button onclick="showSection('history')" class="w-full text-left px-4 py-2 rounded hover:bg-indigo-600 transition-colors bg-indigo-500 text-white">📊 Reading History</button>
                <button onclick="showSection('share')" class="w-full text-left px-4 py-2 rounded hover:bg-yellow-600 transition-colors bg-yellow-500 text-white">🔗 Share Dashboard</button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 overflow-auto p-8">
            <header class="mb-8">
                <h2 class="text-3xl font-bold mb-2">ESP32 Sensor Readings</h2>
                <p class="text-gray-400" id="reading-timestamp">Last Updated: <span id="timestamp">--</span></p>
            </header>

            <!-- Individual Sections -->
            <section id="temperature" class="sensor-section hidden">
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-semibold mb-4">🌡️ Temperature</h2>
                    <div class="flex items-center justify-center">
                        <p class="text-4xl font-bold text-blue-400" id="temperature-value">N/A°C</p>
                        <span class="ml-2 text-2xl" id="temperature-arrow">-</span>
                    </div>
                </div>
            </section>

            <section id="humidity" class="sensor-section hidden">
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-semibold mb-4">💧 Humidity</h2>
                    <div class="flex items-center justify-center">
                        <p class="text-4xl font-bold text-green-400" id="humidity-value">N/A%</p>
                        <span class="ml-2 text-2xl" id="humidity-arrow">-</span>
                    </div>
                </div>
            </section>

            <section id="soil_moisture" class="sensor-section hidden">
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-semibold mb-4">🌱 Soil Moisture</h2>
                    <div class="flex items-center justify-center">
                        <p class="text-4xl font-bold text-purple-400" id="soil_moisture-value">N/A%</p>
                        <span class="ml-2 text-2xl" id="soil_moisture-arrow">-</span>
                    </div>
                </div>
            </section>

            <section id="motion_detected" class="sensor-section hidden">
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-semibold mb-4">🔴 Motion Detector</h2>
                    <div class="flex items-center justify-center">
                        <p class="text-4xl font-bold text-orange-400" id="motion_detected-value">None</p>
                        <span class="ml-2 text-2xl" id="motion_detected-arrow">-</span>
                    </div>
                </div>
            </section>

            <section id="relay_state" class="sensor-section hidden">
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-semibold mb-4">🔌 Relay State</h2>
                    <div class="flex items-center justify-center">
                        <p class="text-4xl font-bold text-red-400" id="relay_state-value">OFF</p>
                        <span class="ml-2 text-2xl" id="relay_state-arrow">-</span>
                    </div>
                </div>
            </section>

            <!-- Profile Display Section -->
            <section id="profile-display" class="hidden bg-gray-800 rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold mb-4">👤 Team Member Profile</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <img id="profile-img" src="" alt="Profile Picture" class="rounded-lg w-40 h-40 object-cover mx-auto">
                    <div>
                        <h3 class="text-xl font-semibold mb-2" id="profile-name">Name</h3>
                        <p><strong>Year & Section:</strong> <span id="profile-year"></span></p>
                        <p><strong>Address:</strong> <span id="profile-address"></span></p>
                        <p><strong>Motto:</strong> <span id="profile-motto"></span></p>
                        <p class="mt-2"><strong>Bio:</strong> <span id="profile-bio"></span></p>
                    </div>
                </div>
            </section>

            <!-- Reading History Section -->
            <section id="history" class="hidden bg-gray-800 rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold mb-4">📊 Sensor Reading History</h2>
                <input type="text" id="date-filter" placeholder="Search by date or time..." class="w-full p-3 bg-gray-700 text-white rounded mb-4" />
                <div id="history-list" class="space-y-4 max-h-96 overflow-y-auto">
                    <p class="text-gray-400">No history available yet.</p>
                </div>
            </section>

            <!-- Share Dashboard Section -->
            <section id="share" class="hidden bg-gray-800 rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold mb-4">🔗 Share Your Dashboard</h2>
                <div class="mb-6">
                    <p class="mb-4">Share your live sensor readings with others using this unique link:</p>
                    <div class="flex items-center">
                        <input type="text" id="share-url" class="w-full p-3 bg-gray-700 text-white rounded-l" readonly />
                        <button onclick="copyShareUrl()" class="bg-blue-500 hover:bg-blue-600 p-3 rounded-r">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </button>
                    </div>
                    <p id="copy-message" class="mt-2 text-green-400 hidden">Link copied to clipboard!</p>
                </div>
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Create Custom Share Link</h3>
                    <p class="mb-4">Choose which sensors to include in your shared link:</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" class="share-option mr-2" value="temperature" checked>
                            🌡️ Temperature
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="share-option mr-2" value="humidity" checked>
                            💧 Humidity
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="share-option mr-2" value="soil_moisture" checked>
                            🌱 Soil Moisture
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="share-option mr-2" value="motion_detected" checked>
                            🔴 Motion Detector
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="share-option mr-2" value="relay_state" checked>
                            🔌 Relay State
                        </label>
                    </div>
                    <button onclick="generateShareUrl()" class="mt-4 bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded">
                        Generate Share Link
                    </button>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-2">About Sharing</h3>
                    <p>Your shared link will show real-time sensor data for the selected sensors. Anyone with the link can view the data, but only you can control the system.</p>
                </div>
            </section>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        const sections = ['temperature', 'humidity', 'soil_moisture', 'motion_detected', 'relay_state', 'share'];

        function showSection(id) {
            sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
            document.getElementById('profile-display').classList.add('hidden');
            document.getElementById('history').classList.add('hidden');

            if (id === 'profile-display') {
                document.getElementById('profile-display').classList.remove('hidden');
            } else if (id === 'history') {
                document.getElementById('history').classList.remove('hidden');
                renderHistory();
            } else {
                document.getElementById(id).classList.remove('hidden');
                if (id === 'share') {
                    generateShareUrl();
                }
            }
        }

        // Check for shared view mode
        function checkSharedMode() {
            const urlParams = new URLSearchParams(window.location.search);
            const shared = urlParams.get('shared');
            
            if (shared) {
                // Remove sidebar in shared mode
                document.querySelector('aside').style.display = 'none';
                document.querySelector('main').classList.remove('ml-64');
                
                // Parse sensors to show
                const sensors = shared.split(',');
                
                // Hide all sections first
                sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
                
                // Show only the selected sensors
                sensors.forEach(sensor => {
                    if (sections.includes(sensor)) {
                        document.getElementById(sensor).classList.remove('hidden');
                    }
                });
                
                // Show header always
                document.querySelector('header').style.display = 'block';
            } else {
                // Default view - show temperature
                showSection('temperature');
            }
        }

        window.onload = checkSharedMode;

        // Share URL generation
        function generateShareUrl() {
            const selectedSensors = Array.from(document.querySelectorAll('.share-option:checked'))
                .map(checkbox => checkbox.value);
            
            if (selectedSensors.length === 0) {
                alert('Please select at least one sensor to share.');
                return;
            }
            
            const shareParam = selectedSensors.join(',');
            const shareUrl = `${window.location.origin}${window.location.pathname}?shared=${shareParam}`;
            
            document.getElementById('share-url').value = shareUrl;
        }

        function copyShareUrl() {
            const shareUrlInput = document.getElementById('share-url');
            shareUrlInput.select();
            document.execCommand('copy');
            
            const copyMessage = document.getElementById('copy-message');
            copyMessage.classList.remove('hidden');
            
            setTimeout(() => {
                copyMessage.classList.add('hidden');
            }, 3000);
        }

        // Profile Data
        const profiles = {
            christian: {
                name: "Christian Rosario",
                year: "BSIT 3A",
                address: "San Juan, Sta Lucia",
                motto: "Stay humble, keep learning.",
                bio: "I enjoy working on IoT projects and developing embedded systems.",
                img: "https://via.placeholder.com/160"
            },
            oliver: {
                name: "Oliver Narvaza",
                year: "BSIT 3A",
                address: "Minglanilla, Cebu",
                motto: "Code is poetry.",
                bio: "Passionate about full-stack development and automation.",
                img: "https://via.placeholder.com/160"
            },
            hener: {
                name: "Hener Lorenzana",
                year: "BSIT 3A",
                address: "Talisay City, Cebu",
                motto: "Make it work, make it fast, make it scalable.",
                bio: "Tech enthusiast focused on AI and machine learning.",
                img: "https://via.placeholder.com/160"
            },
            shekeina: {
                name: "Shekeina Karylle Dabalos",
                year: "BSIT 3A",
                address: "Carcar City, Cebu",
                motto: "Success is not final, failure is not fatal: It is the courage to continue that counts.",
                bio: "Loves designing UI/UX and creating user-centered experiences.",
                img: "https://via.placeholder.com/160"
            }
        };

        // Handle profile selection
        document.getElementById('profile-select').addEventListener('change', function () {
            const selected = this.value;
            const profile = profiles[selected];
            if (profile) {
                document.getElementById('profile-img').src = profile.img;
                document.getElementById('profile-name').textContent = profile.name;
                document.getElementById('profile-year').textContent = profile.year;
                document.getElementById('profile-address').textContent = profile.address;
                document.getElementById('profile-motto').textContent = profile.motto;
                document.getElementById('profile-bio').textContent = profile.bio;

                sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
                document.getElementById('profile-display').classList.remove('hidden');
            }
        });

        // Sensor reading logic
        let previousValues = {
            temperature: null,
            humidity: null,
            soil_moisture: null,
            motion_detected: null,
            relay_state: null
        };

        // Arrow update logic
        function updateArrow(elementId, currentValue, previousValue) {
            const arrowElement = document.getElementById(elementId);
            if (previousValue === null) {
                arrowElement.textContent = '-';
                arrowElement.className = 'ml-2 text-2xl text-gray-400';
                return;
            }

            if (currentValue > previousValue) {
                arrowElement.textContent = '↑';
                arrowElement.className = 'ml-2 text-2xl text-green-500';
            } else if (currentValue < previousValue) {
                arrowElement.textContent = '↓';
                arrowElement.className = 'ml-2 text-2xl text-red-500';
            } else {
                arrowElement.textContent = '-';
                arrowElement.className = 'ml-2 text-2xl text-gray-400';
            }
        }

        // Store up to 20 latest readings
        const historyLog = [];

        function logSensorData(data) {
            const entry = {
                timestamp: new Date().toLocaleString(),
                temperature: data.temperature ?? 'N/A',
                humidity: data.humidity ?? 'N/A',
                soil_moisture: data.soil_moisture ?? 'N/A',
                motion_detected: data.motion_detected ? 'Detected' : 'None',
                relay_state: data.relay_state ? 'ON' : 'OFF'
            };
            historyLog.unshift(entry);
            if (historyLog.length > 20) historyLog.pop();
        }

        function renderHistory() {
            const container = document.getElementById('history-list');
            container.innerHTML = '';

            if (historyLog.length === 0) {
                container.innerHTML = '<p class="text-gray-400">No history available.</p>';
                return;
            }

            historyLog.forEach((entry, index) => {
                const div = document.createElement('div');
                div.className = 'bg-gray-700 p-4 rounded-md';
                div.innerHTML = `
                    <p class="text-sm text-gray-400">${entry.timestamp}</p>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mt-2">
                        <div><strong>🌡️ Temp:</strong> ${entry.temperature}°C</div>
                        <div><strong>💧 Humidity:</strong> ${entry.humidity}%</div>
                        <div><strong>🌱 Moisture:</strong> ${entry.soil_moisture}%</div>
                        <div><strong>🔴 Motion:</strong> ${entry.motion_detected}</div>
                        <div><strong>🔌 Relay:</strong> ${entry.relay_state}</div>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        // Search functionality
        document.getElementById('date-filter').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const items = document.querySelectorAll('#history-list > div');
            items.forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(query) ? 'block' : 'none';
            });
        });

        // Fetch and update sensor values
        function updateSensorData() {
            fetch('/api/sensor-data')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Update temperature
                        const currentTemp = parseFloat(data.temperature);
                        document.getElementById('temperature-value').textContent = isNaN(currentTemp) ? 'N/A°C' : `${currentTemp}°C`;
                        updateArrow('temperature-arrow', currentTemp, previousValues.temperature);
                        previousValues.temperature = currentTemp;

                        // Update humidity
                        const currentHumidity = parseFloat(data.humidity);
                        document.getElementById('humidity-value').textContent = isNaN(currentHumidity) ? 'N/A%' : `${currentHumidity}%`;
                        updateArrow('humidity-arrow', currentHumidity, previousValues.humidity);
                        previousValues.humidity = currentHumidity;

                        // Update soil moisture
                        const currentSoilMoisture = parseFloat(data.soil_moisture);
                        document.getElementById('soil_moisture-value').textContent = isNaN(currentSoilMoisture) ? 'N/A%' : `${currentSoilMoisture}%`;
                        updateArrow('soil_moisture-arrow', currentSoilMoisture, previousValues.soil_moisture);
                        previousValues.soil_moisture = currentSoilMoisture;

                        // Update motion detection
                        const currentMotion = data.motion_detected;
                        document.getElementById('motion_detected-value').textContent = currentMotion ? 'Detected' : 'None';
                        updateArrow('motion_detected-arrow', currentMotion ? 1 : 0, previousValues.motion_detected ? 1 : 0);
                        previousValues.motion_detected = currentMotion;

                        // Update relay state
                        const currentRelay = data.relay_state;
                        document.getElementById('relay_state-value').textContent = currentRelay ? 'ON' : 'OFF';
                        updateArrow('relay_state-arrow', currentRelay ? 1 : 0, previousValues.relay_state ? 1 : 0);
                        previousValues.relay_state = currentRelay;

                        // Update timestamp
                        document.getElementById('timestamp').textContent = new Date().toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            second: 'numeric',
                            hour12: true
                        });

                        // Log data
                        logSensorData(data);
                    }
                })
                .catch(error => console.error('Error fetching sensor data:', error));
        }

        setInterval(updateSensorData, 5000);
        updateSensorData();
    </script>
</body>
</html>