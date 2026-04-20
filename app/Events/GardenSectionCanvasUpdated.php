<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GardenSectionCanvasUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $sectionId,
        public array $canvasData,
        public ?string $clientId = null,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new Channel('garden-section.' . $this->sectionId)];
    }

    public function broadcastAs(): string
    {
        return 'canvas.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'section_id' => $this->sectionId,
            'canvas_data' => $this->canvasData,
            'client_id' => $this->clientId,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
