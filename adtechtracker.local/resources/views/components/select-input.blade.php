@props(['disabled' => false])
{{-- @props(['roles', 'selected' => null]) --}}

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>
    <option></option>
    <option value="advertiser">Рекламодатель</option>
    <option value="webmaster">Вебмастер</option>
    {{-- @foreach ($roles as $role)
        <option value={{ $role['id'] }}>{{ $role['role'] }}</option>
    @endforeach --}}
</select>