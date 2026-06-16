@extends('layouts.app')

@section('js')
    <script src="/js/status/Status.js" defer></script>
    <script src="/js/status/OfferStatusListener.js" defer></script>
    <script src="/js/status/OfferCreateListener.js" defer></script>
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
            <div class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                
                <x-section-offer :offers="$offers" :role="auth()->user()->role">
                    
                </x-section-offer>                     

            </div>
        </div>
    </section>
@endsection