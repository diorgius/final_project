@extends('layouts.app')

@section('content')
    <section class="flex flex-col items-center justify-center py-12">
        <div
            class="flex items-center justify-center w-1/2 h-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <div
                class="flex items-center justify-center max-w flex-col lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] 
                                        shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
                <h1 class="m-4 text-3xl text-indigo-600 font-semibold">Приложение SF-AdTech</h1>
                <p class="m-4 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    трекер трафика, созданный для организации
                    взаимодействия компаний (рекламодателей), которые хотят привлечь к себе
                    на сайт посетителей и покупателей (клиентов), и владельцев сайтов (веб-мастеров), на которые приходят
                    люди.
                </p>
            </div>
        </div>
    </section>
@endsection