@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="/css/admin.css">
@endsection

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <x-section-period>

                </x-section-period>

                <div class="flex flex-col items-center pt-5 pb-1">
                    <p>Статистика:</p>
                </div>
                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                        dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <p class="font-semibold text-gray-700 text-xl pb-2">Количество рекламодателей: <span
                            class="text-indigo-600">{{ $advertisers }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Количество вебмастеров: <span
                            class="text-indigo-600">{{ $webmasters }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Количество офферов: <span
                            class="text-indigo-600">{{ $offers }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Количество подписок: <span
                            class="text-indigo-600">{{ $subscriptions }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Количество переходов: <span
                            class="text-indigo-600">{{ $clicks }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Доходы системы: <span
                            class="text-indigo-600">{{ $systemProfit }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-2">Расходы рекламодателей: <span
                            class="text-indigo-600">{{ $advertiserExpenses }}</span></p>
                    <p class="font-semibold text-gray-700 text-xl pb-1">Доходы вебмастеров: <span
                            class="text-indigo-600">{{ $webmasterIncome }}</span></p>

                </div>
            </section>
        </div>
    </section>
@endsection