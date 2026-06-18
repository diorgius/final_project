@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <x-section-period>

                </x-section-period>

                <div class="flex flex-col items-center pt-5 pb-1">
                    <p>Статистика:</p>
                </div>
                <div class="flex flex-col items-center w-2/3 mt-6 px-6 py-4 mb-2 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                                    dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

                    <table>
                    @foreach ($offers as $offer)
                        <!-- {{ $offer }} -->
                        <tr><td>{{ $offer->name }}</td><td>{{ $offer->click_count }}</td><td>{{ $offer->advertiser_expenses }}</td></tr>
                    @endforeach
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection