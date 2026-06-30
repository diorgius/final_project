@props(['users'])

<!-- <table class="table-users"> -->
<table class="w-3/4 table-auto border-collapse border border-gray-300 dark:border-gray-500 text-center text-gray-600 dark:text-gray-200">
    <tr>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('Name') }}</th>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('Email') }}</th>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('users.status') }}</th>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('users.reg_date') }}</th>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('users.update_date') }}</th>
        <th class="border border-gray-400 dark:border-gray-500">{{ __('users.delete_date') }}</th>
    </tr>
    <tbody>
        @foreach ($users as $user)
            <tr class="cursor-pointer" onclick="window.location='{{ route('users.edit', $user->id) }}'" title="{{ __('users.user_title') }}">
                <td class="border border-gray-400 dark:border-gray-500">{{ $user->name }}</td>
                <td class="border border-gray-400 dark:border-gray-500">{{ $user->email }}</td>
                <td class="border border-gray-400 dark:border-gray-500">
                    @if ($user->status === 1 && $user->deleted_at === null)
                        {{ __('users.user_status_active') }}
                    @elseif ($user->status === 0 && $user->deleted_at === null)
                        {{ __('users.user_status_deactive') }}
                    @elseif ($user->deleted_at !== null)
                        {{ __('users.user_status_deleted') }}
                    @endif
                </td>
                <td class="border border-gray-400 dark:border-gray-500">{{ $user->created_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
                <td class="border border-gray-400 dark:border-gray-500">{{ $user->updated_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
                @if ($user->deleted_at !== null)
                    <td class="border border-gray-400 dark:border-gray-500">{{ $user->deleted_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
                @else
                    <td class="border border-gray-400 dark:border-gray-500">-</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>