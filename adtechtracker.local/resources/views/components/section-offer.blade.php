@props(['offers'])
@props(['role'])

<section class="flex justify-between text-center w-5/6 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]
                        dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

    <!-- активные офферы -->
    <div class="w-1/2 inline-block m-0 p-3">
        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Активные офферы</h4>
        <div class="offers active-offers h-full">
            @foreach ($offers as $offer)
                @if ($offer->status === 1)
                    <div id="{{ $offer->id }}" class="offers__item active-offers__item" draggable="true">
                        <form method="POST" action="{{ route("$role.offers.destroy", $offer->id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-center justify-center mt-4">
                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                            </div>
                        </form>
                        @if ($role === 'admin') <p class="font-semibold">Рекламодатель: {{ $offer->advertiser->name }}</p> @endif
                        <p class="font-semibold">Наименование: {{ $offer->name }}</p>
                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                        <p>Цена: {{ $offer->price }} р. за переход</p>
                        <p class="subscribers font-semibold">Подписчиков: {{ $offer->subscribe->count() }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- неактивные офферы -->
    <div class="w-1/2 inline-block m-0 p-3">
        <h4 class="font-semibold text-xl mx-auto text-gray-600 mb-4">Неактивные офферы</h4>
        <div class="offers deactive-offers h-full">
            @foreach ($offers as $offer)
                @if ($offer->status === 0)
                    <div id="{{ $offer->id }}" class="offers__item deactive-offers__item" draggable="true">
                        <form method="POST" action="{{ route("$role.offers.destroy", $offer->id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-center justify-center mt-4">
                                <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                            </div>
                        </form>
                        @if ($role === 'admin') <p class="font-semibold">Рекламодатель: {{ $offer->advertiser->name }}</p> @endif
                        <p class="font-semibold">Наименование: {{ $offer->name }}</p>
                        <p class="font-semibold">Тема: {{ $offer->theme->name }}</p>
                        <p class="font-semibold">URL: {{ $offer->url }}</p>
                        <p>Цена: {{ number_format($offer->price, 2) }} р. за переход</p>
                        <p class="subscribers font-semibold">Подписчиков: {{ $offer->subscribe->count() }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>