<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Server extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name', 'api_token', 'ip_address', 'status', 'is_enabled',
        'check_type', 'ssh_user', 'ssh_password', 'last_alerted_at',
        'last_sync_details', 'user_id'
    ];

    protected $hidden = [
        'ssh_password',
    ];

    protected $casts = [
        'last_sync_details' => 'array',
        'last_alerted_at' => 'datetime',
        'is_enabled' => 'boolean',
        'ssh_password' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($server) {
            if (empty($server->api_token)) {
                $server->api_token = \Str::random(32);
            }
        });
    }

    /**
     * Find user via API token (Sanctum compatibility)
     */
    public function findForPassport($token)
    {
        return $this->where('api_token', $token)->first();
    }
}