@props(['users'])

<table class="table-users">
    <tr>
        <th>Имя</th>
        <th>Email</th>
        <th>Статус</th>
        <th>Дата регистрации</th>
        <th>Дата обновления</th>
    </tr>
    <tbody>
        @foreach ($users as $user)
            <tr onclick="window.location='{{ route('users.edit', $user->id) }}'" title="Редактировать данные пользователя">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->status === 1)
                        активный
                    @else
                        отключен
                    @endif
                </td>
                <td>{{ $user->created_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
                <td>{{ $user->updated_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>