<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Offer;
use App\Models\Commission;

class OfferStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Offer $offer, public string $senderRole) 
    {
        
    }

    /**
     * Get the channels the event should broadcast on.
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // new Channel('offers'),
            new Channel('offers.admin'),
            new Channel('offers.advertiser'),
            new Channel('offers.webmaster'),
        ];
    }

    /**
     * Summary of broadcastAs
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.status.changed';
    }

    /**
     * Summary of broadcastWith
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {
        // вычисляем коммиссию для вебмастера
        $commission = Commission::get('commission')->value('commission');
        $percent = round((100 - $commission) / 100, 2);

        // передаем данные для отрисовки карточки у вебмастера и перемещения существующей у админа
        return [
            'id' => $this->offer->id,
            'name' => $this->offer->name,
            'url' => $this->offer->url,
            'price' => round($this->offer->price * $percent, 2),
            'theme' => $this->offer->theme->name,
            'advertiser' => $this->offer->advertiser->name,
            'status' => $this->offer->status,
            'sender_role' => $this->senderRole,
            'subscribe' => $this->offer->subscribe()->count(),
        ];
    }
}
