@extends('layouts.app')

@section('js')
    <script src="/js/DragItem.js" defer></script>
    <script src="/js/Status.js" defer></script>
    <script src="/js/OfferStatusListener.js" defer></script>
    <script src="/js/OfferDeleteListener.js" defer></script>
    <script src="/js/OfferSubscribeListener.js" defer></script>
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
            <div class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Button -->
                <section class="flex items-center justify-center mt-4">
                    <x-primary-button class="w-56" onclick="window.location='{{ route('themes.index') }}'">
                        {{ __('Темы офферов') }}
                    </x-primary-button>
                    <x-primary-button class="ms-4 w-56" onclick="window.location='{{ route('offers.create') }}'">
                        {{ __('Создать оффер') }}
                    </x-primary-button>
                </section>

                <x-section-offer :offers="$offers" :role="auth()->user()->role">

                </x-section-offer>     

            </div>
        </div>
    </section>
@endsection