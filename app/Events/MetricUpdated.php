<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $server;
    public $metric;

    /**
     * Create a new event instance.
     */
    public function __construct($server, $metric)
    {
        $this->server = $server;
        $this->metric = $metric;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('server.' . $this->server->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'cpu' => $this->metric->cpu_load,
            'ram' => $this->metric->ram_usage,
            'disk' => $this->metric->disk_free,
            'details' => json_decode($this->server->last_sync_details, true),
            'time' => $this->metric->created_at->format('H:i:s'),
        ];
    }
}
