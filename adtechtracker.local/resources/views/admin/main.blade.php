@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('main.set_commission') }}:</h3>
                <div
                    class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">

                    <form method="POST" action="{{ route('commission.update', $commissions[0]->id) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Commission -->
                        <div>
                            <x-input-label for="name" :value="__('main.commission')" />
                            <x-text-input id="commission" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100"
                                name="commission" value="{{ old('commission', $commissions[0]->commission) }}" required
                                autofocus autocomplete="off" />
                        </div>

                        <!-- Button -->
                        <div class="flex items-center justify-center mt-4">
                            <x-secondary-button type="reset">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button class="ms-4">
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="flex flex-col items-center pt-5 pb-1">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-xl">{{ __('main.summary') }}:</h3>
                </div>

                <!-- Выводим общие данные -->
                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">
                    <p class="border-b-2 font-semibold text-gray-700 dark:text-gray-300 text-xl pb-2">{{ __('statistics.advertiser_expenses') }} - <span class="text-indigo-600">{{ $advertiserExpenses }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 dark:text-gray-300 text-xl pb-1">{{ __('statistics.webmasters_income') }} - <span class="text-indigo-600">{{ $webmasterIncome }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 dark:text-gray-300 text-xl pb-2">{{ __('statistics.revenue_system') }} - <span class="text-indigo-600">{{ $systemProfit }}</span></p>
                </div>
            </div>
        </div>
    </section>
@endsection