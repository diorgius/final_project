@extends('layouts.app')

@section('content')
    <section class="flex flex-col items-center justify-center py-12">
        <div
            class="flex items-center justify-center w-1/2 h-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <div class="flex items-center justify-center max-w flex-col lg:p-20 bg-white dark:bg-gray-800
                                                shadow-lg border border-gray-200 dark:border-gray-700 rounded-lg">
                <h1 class="m-4 text-3xl text-indigo-600 dark:text-gray-200 font-semibold">{{ __('common.application') }}</h1>
                <p class="m-4 text-center text-sm text-gray-700 dark:text-gray-300">
                    {{ __('common.dashboard_text') }}
                </p>
            </div>
        </div>
    </section>
@endsection
