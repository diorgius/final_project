@extends('layouts.app')

@section('js')
    @vite('resources/js/advertiser.js')
    <script>
        // передаем в js роль пользователя
        window.userRole = '{{ auth()->user()->role }}';
    </script>
@endsection

@section('content')
    <section class="py-12 relative z-49">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">

                <!-- Button -->
                <section class="flex items-center justify-center mt-4">
                    <x-link-button class="w-56" :href="route('themes.index')">
                        {{ __('offers.offer_themes') }}
                    </x-link-button>

                    <x-link-button class="ms-4 w-56" :href="route('offers.create')">
                        {{ __('Create') }}
                    </x-link-button>
                </section>

                <!-- выводим офферы -->
                <x-section-offer :offers="$offers" :role="auth()->user()->role">

                </x-section-offer>     

            </div>
        </div>
    </section>
@endsection