<x-mail::message>
    {{-- # Учетная запись заблокирована --}}
    # {{ __('mail.hello') }}, {{ $user->name }}!


    {{ __('mail.blocked_text') }}

    {{ __('mail.contact_text') }}

    {{-- <x-mail::button :url="''">
        Button Text
    </x-mail::button> --}}

    {{ __('mail.footer_text') }},<br>
    {{ config('app.name') }}
</x-mail::message>
