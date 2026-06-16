@extends('layouts.app')

@section('js')
    <script src="/js/status/Status.js" defer></script>
    <script src="/js/status/OfferStatusListener.js" defer></script>
    <script src="/js/status/OfferDeleteListener.js" defer></script>
    <script src="/js/status/OfferSubscribeListener.js" defer></script>
    <script>
        window.userRole = '{{ auth()->user()->role }}';
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/offers.css">
@endsection

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Button -->
                <section class="flex items-center justify-center mt-4">
                    <x-primary-button class="w-56" onclick="window.location='{{ route('themes.index') }}'">
                        {{ __('Темы офферов') }}
                    </x-primary-button>
                    <x-primary-button class="ms-4 w-56" onclick="window.location='{{ route('offers.create') }}'">
                        {{ __('Создать оффер') }}
                    </x-primary-button>
                </section>

                <section class="flex justify-between text-center w-5/6 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]
                        dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">
                    <!-- активные офферы -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Активные офферы</h4>
                        <div class="offers active-offers h-full">
                            @foreach ($offers as $offer)
                                @if ($offer->status === 1)
                                    <div id="{{ $offer->id }}"
                                        class="offers__item active-offers__item" draggable="true">
                                        <form method="POST" action="{{ route("advertiser.offers.destroy", $offer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex items-center justify-center mt-4">
                                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                                            </div>
                                        </form>
                                        <p class="font-semibold">Наименование: {{ $offer->name }}</p>
                                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                                        <p>Цена: {{ $offer->price }} р. за переход</p>
                                        <p class="font-semibold">Подписчиков: {{ $offer->subscribe->count() }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- неактивные офферы -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Неактивные офферы</h4>
                        <div class="offers deactive-offers h-full">
                            @foreach ($offers as $offer)
                                @if ($offer->status === 0)
                                    <div id="{{ $offer->id }}"
                                        class="offers__item deactive-offers__item" draggable="true">
                                        <form method="POST" action="{{ route("advertiser.offers.destroy", $offer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex items-center justify-center mt-4">
                                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                                            </div>
                                        </form>
                                        <p class="font-semibold">Наименование: {{ $offer->name }}</p>
                                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                                        <p>Цена: {{ number_format($offer->price, 2) }} р. за переход</p>
                                        <p class="font-semibold">Подписчиков: {{ $offer->subscribe->count() }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection