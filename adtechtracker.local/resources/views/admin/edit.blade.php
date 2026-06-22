@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="/css/admin.css">
@endsection

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <p class="font-semibold text-gray-700 dark:text-gray-200">Редактирование данных пользователя - {{ $user->name }}<p>
                <div
                    class=" w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Имя')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $user->name) }}"
                                required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                value="{{ old('email', $user->email) }}" required autocomplete="username" disabled title="email изменить нельзя"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Роль')" />
                            <x-select-input id="role" class='block mt-1 w-full' name="role" :role="$user->role" required >
                            </x-select-input>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Пароль')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" value="{{ old('password', $user->password) }}" required
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-4" align="center">
                            <x-input-label for="password" :value="__('Статус')" />
                            <label class='switch'>
                                @if ($user->status === 1)
                                    <input type="checkbox" name="status" checked />
                                @else
                                    <input type="checkbox" name="status" />
                                @endif
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <!-- Button -->
                        <div class="flex items-center justify-center mt-4">
                            <x-reset-button onclick="window.location='{{ route('users.index') }}'">
                                {{ __('Отменить') }}
                            </x-reset-button>
                            <x-primary-button class="ms-4">
                                {{ __('Сохранить') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Delete -->
                    <form method="POST" action="{{ route('users.destroy', $user->id) }}">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-center mt-4">
                            <x-primary-button>
                                {{ __('Удалить пользователя') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection