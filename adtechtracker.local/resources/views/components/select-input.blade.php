@props(['disabled' => false])
@props(['role'])
@props(['create'])
@props(['themes'])

@isset($role)
    @if ($role === 'admin')
        @php
            $role_ru = __('users.admin')
        @endphp
    @elseif ($role === 'advertiser')
        @php
            $role_ru = __('users.advertiser')
        @endphp
    @elseif ($role === 'webmaster')
        @php
            $role_ru = __('users.webmaster')
        @endphp
    @else
        @php
            $role_ru = null
        @endphp
    @endif
@endisset

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>

    @isset($themes)
        <option></option>
        @foreach ($themes as $theme)
            <option value={{ $theme['id'] }}>{{ $theme['name'] }}</option>
        @endforeach
    @else
        <option value="{{ $role ?? '' }}">{{ $role_ru ?? ''}}</option>
        @isset ($create)
            <option value="admin">{{ __('users.admin') }}</option>
        @endisset
        <option value="advertiser">{{ __('users.advertiser') }}</option>
        <option value="webmaster">{{ __('users.webmaster') }}</option>
    @endisset

</select>