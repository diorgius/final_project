@props(['disabled' => false])
@props(['role'])
@props(['create'])
@props(['themes'])
@props(['lang'])

@isset($role)
    @if ($role === 'admin')
        @php
            $role_full = __('users.admin')
        @endphp
    @elseif ($role === 'advertiser')
        @php
            $role_full = __('users.advertiser')
        @endphp
    @elseif ($role === 'webmaster')
        @php
            $role_full = __('users.webmaster')
        @endphp
    @else
        @php
            $role_full = null
        @endphp
    @endif
@endisset

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>

    @isset($themes)
        <option></option>
        @foreach ($themes as $theme)
            <option value={{ $theme['id'] }}>{{ $theme['name'] }}</option>
        @endforeach
    @elseif(@isset($lang))
        <option value="ru">{{ __('common.ru') }}</option>
        <option value="en">{{ __('common.en') }}</option>
    @else
        <option value="{{ $role ?? '' }}">{{ $role_full ?? ''}}</option>
        @isset ($create)
            <option value="admin">{{ __('users.admin') }}</option>
        @endisset
        <option value="advertiser">{{ __('users.advertiser') }}</option>
        <option value="webmaster">{{ __('users.webmaster') }}</option>
    @endisset

</select>