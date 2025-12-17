<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'deliver_job_id' => 'integer',
        'amount' => 'float',
        'status' => 'string',
        'payment_method' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryJob()
    {
        return $this->belongsTo(DeliveryJob::class, 'deliver_job_id');
    }
}
