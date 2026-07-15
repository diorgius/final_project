@props(['offers'])
@props(['role'])
@props(['totalClicks'])
@props(['total'])

<table class="table-fixed text-gray-600 table-auto w-full text-center align-center">

    <tr class="border-b border-gray-200 text-sm dark:text-gray-200 uppercase">
        <th class="py-4">{{ __('statistics.offers') }}</th>
        <th class="py-4">{{ __('statistics.status') }}</th>
        <th class="py-4">{{ __('statistics.clicks') }}
        <th class="py-4">{{ $role === 'advertiser' ? __('statistics.expenses') : __('statistics.income') }}</th>
    </tr>

    <tbody id="offers-table-body">
        @foreach ($offers as $offer)
            <tr class="border-b border-gray-200 dark:text-gray-200 text-xl">
                <td class="py-2">{{ $offer->name }}</td>
                <td class="py-2">
                    @if (!$offer->trashed() && $offer->status === 1) 
                        {{ __('statistics.active') }}
                    @elseif ($offer->trashed())
                        {{ __('statistics.deleted') }}
                    @else
                        {{ __('statistics.deactive') }}
                    @endif    
                </td>
                <td class="py-2">{{ $offer->click_count }}</td>
                <td class="py-2">{{ $role === 'advertiser' ? number_format($offer->advertiser_expenses, 2, '.', '') : number_format($offer->webmaster_revenue, 2, '.', '') }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr class="border-b border-gray-200 text-xl dark:text-gray-200 uppercase">
            <th class="py-2">{{ __('statistics.total') }}</th>
            <th class="py-2"></th>
            <th id="total-clicks" class="py-2">{{ $totalClicks }}</th>
            <th id="total-expenses" class="py-2">{{ number_format($total, 2, '.', '') }}</th>
        </tr>
    </tfoot>
    
</table>