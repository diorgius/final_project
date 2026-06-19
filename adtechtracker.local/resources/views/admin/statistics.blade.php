@extends('layouts.app')

@section('js')
    <script src="/js/Statistics.js" defer></script>
    <script>
        window.userRole = '{{ auth()->user()->role }}';
    </script>
@endsection

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <x-section-period>

                </x-section-period>

                <x-div-date>
                
                </x-div-date>

                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white dark:bg-gray-800 
                                shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                                dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество
                        рекламодателей - <span id="advertisers" class="text-indigo-600">{{ $advertisers }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество вебмастеров -
                        <span id="webmasters" class="text-indigo-600">{{ $webmasters }}</span>
                    </p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество офферов - <span id="offers"
                            class="text-indigo-600">{{ $offers }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество подписок на
                        офферы - <span id="subscriptions" class="text-indigo-600">{{ $subscriptions }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество отписок от
                        офферов - <span id="unsubscriptions" class="text-indigo-600">{{ $unsubscriptions }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество переходов по
                        ссылкам - <span id="clicks" class="text-indigo-600">{{ $clicks }}</span></p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Количество отклоненных
                        переходов по ссылкам - <span id="rejected-clicks"
                            class="text-indigo-600">{{ $rejectedClicks }}</span>
                    </p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Расходы рекламодателей -
                        <span id="advertiser-expenses" class="text-indigo-600">{{ $advertiserExpenses }}</span>
                    </p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-1">Доходы вебмастеров -
                        <span id="webmaster-income" class="text-indigo-600">{{ $webmasterIncome }}</span>
                    </p>
                    <p class="border-b-2 font-semibold text-gray-700 text-xl pb-2">Доходы системы - <span id="system-profit"
                            class="text-indigo-600">{{ $systemProfit }}</span></p>

                </div>
            </section>
        </div>
    </section>
@endsection