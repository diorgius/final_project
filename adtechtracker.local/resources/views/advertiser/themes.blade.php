@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-4 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ __('offers.new_theme') }}:</h3>

                <!-- форма создания темы -->
                <form method="POST" action="{{ route('themes.store') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('offers.offer_theme_name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                            autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <div class="flex items-center justify-center mt-4">
                        <x-link-button :href="route('advertiser.offers')">
                            {{ __('Cancel') }}
                        </x-link-button>

                        <x-primary-button class="ms-4">
                            {{ __('Save') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- выводим темы -->
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mt-8">{{ __('offers.current_themes') }}:</h3>
                <div class="flex justify-center items-center mt-4" >
                    <ol class="list-decimal">    
                        @foreach ($themes as $theme)
                            <li class="border-b-2 text-xl text-gray-600 dark:text-gray-200">{{ $theme->name }}</li>
                        @endforeach
                    </ol>    
                </div>    
            </div>
        </div>
    </section>
@endsection 