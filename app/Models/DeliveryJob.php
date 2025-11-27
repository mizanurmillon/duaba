<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryJob extends Model
{
    protected $fillable = [
        'user_id',
        'pickup_address',
        'sender_name',
        'sender_phone',
        'dropoff_address',
        'receiver_name',
        'receiver_phone',
        'house_number',
        'house_name',
        'delivery_instructions',
        'package_height',
        'package_width',
        'package_depth',
        'package_weight',
        'package_type',
        'schedule_type',
        'schedule_date',
        'schedule_time',
        'stuart_job_id',
        'stuart_response',
    ];

    protected $casts = [
        'stuart_response' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
