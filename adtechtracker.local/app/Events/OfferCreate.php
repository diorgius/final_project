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
 * Summary of OfferCreate
 */
class OfferCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Получаем данные.
     */
    public function __construct(public Offer $offer)
    {

    }

    /**
     * Создаем канал для прослушивания
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('offers.admin'),
        ];
    }

    /**
     * Событие
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.create';
    }

    /**
     * Передаем данные по событию
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {
        // передаем данные для отрисовки карточки у админа
        return [
            'id' => $this->offer->id,
            'name' => $this->offer->name,
            'url' => $this->offer->url,
            'price' => $this->offer->price,
            'theme' => $this->offer->theme->name,
            'advertiser' => $this->offer->advertiser->name,
            'status' => $this->offer->status,
        ];
    }
}
