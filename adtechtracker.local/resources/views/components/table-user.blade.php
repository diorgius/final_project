@props(['users'])

<!-- <table class="table-users"> -->
<table class="w-3/4 table-auto border-collapse border border-gray-300 dark:border-gray-200 text-center text-gray-600 dark:text-gray-300">
    <tr>
        <th class="border border-gray-400 dark:border-gray-200">Имя</th>
        <th class="border border-gray-400 dark:border-gray-200">Email</th>
        <th class="border border-gray-400 dark:border-gray-200">Статус</th>
        <th class="border border-gray-400 dark:border-gray-200">Дата регистрации</th>
        <th class="border border-gray-400 dark:border-gray-200">Дата обновления</th>
    </tr>
    <tbody>
        @foreach ($users as $user)
            <tr class="cursor-pointer" onclick="window.location='{{ route('users.edit', $user->id) }}'" title="Редактировать данные пользователя">
                <td class="border border-gray-400 dark:border-gray-200">{{ $user->name }}</td>
                <td class="border border-gray-400 dark:border-gray-200">{{ $user->email }}</td>
                <td class="border border-gray-400 dark:border-gray-200">
                    @if ($user->status === 1)
                        активный
                    @else
                        отключен
                    @endif
                </td>
                <td class="border border-gray-400 dark:border-gray-200">{{ $user->created_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
                <td class="border border-gray-400 dark:border-gray-200">{{ $user->updated_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>