<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Models\Commission;

class OfferStatusForWebmasterChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Offer $offer,
        public User $webmaster,
        public ?OfferSubscription $subscription
        )
    {

    }

    /**
     * Создаем приватный канал для прослушивания
     * @return PrivateChannel[]
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                "offers.webmaster.{$this->webmaster->id}"
            )
        ];
    }

    /**
     * Событие
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'offer.status.for.webmaster.changed';
    }

    /**
     * Передаем данные по событию
     * @return array{id: mixed, status: mixed, title: mixed}
     */
    public function broadcastWith(): array
    {
        // вычисляем коммиссию для вебмастера
        $commission = Commission::get('commission')->value('commission');
        $percent = round((100 - $commission) / 100, 2);

        // передаем данные для отрисовки карточки у вебмастера
        return [
            'offer' => [
                'id' => $this->offer->id,
                'name' => $this->offer->name,
                'url' => $this->offer->url,
                'price' => round($this->offer->price * $percent, 2),
                'theme' => $this->offer->theme->name,
                'status' => $this->offer->status,
                'advertiser' => $this->offer->advertiser->name,
                'subscribed' =>
                    $this->subscription &&
                    !$this->subscription->trashed(),
                'ref_code' =>
                    $this->subscription?->ref_code,
            ],
        ];
    }
}
