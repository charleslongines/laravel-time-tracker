<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTracker extends Model
{
    protected $table = 'time_tracker';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
