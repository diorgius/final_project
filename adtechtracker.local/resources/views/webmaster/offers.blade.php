@extends('layouts.app')

@section('js')
    <script src="/js/Subscription.js" defer></script>
    <script src="/js/OfferStatusListener.js" defer></script>
    <script src="/js/OfferDeleteListener.js" defer></script>
    <script>
        window.userRole = '{{ auth()->user()->role }}';
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/offers.css">
@endsection

@section('content')
    <section class="py-12 relative z-49">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <section class="flex justify-between text-center w-5/6 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]
                            dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <!-- Подписки -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 dark:text-gray-200 mb-4">Подписки</h4>
                        <div class="offers subscriptions h-full">
                            @foreach ($subscriptions as $subscription)
                                @if ($subscription->offer->status === 1)
                                    <div id="{{ $subscription->offer->id }}" class="offers__item active-offers__item" draggable="true">
                                        <p class="font-semibold">Рекламодатель: <span class="font-light">{{ $subscription->offer->advertiser->name }}</span></p>
                                        <p class="font-semibold">Наименование: <span class="font-light">{{ $subscription->offer->name }}</span></p>
                                        <p class="font-semibold">Тема: <span class="font-light">{{ $subscription->offer->theme->name }}</span></p>
                                        <p class="font-semibold">Цена: <span class="font-light">{{ number_format($subscription->offer->price * $percent, 2) }} р. за переход</span></p>
                                        <a href="/r/{{ $subscription->ref_code }}" class="offer-url font-semibold text-xl text-blue-600" title={{ $subscription->offer->url }} target="_blank">Реферальная ссылка</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Доступные активные офферы -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 dark:text-gray-200 mb-4">Доступные офферы</h4>
                        <div class="offers deactive-offers h-full">
                            @foreach ($offers as $offer)
                                <div id="{{ $offer->id }}" class="offers__item deactive-offers__item" draggable="true">
                                    <p class="font-semibold">Рекламодатель: <span class="font-light">{{ $offer->advertiser->name }}</span></p>
                                    <p class="font-semibold">Наименование: <span class="font-light">{{ $offer->name }}</span></p>
                                    <p class="font-semibold">Тема: <span class="font-light">{{ $offer->theme->name }}</span></p>
                                    <p class="font-semibold">Цена: <span class="font-light">{{ number_format($offer->price * $percent, 2) }} р. за переход</span></p>
                                    <a href="#" class="offer-url hidden__item font-semibold text-xl text-blue-600" title={{ $offer->url }} target="_blank">Реферальная ссылка</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </section>
@endsection