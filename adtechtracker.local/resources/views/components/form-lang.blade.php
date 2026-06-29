<form class="text-sm" method="POST" action="{{ route('locale') }}" class="flex gap-2">
    @csrf

    <button type="submit" name="locale" value="ru" class="{{ app()->getLocale() === 'ru'
    ? 'font-bold text-indigo-600'
    : 'text-gray-500' }}">
        Русский
    </button>

    <button type="submit" name="locale" value="en" class="{{ app()->getLocale() === 'en'
    ? 'font-bold text-indigo-600'
    : 'text-gray-500' }}">
        English
    </button>
</form>