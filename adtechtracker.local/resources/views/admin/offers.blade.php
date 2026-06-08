@extends('layouts.app')
<!-- 
@section('header')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-indigo-600 dark:text-gray-200 leading-tight">
            {{ __('Панель рекламодателя') }}
        </h2>
    </div>
@endsection -->

@section('js')
    <script src="/js/status/Status.js" defer></script>
@endsection

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <section class="flex justify-between text-center w-5/6 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]
                        dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <!-- активные офферы -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Активные офферы</h4>
                        <div class="h-full" id="active-offers">
                            @foreach ($offers as $offer)
                                @if ($offer->status === 1)
                                    <div id="{{ $offer->id }}"
                                        class="p-2 relative mb-1 rounded cursor-pointer bg-indigo-100 text-indigo-600 offers__item active-offers__item"
                                        draggable="true">
                                        <!-- <form method="POST" action="{{ route("offers.destroy", $offer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex items-center justify-center mt-4">
                                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                                            </div>
                                        </form> -->
                                        <p class="font-semibold">{{ $offer->name }}</p>
                                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                                        <p>Цена: {{ number_format($offer->price, 2) }} р. за переход</p>
                                        {{-- <p class="table-offers__td-link-count">Подписчиков: {{ $offer->links->count() }} </p> --}}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- неактивные офферы -->
                    <div class="w-1/2 inline-block m-0 p-3">
                        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Неактивные офферы</h4>
                        <div class="h-full" id="deactive-offers">
                            @foreach ($offers as $offer)
                                @if ($offer->status === 0)
                                    <div id="{{ $offer->id }}"
                                        class="p-2 relative mb-1 rounded-lg cursor-pointer bg-gray-100 text-gray-700 offers__item deactive-offers__item"
                                        draggable="true">
                                        <form method="POST" action="{{ route("offers.destroy", $offer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex items-center justify-center mt-4">
                                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                                            </div>
                                        </form>
                                        <p class="font-semibold">{{ $offer->name }}</p>
                                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                                        <p>Цена: {{ number_format($offer->price, 2) }} р. за переход</p>
                                        {{-- <p class="table-offers__td-link-count">Подписчиков: {{ $offer->links->count() }} </p> --}}
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