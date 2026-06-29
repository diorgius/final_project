<p class="font-semibold text-gray-700 dark:text-gray-200">{{ __('statistics.period') }}:
<p>
<div class="w-auto mt-6 px-6 py-4 bg-white text-gray-700 dark:text-gray-200 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">

    <div class='grid grid-cols-4 gap-4 justify-around items-center w-full'>
        <button data-period="day" class="period-btn text-center rounded-lg border px-4 py-2 uppercase">{{ __('statistics.day') }}</button>
        <button data-period="month" class="period-btn text-center rounded-lg border px-4 py-2 uppercase">{{ __('statistics.month') }}</button>
        <button data-period="year" class="period-btn text-center rounded-lg border px-4 py-2 uppercase">{{ __('statistics.year') }}</button>
        <button data-period="all" class="period-btn text-center rounded-lg border px-4 py-2 uppercase bg-indigo-600 text-white border-indigo-600">{{ __('statistics.alltime') }}</button>
    </div>

</div>
