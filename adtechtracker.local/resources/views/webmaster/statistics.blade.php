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

                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white 
                                dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                                dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <x-table-statistics :offers="$offers" :totalClicks="$totalClicks" :total="$totalRevenue" :role="auth()->user()->role">

                    </x-table-statistics>

                </div>
            </section>
        </div>
    </section>
@endsection