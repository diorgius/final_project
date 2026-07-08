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
 * Summary of OfferSubscribeChanged
 */
class OfferSubscribeChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Получаем данные
     */
    public function __construct(
        public Offer $offer,
        public int $webmasterId,
        public string $action)
    {

    }

    /**
     * Создаем каналы
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('offers.admin'),
            new Channel('offers.advertiser'),
        ];
    }

    
    /**
     * Событие
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.subscribe.changed';
    }

    /**
     * Передаем данные
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {
        // передаем данные для отображения количества подписчиков у рекламщика и админа
        return [
            'offer_id' => $this->offer->id,
            'action' => $this->action,
            'webmaster_id' => $this->webmasterId,
            'subscribe_count' => $this->offer
                ->subscribe()
                ->count(),
        ];
    }
}
