<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_version',
        'available_version',
        'status',
        'log',
        'last_error',
        'requested_by',
        'requested_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'log' => 'array', // If we store JSON logs
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
