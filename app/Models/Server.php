<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Server extends Model
{
    use HasApiTokens;

    protected $fillable = ['name', 'api_token', 'ip_address', 'status', 'is_enabled', 'check_type', 'ssh_user', 'ssh_password', 'last_alerted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }
}