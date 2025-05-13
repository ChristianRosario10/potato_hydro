<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    /**
     * Display the sensor data dashboard
     */
    public function dashboard()
    {
        return view('sensor-data');
    }

    /**
     * Store sensor data from ESP32
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'soil_moisture' => 'nullable|numeric',
            'motion_detected' => 'nullable|boolean',
            'relay_state' => 'nullable|boolean',
        ]);

        $sensorData = SensorData::create($validated);

        return response()->json([
            'success' => true,
            'data' => $sensorData
        ], 201);
    }

    /**
     * Get the latest sensor readings
     */
    public function getLatest()
    {
        $latest = SensorData::latest()->first();
        
        if (!$latest) {
            // Return dummy data if no readings yet
            return response()->json([
                'temperature' => 25.0,
                'humidity' => 60.0,
                'soil_moisture' => 45.0,
                'motion_detected' => false,
                'relay_state' => false,
                'created_at' => now(),
            ]);
        }
        
        return response()->json($latest);
    }
    
    /**
     * Get all sensor readings (paginated)
     */
    public function getAll()
    {
        $readings = SensorData::latest()->paginate(20);
        return response()->json($readings);
    }
    
    /**
     * Display a listing of the sensor data (for API resource)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $readings = SensorData::latest()->paginate(20);
        return response()->json($readings);
    }
}
