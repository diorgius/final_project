@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <p class="font-semibold text-gray-700">Установить коммиссию системы (%):<p>
                <div
                    class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                            dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <form method="POST" action="{{ route('commission.update', $commissions[0]->id) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Commission -->
                        <div>
                            <x-input-label for="name" :value="__('Коммиссия')" />
                            <x-text-input id="commission" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100"
                                name="commission" value="{{ old('commission', $commissions[0]->commission) }}" required
                                autofocus autocomplete="off" />
                        </div>

                        <!-- Button -->
                        <div class="flex items-center justify-center mt-4">
                            <x-reset-button>
                                {{ __('Отменить') }}
                            </x-reset-button>
                            <x-primary-button class="ms-4">
                                {{ __('Сохранить') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                <div class="flex flex-col items-center pt-5 pb-1">
                    </div>
                    <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                    dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">
                    
                    <p class="font-semibold text-gray-700 text-xl pb-2">Прибыль системы: <span class="text-indigo-600">{{ $systemProfit }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Расходы рекламодателей: <span class="text-indigo-600">{{ $advertiserExpenses }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl">Доходы вебмастеров: <span class="text-indigo-600">{{ $webmasterIncome }}</span></p>

                </div>

            </div>
        </div>
    </section>
@endsection