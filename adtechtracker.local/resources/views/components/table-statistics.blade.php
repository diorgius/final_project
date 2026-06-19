@props(['offers'])
@props(['role'])
@props(['totalClicks'])
@props(['totalExpenses'])

<table class="text-gray-600 table-auto w-full text-center align-center">

    <tr class="border-b border-gray-200 text-sm uppercase">
        <th class="py-4">Офферы</th>
        <th class="py-4">Переходы
        <th class="py-4">{{ $role === 'advertiser' ? 'Расходы' : 'Доходы' }}</th>
    </tr>
    <tbody id="offers-table-body">
        @foreach ($offers as $offer)
        <tr class="border-b border-gray-200 text-xl">
            <td class="py-2">{{ $offer->name }}</td>
            <td class="py-2">{{ $offer->click_count }}</td>
            <td class="py-2">{{ number_format($offer->advertiser_expenses, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="border-b border-gray-200 text-xl uppercase">
            <th class="py-2">Итого</th>
            <th id="total-clicks" class="py-2">{{ $totalClicks }}</th>
            <th id="total-expenses" class="py-2">{{ number_format($totalExpenses, 2) }}</th>
        </tr>
    </tfoot>
</table>