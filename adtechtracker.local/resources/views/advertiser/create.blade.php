@extends('layouts.app')

@section('content')
        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="flex flex-col items-center pb-6 pt-4 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                    <p class="font-semibold text-gray-700 dark:text-gray-200">{{ __('offers.new_offer') }}:</p>
                    <div
                        class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">
                        
                        <!-- форма создания оффера -->
                        <form method="POST" action="{{ route('offers.store') }}" id="offer-create">
                            @csrf

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('offers.offer_name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                                    required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- URL -->
                            <div class="mt-4">
                                <x-input-label for="url" :value="__('URL')" />
                                <x-text-input id="url" class="block mt-1 w-full" type="text" name="url" :value="old('url')"
                                    required autocomplete="off" />
                                <x-input-error :messages="$errors->get('url')" class="mt-2" />
                            </div>

                            <!-- Price -->
                            <div class="mt-4">
                                <x-input-label for="price" :value="__('Price')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" min="0" name="price"
                                    :value="old('price')" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Theme -->
                            <div class="mt-4">
                                <x-input-label for="theme" :value="__('offers.offer_theme')" />
                                <x-select-input id="theme" class="block mt-1 w-full" name="theme" required 
                                    :options="$themes->pluck('name', 'id')"
                                    :selected="old('theme')">
                                </x-select-input>
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
                    </div>
                </div>
            </div>
        </section>

    <!-- Записываем данные удаленного оффера в переменную -->
    @php
    $restoreOffer = session('restore_offer');
    @endphp

    <!-- Вызываем модальное окно -->
    @if($restoreOffer)
        <x-modal name="restore-offer" :show="$restoreOffer !== null">
            <div class="p-6"> 

                <h2 class="text-xl font-semibold text-indigo-600 dark:text-gray-200">
                    {{ __('offers.found_deleted_offer') }}
                </h2>

                <!-- выводим данные удаленного оффера -->
                <div class="my-8 py-8 px-6 text-lg text-gray-700 dark:text-gray-300 border rounded-lg">

                    <p class="font-semibold border-b-2 pb-2">{{ __('offers.offer_name') }}: <span class="font-light">{{ $restoreOffer['name'] ?? '' }}</span></p>
                    <p class="font-semibold border-b-2 pb-2">URL: <span class="font-light">{{ $restoreOffer['url'] ?? '' }}</span></p>
                    <p class="font-semibold border-b-2 pb-2">{{ __('offers.offer_price') }}: <span class="font-light">{{ $restoreOffer['price'] ?? '' }}</span></p>
                    <p class="font-semibold border-b-2 pb-2">{{ __('offers.offer_theme') }}: <span class="font-light">{{ $restoreOffer['theme'] ?? '' }}</span></p>
                    <p class="font-semibold border-b-2 pb-2">{{ __('users.delete_date') }}: <span class="font-light">{{ $restoreOffer['deleted_at'] ?? '' }}</span></p>

                </div>

                <!-- Button -->
                <div class="flex items-center justify-center mt-4">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                                
                    <form method="POST" action="{{ route('offers.restore', $restoreOffer['id']) }}">
                        @csrf
                        @method('PATCH')

                        <x-primary-button class="ms-4">
                            {{ __('Restore') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endif
@endsection