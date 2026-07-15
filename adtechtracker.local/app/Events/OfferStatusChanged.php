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

/**
 * Summary of OfferStatusChanged
 */
class OfferStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Получаем данные
     */
    public function __construct(
        public Offer $offer, 
        public string $senderRole
        ) 
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
        ];
    }

    /**
     * Событие
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.status.changed';
    }

    /**
     * Передаем данные по событию
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {

        // передаем данные для перемещения существующего оффера у админа или рекламщика
        return [
            'id' => $this->offer->id,
            'status' => $this->offer->status,
            'sender_role' => $this->senderRole,
        ];
    }
}
