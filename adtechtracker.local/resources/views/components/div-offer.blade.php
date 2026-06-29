@props(['role'])
@props(['offer'])

<div>

    <form method="POST" action="{{ route("$role.offers.destroy", $offer->id) }}">
        @csrf
        @method('DELETE')
        <div class="flex items-center justify-center mt-4">
            <button class="absolute bottom-0 right-0 m-1 text-2xl" title="{{ __('common.delete') }}">&#10008;</button>
        </div>
    </form>
    @if ($role === 'admin')
        <p class="font-semibold">{{ __('users.advertiser') }}: <span class="font-light">{{ $offer->advertiser->name }}</span>
    </p> @endif
    <p class="font-semibold">{{ __('offers.offer_name') }}: <span class="font-light">{{ $offer->name }}</span></p>
    <p class="font-semibold">{{ __('offers.offer_theme') }}: <span class="font-light">{{ $offer->theme->name }}</span></p>
    <p class="font-semibold">URL: <span class="font-light">{{ $offer->url }}</span></p>
    <p class="font-semibold">{{ __('offers.offer_price') }}: <span class="font-light">{{ $offer->price }}</span></p>
    <p class="subscribers font-semibold">{{ __('offers.subscribers') }}: <span class="font-light">{{ $offer->subscribe->count() }}</span></p>

</div>