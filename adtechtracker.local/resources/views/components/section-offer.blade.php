@props(['offers'])
@props(['role'])

<section class="flex justify-between text-center w-5/6 mt-6 px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">

    <!-- активные офферы -->
    <div class="w-1/2 inline-block m-0 p-3">
        <h4 class="font-semibold text-xl mx-auto text-gray-600 dark:text-gray-200 mb-4">{{ __('offers.active_offers') }}</h4>
        <div class="offers active-offers h-full">
            @foreach ($offers as $offer)
                @if ($offer->status === 1)
                    <div id="{{ $offer->id }}" class="offers__item offer-item offer-active" draggable="true">

                        <x-div-offer :role="$role" :offer="$offer">

                        </x-div-offer>

                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- неактивные офферы -->
    <div class="w-1/2 inline-block m-0 p-3">
        <h4 class="font-semibold text-xl mx-auto text-gray-600 dark:text-gray-200 mb-4">{{ __('offers.inactive_offers') }}</h4>
        <div class="offers deactive-offers h-full">
            @foreach ($offers as $offer)
                @if ($offer->status === 0)
                    <div id="{{ $offer->id }}" class="offers__item offer-item offer-deactive" draggable="true">
                    
                        <x-div-offer :role="$role" :offer="$offer">

                        </x-div-offer>

                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>