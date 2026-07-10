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

/**
 * Summary of OfferDelete
 */
class OfferDelete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Получаем данные
     */
    public function __construct(public int $offerId)
    {
        
    }

    /**
     * Создаем каналы для прослушивания
     * @return PrivateChannel[]
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('offers.admin'),
            new PrivateChannel('offers.advertiser'),
            new PrivateChannel('offers.webmaster'),
        ];
    }

    /**
     * Событие
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.delete';
    }

    /**
     * Передаем данные по событию
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->offerId,
        ];
    }
}
