@props(['disabled' => false])
@props(['role'])
@props(['create'])

@isset($role)
    @if ($role === 'admin')
        @php
            $role_ru = 'Администратор'
        @endphp
    @elseif ($role === 'advertiser')
        @php
            $role_ru = 'Рекламодатель'
        @endphp
    @elseif ($role === 'webmaster')
        @php
            $role_ru = 'Вебмастер'
        @endphp
    @else
        @php
            $role_ru = null
        @endphp
    @endif
@endisset

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>

    {{-- <option>{{ $role ?? ''}}</option> --}}
    
    <option value="{{ $role ?? '' }}">{{ $role_ru ?? ''}}</option>
    @isset ($create)
        <option value="admin">Администратор</option>
    @endisset
    <option value="advertiser">Рекламодатель</option>
    <option value="webmaster">Вебмастер</option>

    {{-- @foreach ($roles as $role)
        <option value={{ $role['id'] }}>{{ $role['role'] }}</option>
    @endforeach --}}
</select>