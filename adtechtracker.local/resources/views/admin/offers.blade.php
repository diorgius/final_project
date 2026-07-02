@extends('layouts.app')

@section('js')
    @vite('resources/js/admin.js')
    <script>
        // передаем в js роль пользователя
        window.userRole = '{{ auth()->user()->role }}';
    </script>
@endsection

@section('content')
    <section class="py-12 relative z-49">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center pb-6 pt-1 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">

                <!-- выводим офферы -->
                <x-section-offer :offers="$offers" :role="auth()->user()->role">

                </x-section-offer>                     

            </div>
        </div>
    </section>
@endsection