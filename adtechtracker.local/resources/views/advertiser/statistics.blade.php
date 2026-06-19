@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <x-section-period>

                </x-section-period>

                <div class="flex flex-col items-center pt-5 pb-1">
                    <p class="font-semibold text-gray-700">Статистика за все время на {{ now()->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}:</p>
                </div>
                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                                    dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <table class="text-gray-600 table-auto w-full text-center align-center">
                    <tr class="border-b border-gray-200 text-sm uppercase"><th class="py-4">Офферы</th><th class="py-4">Переходы<th class="py-4">Расходы</th></tr>
                    @foreach ($offers as $offer)
                        <tr class="border-b border-gray-200 text-xl"><td class="py-2">{{ $offer->name }}</td><td class="py-2">{{ $offer->click_count }}</td><td class="py-2">{{ number_format($offer->advertiser_expenses, 2 ) }}</td></tr>
                    @endforeach
                    <tr class="border-b border-gray-200 text-sm uppercase"><td class="py-2">Итого</td><td class="py-2">{{ $totalClicks }}</td><td class="py-2">{{ number_format($totalExpenses, 2 ) }}</td></tr>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection