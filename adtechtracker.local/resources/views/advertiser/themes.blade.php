@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('themes.store') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Название темы')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                            autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <div class="flex items-center justify-center mt-4">
                        <x-reset-button onclick="window.location='{{ route('advertiser.offers') }}'">
                            {{ __('Отменить') }}
                        </x-reset-button>
                        <x-primary-button class="ms-4">
                            {{ __('Создать тему') }}
                        </x-primary-button>
                    </div>
                </form>

                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mt-8">Текущие темы:</h3>
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