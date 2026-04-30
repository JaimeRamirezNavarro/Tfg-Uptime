<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    // Desactivamos la protección de campos para permitir guardar server_id, disk_free, etc.
    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Relación con el servidor
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}