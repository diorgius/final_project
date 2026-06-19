<p class="font-semibold text-gray-700">Отчетный период:
<p>
<div class="w-auto mt-6 px-6 py-2 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] 
                                dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">

    <div class='flex gap-6 justify-around items-center w-full'>

            <input type="radio" id="day" name="period" value="day" class="peer/day hidden">
            <label for="day" class="cursor-pointer rounded-lg border px-4 py-2 text-gray-700 uppercase
               transition
               hover:bg-slate-100
               peer-checked/day:bg-indigo-600
               peer-checked/day:text-white
               peer-checked/day:border-indigo-600">
                День
            </label>

            <input type="radio" id="month" name="period" value="month" class="peer/month hidden">
            <label for="month" class="cursor-pointer rounded-lg border px-4 py-2 text-gray-700 uppercase
               transition
               hover:bg-slate-100
               peer-checked/month:bg-indigo-600
               peer-checked/month:text-white
               peer-checked/month:border-indigo-600">
                Месяц
            </label>

            <input type="radio" id="year" name="period" value="year" class="peer/year hidden">
            <label for="year" class="cursor-pointer rounded-lg border px-4 py-2 text-gray-700 uppercase
               transition
               hover:bg-slate-100
               peer-checked/year:bg-indigo-600
               peer-checked/year:text-white
               peer-checked/year:border-indigo-600">
                Год
            </label>

            <input type="radio" id="all" name="period" value="all" checked class="peer/all hidden">
            <label for="all" class="cursor-pointer rounded-lg border px-4 py-2 text-gray-700 uppercase
               transition
               hover:bg-slate-100
               peer-checked/all:bg-indigo-600
               peer-checked/all:text-white
               peer-checked/all:border-indigo-600">
                Всё время
            </label>

    </div>
    {{-- <input name="period" type="radio" id="day"> Сегодня
    <input name="period" type="radio" id="month"> Текущий месяц
    <input name="period" type="radio" id="year"> Текущий год
    <input name="period" type="radio" id="all" checked> Все время
    <input name="period" type="radio" id="custom"> Произвольный период --}}
</div>
