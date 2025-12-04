<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firewall extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'url',
        'api_key',
        'api_secret',
        'description',
        'is_dirty',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
