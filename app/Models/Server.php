<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Server extends Model
{
    use HasApiTokens;

    // Esta línea es la que soluciona el error:
    protected $fillable = ['name', 'api_token', 'ip_address', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }
}