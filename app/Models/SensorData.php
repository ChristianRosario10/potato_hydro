<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'temperature',
        'humidity',
        'soil_moisture',
        'motion_detected',
        'relay_state'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'float',
        'soil_moisture' => 'float',
        'motion_detected' => 'boolean',
        'relay_state' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
