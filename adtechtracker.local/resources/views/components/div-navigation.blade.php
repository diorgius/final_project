<div class="flex">

    @if (Auth::user()->role === 'admin')

        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
            <x-nav-link :href="route('admin.main')" :active="request()->routeIs('admin.main')">
                {{ __('navigation.main') }}
            </x-nav-link>
            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                {{ __('navigation.users') }}
            </x-nav-link>
            <x-nav-link :href="route('admin.offers')" :active="request()->routeIs('admin.offers')">
                {{ __('navigation.offers') }}
            </x-nav-link>
            <x-nav-link :href="route('admin.statistics')" :active="request()->routeIs('admin.statistics')">
                {{ __('navigation.statistics') }}
            </x-nav-link>
        </div>

    @elseif (Auth::user()->role === 'advertiser')

        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
            <x-nav-link :href="route('advertiser.offers')" :active="request()->routeIs(['advertiser.offers', 'offers.*', 'themes.index'])">
                {{ __('navigation.offers') }}
            </x-nav-link>
            <x-nav-link :href="route('advertiser.statistics')" :active="request()->routeIs('advertiser.statistics')">
                {{ __('navigation.statistics') }}
            </x-nav-link>
        </div>

    @elseif (Auth::user()->role === 'webmaster')

        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
            <x-nav-link :href="route('webmaster.offers')" :active="request()->routeIs('webmaster.offers')">
                {{ __('navigation.offers') }}
            </x-nav-link>
            <x-nav-link :href="route('webmaster.statistics')" :active="request()->routeIs('webmaster.statistics')">
                {{ __('navigation.statistics') }}
            </x-nav-link>
        </div>

    @endif

</div>