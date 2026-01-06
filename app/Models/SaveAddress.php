<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveAddress extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
