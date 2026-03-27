<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    public function server() {
        return $this->belongsTo(Server::class); // Una métrica pertenece a un servidor 
    }
    protected $fillable = ['cpu_load', 'ram_usage', 'disk_free'];
}
