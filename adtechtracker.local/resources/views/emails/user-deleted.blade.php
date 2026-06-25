<x-mail::message>
{{-- # Учетная запись удалена --}}
# Здравствуйте, {{ $user->name }}!


Ваша учетная запись в системе AdTechTracker была удалена администратором.

Если вы считаете это ошибкой, свяжитесь с администрацией.

{{-- <x-mail::button :url="''">
Button Text
</x-mail::button> --}}

С уважением, AdTechTracker<br>
{{ config('app.name') }}
</x-mail::message>
