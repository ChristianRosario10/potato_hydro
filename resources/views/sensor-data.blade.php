<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potato Hydroponic System</title>
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 47% 33%, hsl(162, 77%, 40%, 0.15) 0, transparent 59%), 
                radial-gradient(at 82% 65%, hsl(218, 39%, 11%, 0.15) 0, transparent 55%);
        }
        .code-font {
            font-family: 'Roboto Mono', monospace;
        }
        .card {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(17, 25, 40, 0.75);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.125);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.3);
        }
        .sensor-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .sidebar-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .sidebar-item:hover, .sidebar-item.active {
            border-left-color: #10b981;
            background-color: rgba(16, 185, 129, 0.1);
        }
        .history-card {
            transition: all 0.2s ease;
        }
        .history-card:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        /* Animated gauge meter */
        .gauge-container {
            position: relative;
            width: 200px;
            height: 100px;
            margin: 0 auto;
            overflow: hidden;
        }
        .gauge {
            width: 100%;
            height: 200px;
            border-radius: 100px 100px 0 0;
            border: 10px solid #334155;
            border-bottom: none;
            position: absolute;
            top: 0;
            overflow: hidden;
        }
        .gauge-fill {
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, #10b981, #3b82f6);
            border-radius: 100px 100px 0 0;
            transform-origin: center bottom;
            transform: rotate(0.5turn);
            transition: transform 1s ease-out;
        }
        .gauge-center {
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: #1e293b;
            border-radius: 50%;
            border: 5px solid #334155;
            z-index: 10;
        }
        .gauge-value {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-900 text-white dark">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-72 bg-gray-800 bg-opacity-70 backdrop-blur-xl shadow-xl fixed h-full z-10 transition-all duration-300 transform" id="sidebar">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="bg-emerald-500 p-2 rounded-lg shadow-lg">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Potato Hydroponics</h1>
                </div>
                <p class="mt-2 text-xs text-gray-400">Real-time monitoring dashboard</p>
            </div>
            
            <!-- Team Members Dropdown -->
            <div class="p-4 border-b border-gray-700">
                <label for="profile-select" class="block mb-2 text-sm font-semibold text-gray-300">
                    <i class="fas fa-users mr-2"></i>Team Members
                </label>
                <select id="profile-select" class="w-full p-2 bg-gray-700 bg-opacity-50 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Select a team member</option>
                    <option value="christian">Christian Rosario</option>
                    <option value="oliver">Oliver Narvaza</option>
                    <option value="hener">Hener Lorenzana</option>
                    <option value="shekeina">Shekeina Karylle Dabalos</option>
                </select>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-4 p-4 space-y-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sensors</p>
                <button onclick="showSection('temperature')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-temperature-high text-blue-400"></i>
                    <span>Temperature</span>
                </button>
                <button onclick="showSection('humidity')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-water text-green-400"></i>
                    <span>Humidity</span>
                </button>
                <button onclick="showSection('soil_moisture')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-tint text-purple-400"></i>
                    <span>Soil Moisture</span>
                </button>
                <button onclick="showSection('motion_detected')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-running text-orange-400"></i>
                    <span>Motion Detector</span>
                </button>
                <button onclick="showSection('relay_state')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-plug text-red-400"></i>
                    <span>Relay State</span>
                </button>
                
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Analytics</p>
                <button onclick="showSection('history')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-chart-line text-indigo-400"></i>
                    <span>Reading History</span>
                </button>
                <button onclick="showSection('share')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg flex items-center space-x-3 text-gray-300 hover:text-white">
                    <i class="fas fa-share-alt text-yellow-400"></i>
                    <span>Share Dashboard</span>
                </button>
                
                <div class="pt-6 mt-6 border-t border-gray-700">
                    <div class="px-4 py-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">System Status</div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-300">Online</span>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-72 flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-white" data-aos="fade-right">ESP32 Sensor Dashboard</h2>
                        <p class="text-gray-400 flex items-center" data-aos="fade-right" data-aos-delay="100">
                            <i class="fas fa-sync-alt mr-2 animate-spin-slow"></i>
                            Last Updated: <span id="timestamp" class="ml-2 code-font">--</span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar" class="p-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </header>

                <!-- Individual Sections -->
                <section id="temperature" class="sensor-section hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Reading Card -->
                        <div class="card p-6" data-aos="fade-up">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-temperature-high text-blue-400 mr-2"></i>
                                    Temperature
                                </h2>
                                <span class="rounded-full bg-blue-500 bg-opacity-20 text-blue-300 text-xs px-3 py-1">
                                    Live
                                </span>
                            </div>
                            
                            <!-- Gauge Meter -->
                            <div class="gauge-container my-6">
                                <div class="gauge">
                                    <div class="gauge-fill" id="temperature-gauge"></div>
                                </div>
                                <div class="gauge-center"></div>
                                <div class="gauge-value">
                                    <span id="temperature-value" class="code-font">N/A°C</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm text-gray-400 code-font mt-4">
                                <span>0°C</span>
                                <span>25°C</span>
                                <span>50°C</span>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">Status</p>
                                    <p class="text-white font-semibold" id="temperature-status">Normal</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-400">Change</p>
                                    <p class="flex items-center font-semibold">
                                        <span id="temperature-arrow" class="mr-1">-</span>
                                        <span id="temperature-change">0°C</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart Card -->
                        <div class="card p-6 md:col-span-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-chart-line text-blue-400 mr-2"></i>
                                    Temperature History
                                </h2>
                                <select id="temperature-time-range" class="bg-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1 focus:outline-none">
                                    <option value="1h">Last Hour</option>
                                    <option value="24h" selected>Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                </select>
                            </div>
                            
                            <div class="h-64">
                                <canvas id="temperature-chart"></canvas>
                            </div>
                            
                            <!-- Recent Readings -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-400 mb-3">Recent Readings</h3>
                                <div class="max-h-40 overflow-y-auto" id="temperature-history-list">
                                    <p class="text-gray-500 text-sm">Loading history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="humidity" class="sensor-section hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Reading Card -->
                        <div class="card p-6" data-aos="fade-up">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-water text-green-400 mr-2"></i>
                                    Humidity
                                </h2>
                                <span class="rounded-full bg-green-500 bg-opacity-20 text-green-300 text-xs px-3 py-1">
                                    Live
                                </span>
                            </div>
                            
                            <!-- Gauge Meter -->
                            <div class="gauge-container my-6">
                                <div class="gauge">
                                    <div class="gauge-fill" id="humidity-gauge" style="background: linear-gradient(to top, #10b981, #3b82f6);"></div>
                                </div>
                                <div class="gauge-center"></div>
                                <div class="gauge-value">
                                    <span id="humidity-value" class="code-font">N/A%</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm text-gray-400 code-font mt-4">
                                <span>0%</span>
                                <span>50%</span>
                                <span>100%</span>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">Status</p>
                                    <p class="text-white font-semibold" id="humidity-status">Normal</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-400">Change</p>
                                    <p class="flex items-center font-semibold">
                                        <span id="humidity-arrow" class="mr-1">-</span>
                                        <span id="humidity-change">0%</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart Card -->
                        <div class="card p-6 md:col-span-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-chart-line text-green-400 mr-2"></i>
                                    Humidity History
                                </h2>
                                <select id="humidity-time-range" class="bg-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1 focus:outline-none">
                                    <option value="1h">Last Hour</option>
                                    <option value="24h" selected>Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                </select>
                            </div>
                            
                            <div class="h-64">
                                <canvas id="humidity-chart"></canvas>
                            </div>
                            
                            <!-- Recent Readings -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-400 mb-3">Recent Readings</h3>
                                <div class="max-h-40 overflow-y-auto" id="humidity-history-list">
                                    <p class="text-gray-500 text-sm">Loading history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="soil_moisture" class="sensor-section hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Reading Card -->
                        <div class="card p-6" data-aos="fade-up">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-tint text-purple-400 mr-2"></i>
                                    Soil Moisture
                                </h2>
                                <span class="rounded-full bg-purple-500 bg-opacity-20 text-purple-300 text-xs px-3 py-1">
                                    Live
                                </span>
                            </div>
                            
                            <!-- Gauge Meter -->
                            <div class="gauge-container my-6">
                                <div class="gauge">
                                    <div class="gauge-fill" id="soil_moisture-gauge" style="background: linear-gradient(to top, #8b5cf6, #ec4899);"></div>
                                </div>
                                <div class="gauge-center"></div>
                                <div class="gauge-value">
                                    <span id="soil_moisture-value" class="code-font">N/A%</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm text-gray-400 code-font mt-4">
                                <span>0%</span>
                                <span>50%</span>
                                <span>100%</span>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">Status</p>
                                    <p class="text-white font-semibold" id="soil_moisture-status">Normal</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-400">Change</p>
                                    <p class="flex items-center font-semibold">
                                        <span id="soil_moisture-arrow" class="mr-1">-</span>
                                        <span id="soil_moisture-change">0%</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart Card -->
                        <div class="card p-6 md:col-span-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-chart-line text-purple-400 mr-2"></i>
                                    Soil Moisture History
                                </h2>
                                <select id="soil_moisture-time-range" class="bg-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1 focus:outline-none">
                                    <option value="1h">Last Hour</option>
                                    <option value="24h" selected>Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                </select>
                            </div>
                            
                            <div class="h-64">
                                <canvas id="soil_moisture-chart"></canvas>
                            </div>
                            
                            <!-- Recent Readings -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-400 mb-3">Recent Readings</h3>
                                <div class="max-h-40 overflow-y-auto" id="soil_moisture-history-list">
                                    <p class="text-gray-500 text-sm">Loading history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="motion_detected" class="sensor-section hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Reading Card -->
                        <div class="card p-6" data-aos="fade-up">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-running text-orange-400 mr-2"></i>
                                    Motion Detector
                                </h2>
                                <span class="rounded-full bg-orange-500 bg-opacity-20 text-orange-300 text-xs px-3 py-1">
                                    Live
                                </span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center py-8">
                                <div id="motion-indicator" class="w-32 h-32 rounded-full flex items-center justify-center mb-6 border-4 border-gray-700">
                                    <i id="motion-icon" class="fas fa-user-slash text-gray-500 text-4xl"></i>
                                </div>
                                <p class="text-2xl font-bold code-font" id="motion_detected-value">None</p>
                                <p class="text-sm text-gray-400 mt-2" id="motion-status">No motion detected</p>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">Last Triggered</p>
                                    <p class="text-white font-semibold" id="motion-last-time">Never</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-400">State</p>
                                    <p class="flex items-center font-semibold">
                                        <span id="motion_detected-arrow" class="mr-1">-</span>
                                        <span id="motion-state">Inactive</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- History Card -->
                        <div class="card p-6 md:col-span-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-history text-orange-400 mr-2"></i>
                                    Motion History
                                </h2>
                                <select id="motion-time-range" class="bg-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1 focus:outline-none">
                                    <option value="1h">Last Hour</option>
                                    <option value="24h" selected>Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                </select>
                            </div>
                            
                            <div class="h-64">
                                <canvas id="motion-chart"></canvas>
                            </div>
                            
                            <!-- Activity Log -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-400 mb-3">Activity Log</h3>
                                <div class="max-h-40 overflow-y-auto" id="motion-history-list">
                                    <p class="text-gray-500 text-sm">Loading history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="relay_state" class="sensor-section hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Reading Card -->
                        <div class="card p-6" data-aos="fade-up">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-plug text-red-400 mr-2"></i>
                                    Relay State
                                </h2>
                                <span class="rounded-full bg-red-500 bg-opacity-20 text-red-300 text-xs px-3 py-1">
                                    Live
                                </span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center py-8">
                                <div class="relative">
                                    <div id="relay-switch" class="w-24 h-12 rounded-full bg-gray-700 p-1 transition-all duration-300 transform">
                                        <div id="relay-slider" class="w-10 h-10 bg-gray-500 rounded-full shadow-md transform transition-all duration-300"></div>
                                    </div>
                                    <div class="absolute -left-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-400">OFF</div>
                                    <div class="absolute -right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-400">ON</div>
                                </div>
                                <p class="text-2xl font-bold code-font mt-6" id="relay_state-value">OFF</p>
                                <p class="text-sm text-gray-400 mt-2" id="relay-status">System is not active</p>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">Active Time</p>
                                    <p class="text-white font-semibold" id="relay-active-time">0h 0m</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-400">State</p>
                                    <p class="flex items-center font-semibold">
                                        <span id="relay_state-arrow" class="mr-1">-</span>
                                        <span id="relay-state">Inactive</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- History Card -->
                        <div class="card p-6 md:col-span-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold text-white">
                                    <i class="fas fa-history text-red-400 mr-2"></i>
                                    Relay History
                                </h2>
                                <select id="relay-time-range" class="bg-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1 focus:outline-none">
                                    <option value="1h">Last Hour</option>
                                    <option value="24h" selected>Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                </select>
                            </div>
                            
                            <div class="h-64">
                                <canvas id="relay-chart"></canvas>
                            </div>
                            
                            <!-- Activity Log -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-400 mb-3">Activity Log</h3>
                                <div class="max-h-40 overflow-y-auto" id="relay-history-list">
                                    <p class="text-gray-500 text-sm">Loading history...</p>
                                </div>
                            </div>
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
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Initialize AOS animations
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Toggle sidebar functionality
            const sidebarToggle = document.getElementById('toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('main');
            
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    mainContent.classList.toggle('ml-0');
                    mainContent.classList.toggle('ml-72');
                });
            }
        });

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyD51VjKsCK-HpeDno6cVqrbPBeshynIOic",
            authDomain: "potato-hydro.firebaseapp.com",
            databaseURL: "https://potato-hydro-default-rtdb.firebaseio.com",
            projectId: "potato-hydro",
            storageBucket: "potato-hydro.firebasestorage.app",
            messagingSenderId: "569203450870",
            appId: "1:569203450870:web:86b80d4d45843253d45dd7",
            measurementId: "G-TJ2L4QJS24"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const database = firebase.database();

        const sections = ['temperature', 'humidity', 'soil_moisture', 'motion_detected', 'relay_state', 'share'];
        
        // Chart objects
        const charts = {};
        
        // Keep track of historical data
        const historicalData = {
            temperature: [],
            humidity: [],
            soil_moisture: [],
            motion_detected: [],
            relay_state: []
        };

        function showSection(id) {
            // Add active class to sidebar item
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Find the clicked sidebar item and add active class
            const clickedButton = Array.from(document.querySelectorAll('.sidebar-item')).find(
                button => button.textContent.toLowerCase().includes(id.replace('_', ' '))
            );
            if (clickedButton) {
                clickedButton.classList.add('active');
            }
            
            // Hide all sections first
            sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
            document.getElementById('profile-display').classList.add('hidden');
            document.getElementById('history').classList.add('hidden');

            // Show the clicked section
            if (id === 'profile-display') {
                document.getElementById('profile-display').classList.remove('hidden');
            } else if (id === 'history') {
                document.getElementById('history').classList.remove('hidden');
                renderHistory();
            } else if (id === 'share') {
                document.getElementById('share').classList.remove('hidden');
                generateShareUrl();
            } else {
                // For sensor sections, show only the clicked one
                document.getElementById(id).classList.remove('hidden');
                
                // Initialize or update chart if needed
                initializeOrUpdateChart(id);
            }
        }

        // Initialize or update chart for a sensor
        function initializeOrUpdateChart(sensorId) {
            const canvasId = `${sensorId}-chart`;
            const canvas = document.getElementById(canvasId);
            
            if (!canvas) return;
            
            if (!charts[sensorId]) {
                // Initialize new chart
                const ctx = canvas.getContext('2d');
                
                let chartConfig = {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: sensorId.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()),
                            data: [],
                            borderColor: getChartColor(sensorId),
                            backgroundColor: getChartColor(sensorId, 0.2),
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: 'rgba(255, 255, 255, 0.8)'
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(17, 25, 40, 0.9)',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                boxPadding: 6
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: 'rgba(255, 255, 255, 0.6)',
                                    maxRotation: 0,
                                    maxTicksLimit: 8
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: 'rgba(255, 255, 255, 0.6)'
                                }
                            }
                        }
                    }
                };
                
                // For boolean values (motion_detected, relay_state), use stepped chart
                if (sensorId === 'motion_detected' || sensorId === 'relay_state') {
                    chartConfig.data.datasets[0].stepped = true;
                    chartConfig.options.scales.y = {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.6)',
                            callback: function(value) {
                                return value === 0 ? 'Inactive' : 'Active';
                            }
                        },
                        min: 0,
                        max: 1
                    };
                }
                
                charts[sensorId] = new Chart(ctx, chartConfig);
            } else {
                // Update existing chart
                const chart = charts[sensorId];
                
                // Update data with historical values
                const data = historicalData[sensorId];
                chart.data.labels = data.map(item => {
                    const date = new Date(item.timestamp);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                });
                chart.data.datasets[0].data = data.map(item => item.value);
                
                chart.update();
            }
            
            // Update history list
            updateHistoryList(sensorId);
        }
        
        // Get chart color based on sensor type
        function getChartColor(sensorId, alpha = 1) {
            const colors = {
                'temperature': `rgba(59, 130, 246, ${alpha})`, // blue
                'humidity': `rgba(16, 185, 129, ${alpha})`, // green
                'soil_moisture': `rgba(139, 92, 246, ${alpha})`, // purple
                'motion_detected': `rgba(249, 115, 22, ${alpha})`, // orange
                'relay_state': `rgba(239, 68, 68, ${alpha})` // red
            };
            
            return colors[sensorId] || `rgba(75, 85, 99, ${alpha})`;
        }
        
        // Update history list for a specific sensor
        function updateHistoryList(sensorId) {
            const listElement = document.getElementById(`${sensorId}-history-list`);
            if (!listElement) return;
            
            const data = historicalData[sensorId];
            if (data.length === 0) {
                listElement.innerHTML = '<p class="text-gray-500 text-sm">No history available yet.</p>';
                return;
            }
            
            listElement.innerHTML = '';
            
            // Take the last 10 readings
            const recentData = data.slice(-10).reverse();
            
            recentData.forEach(item => {
                const date = new Date(item.timestamp);
                const timeString = date.toLocaleTimeString([], { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                });
                const dateString = date.toLocaleDateString([], {
                    month: 'short',
                    day: 'numeric'
                });
                
                const div = document.createElement('div');
                div.className = 'history-card p-2 rounded mb-2 flex justify-between items-center';
                
                let valueDisplay = '';
                if (sensorId === 'temperature') {
                    valueDisplay = `${item.value}°C`;
                } else if (sensorId === 'humidity' || sensorId === 'soil_moisture') {
                    valueDisplay = `${item.value}%`;
                } else if (sensorId === 'motion_detected') {
                    valueDisplay = item.value ? 'Detected' : 'None';
                } else if (sensorId === 'relay_state') {
                    valueDisplay = item.value ? 'ON' : 'OFF';
                }
                
                div.innerHTML = `
                    <div>
                        <span class="text-sm text-white">${valueDisplay}</span>
                        <span class="text-xs text-gray-400 block">${dateString}, ${timeString}</span>
                    </div>
                    <div>
                        <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(sensorId, item.value)}">
                            ${getStatusText(sensorId, item.value)}
                        </span>
                    </div>
                `;
                
                listElement.appendChild(div);
            });
        }
        
        // Get status class based on sensor value
        function getStatusClass(sensorId, value) {
            if (sensorId === 'temperature') {
                if (value < 10) return 'bg-blue-500 bg-opacity-20 text-blue-300';
                if (value > 35) return 'bg-red-500 bg-opacity-20 text-red-300';
                return 'bg-green-500 bg-opacity-20 text-green-300';
            } else if (sensorId === 'humidity') {
                if (value < 30) return 'bg-red-500 bg-opacity-20 text-red-300';
                if (value > 80) return 'bg-blue-500 bg-opacity-20 text-blue-300';
                return 'bg-green-500 bg-opacity-20 text-green-300';
            } else if (sensorId === 'soil_moisture') {
                if (value < 20) return 'bg-red-500 bg-opacity-20 text-red-300';
                if (value > 80) return 'bg-blue-500 bg-opacity-20 text-blue-300';
                return 'bg-green-500 bg-opacity-20 text-green-300';
            } else if (sensorId === 'motion_detected') {
                return value ? 'bg-orange-500 bg-opacity-20 text-orange-300' : 'bg-gray-500 bg-opacity-20 text-gray-300';
            } else if (sensorId === 'relay_state') {
                return value ? 'bg-green-500 bg-opacity-20 text-green-300' : 'bg-gray-500 bg-opacity-20 text-gray-300';
            }
            
            return 'bg-gray-500 bg-opacity-20 text-gray-300';
        }
        
        // Get status text based on sensor value
        function getStatusText(sensorId, value) {
            if (sensorId === 'temperature') {
                if (value < 10) return 'Cold';
                if (value > 35) return 'Hot';
                return 'Normal';
            } else if (sensorId === 'humidity') {
                if (value < 30) return 'Dry';
                if (value > 80) return 'Humid';
                return 'Normal';
            } else if (sensorId === 'soil_moisture') {
                if (value < 20) return 'Dry';
                if (value > 80) return 'Wet';
                return 'Normal';
            } else if (sensorId === 'motion_detected') {
                return value ? 'Active' : 'Inactive';
            } else if (sensorId === 'relay_state') {
                return value ? 'Active' : 'Inactive';
            }
            
            return 'Normal';
        }

        // Check for shared view mode
        function checkSharedMode() {
            const urlParams = new URLSearchParams(window.location.search);
            const shared = urlParams.get('shared');
            
            if (shared) {
                // Remove sidebar in shared mode
                document.querySelector('aside').style.display = 'none';
                document.querySelector('main').classList.remove('ml-72');
                document.querySelector('main').classList.add('ml-0');
                
                // Parse sensors to show
                const sensors = shared.split(',');
                
                // Hide all sections first
                sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
                
                // Show only the selected sensors
                sensors.forEach(sensor => {
                    if (sections.includes(sensor)) {
                        document.getElementById(sensor).classList.remove('hidden');
                        initializeOrUpdateChart(sensor);
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
                img: "img/chan.png"
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
                img: "img/she.png"
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

        // Set up Firebase real-time listener for sensor data
        function setupSensorDataListener() {
            const sensorRef = database.ref('sensor_data');
            
            // Listen for real-time updates
            sensorRef.on('value', (snapshot) => {
                const data = snapshot.val();
                if (data) {
                    // Update temperature
                    const currentTemp = parseFloat(data.temperature);
                    document.getElementById('temperature-value').textContent = isNaN(currentTemp) ? 'N/A°C' : `${currentTemp}°C`;
                    updateArrow('temperature-arrow', currentTemp, previousValues.temperature);
                    document.getElementById('temperature-change').textContent = previousValues.temperature ? `${(currentTemp - previousValues.temperature).toFixed(1)}°C` : '0°C';
                    previousValues.temperature = currentTemp;
                    
                    // Update temperature gauge and status
                    updateGauge('temperature', currentTemp, 0, 50);
                    document.getElementById('temperature-status').textContent = getStatusText('temperature', currentTemp);
                    
                    // Add to historical data
                    historicalData.temperature.push({
                        timestamp: new Date().toISOString(),
                        value: currentTemp
                    });
                    if (historicalData.temperature.length > 100) {
                        historicalData.temperature.shift(); // Keep only the last 100 readings
                    }

                    // Update humidity
                    const currentHumidity = parseFloat(data.humidity);
                    document.getElementById('humidity-value').textContent = isNaN(currentHumidity) ? 'N/A%' : `${currentHumidity}%`;
                    updateArrow('humidity-arrow', currentHumidity, previousValues.humidity);
                    document.getElementById('humidity-change').textContent = previousValues.humidity ? `${(currentHumidity - previousValues.humidity).toFixed(1)}%` : '0%';
                    previousValues.humidity = currentHumidity;
                    
                    // Update humidity gauge and status
                    updateGauge('humidity', currentHumidity, 0, 100);
                    document.getElementById('humidity-status').textContent = getStatusText('humidity', currentHumidity);
                    
                    // Add to historical data
                    historicalData.humidity.push({
                        timestamp: new Date().toISOString(),
                        value: currentHumidity
                    });
                    if (historicalData.humidity.length > 100) {
                        historicalData.humidity.shift();
                    }

                    // Update soil moisture
                    const currentSoilMoisture = parseFloat(data.soil_moisture);
                    document.getElementById('soil_moisture-value').textContent = isNaN(currentSoilMoisture) ? 'N/A%' : `${currentSoilMoisture}%`;
                    updateArrow('soil_moisture-arrow', currentSoilMoisture, previousValues.soil_moisture);
                    document.getElementById('soil_moisture-change').textContent = previousValues.soil_moisture ? `${(currentSoilMoisture - previousValues.soil_moisture).toFixed(1)}%` : '0%';
                    previousValues.soil_moisture = currentSoilMoisture;
                    
                    // Update soil moisture gauge and status
                    updateGauge('soil_moisture', currentSoilMoisture, 0, 100);
                    document.getElementById('soil_moisture-status').textContent = getStatusText('soil_moisture', currentSoilMoisture);
                    
                    // Add to historical data
                    historicalData.soil_moisture.push({
                        timestamp: new Date().toISOString(),
                        value: currentSoilMoisture
                    });
                    if (historicalData.soil_moisture.length > 100) {
                        historicalData.soil_moisture.shift();
                    }

                    // Update motion detection
                    const currentMotion = data.motion_detected;
                    document.getElementById('motion_detected-value').textContent = currentMotion ? 'Detected' : 'None';
                    updateArrow('motion_detected-arrow', currentMotion ? 1 : 0, previousValues.motion_detected ? 1 : 0);
                    document.getElementById('motion-state').textContent = currentMotion ? 'Active' : 'Inactive';
                    document.getElementById('motion-status').textContent = currentMotion ? 'Motion detected' : 'No motion detected';
                    previousValues.motion_detected = currentMotion;
                    
                    // Update motion indicator
                    const motionIndicator = document.getElementById('motion-indicator');
                    const motionIcon = document.getElementById('motion-icon');
                    
                    if (currentMotion) {
                        motionIndicator.classList.add('border-orange-500');
                        motionIndicator.classList.remove('border-gray-700');
                        motionIcon.classList.add('text-orange-400');
                        motionIcon.classList.remove('text-gray-500');
                        motionIcon.classList.remove('fa-user-slash');
                        motionIcon.classList.add('fa-user');
                        
                        // Update last triggered time
                        document.getElementById('motion-last-time').textContent = new Date().toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        motionIndicator.classList.remove('border-orange-500');
                        motionIndicator.classList.add('border-gray-700');
                        motionIcon.classList.remove('text-orange-400');
                        motionIcon.classList.add('text-gray-500');
                        motionIcon.classList.add('fa-user-slash');
                        motionIcon.classList.remove('fa-user');
                    }
                    
                    // Add to historical data
                    historicalData.motion_detected.push({
                        timestamp: new Date().toISOString(),
                        value: currentMotion ? 1 : 0
                    });
                    if (historicalData.motion_detected.length > 100) {
                        historicalData.motion_detected.shift();
                    }

                    // Update relay state
                    const currentRelay = data.relay_state;
                    document.getElementById('relay_state-value').textContent = currentRelay ? 'ON' : 'OFF';
                    updateArrow('relay_state-arrow', currentRelay ? 1 : 0, previousValues.relay_state ? 1 : 0);
                    document.getElementById('relay-state').textContent = currentRelay ? 'Active' : 'Inactive';
                    document.getElementById('relay-status').textContent = currentRelay ? 'System is active' : 'System is not active';
                    previousValues.relay_state = currentRelay;
                    
                    // Update relay switch
                    const relaySwitch = document.getElementById('relay-switch');
                    const relaySlider = document.getElementById('relay-slider');
                    
                    if (currentRelay) {
                        relaySwitch.classList.add('bg-red-500', 'bg-opacity-50');
                        relaySwitch.classList.remove('bg-gray-700');
                        relaySlider.classList.add('bg-red-500', 'translate-x-12');
                        relaySlider.classList.remove('bg-gray-500', 'translate-x-0');
                    } else {
                        relaySwitch.classList.remove('bg-red-500', 'bg-opacity-50');
                        relaySwitch.classList.add('bg-gray-700');
                        relaySlider.classList.remove('bg-red-500', 'translate-x-12');
                        relaySlider.classList.add('bg-gray-500', 'translate-x-0');
                    }
                    
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

                    // Log data for history
                    logSensorData(data);

                    // Find which sensor section is currently visible and update its chart
                    const visibleSensor = ['temperature', 'humidity', 'soil_moisture', 'motion_detected', 'relay_state'].find(
                        sensor => !document.getElementById(sensor).classList.contains('hidden')
                    );
                    
                    if (visibleSensor) {
                        initializeOrUpdateChart(visibleSensor);
                    }

                    // Update history view if it's currently visible
                    if (!document.getElementById('history').classList.contains('hidden')) {
                        renderHistory();
                    }
                }
            });
        }
        
        // Update gauge based on sensor value
        function updateGauge(sensorId, value, min, max) {
            const gaugeElement = document.getElementById(`${sensorId}-gauge`);
            if (!gaugeElement) return;
            
            // Calculate percentage (0-1)
            const percentage = Math.min(Math.max((value - min) / (max - min), 0), 1);
            
            // Calculate rotation (0.5 turn is empty, 0 turn is full)
            const rotation = 0.5 - (percentage * 0.5);
            
            // Apply rotation
            gaugeElement.style.transform = `rotate(${rotation}turn)`;
        }

        // Initialize
        window.onload = () => {
            checkSharedMode();
            setupSensorDataListener(); // Start listening for Firebase updates
            
            // Initialize AOS for animations
            AOS.init();
        };
    </script>
</body>
</html>