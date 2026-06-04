@extends('layouts.app')

@section('header')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Панель администрирования') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-4 pt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <p class="font-semibold text-gray-700">Новый пользователь:</p>
                <div
                    class=" w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden rounded-lg">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Имя')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                                required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Роль')" />
                            {{-- <x-select-input id="role" class='block mt-1 w-full' name="role" :roles="$roles" required>
                                --}}
                                <x-select-input id="role" class='block mt-1 w-full' name="role" required>
                                </x-select-input>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Пароль')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-center mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Создать пользователя') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                <div class="flex flex-col items-center p-5">
                    <h3 class="font-semibold text-gray-700 text-xl">Текущие пользователи:</h3>
                    <h4 class="font-semibold text-gray-700 p-3">Рекламодатели:</h4>
                </div>
                <table class="table-auto mx-auto w-11/12 text-sm text-left text-gray-500 dark:text-gray-400 border"
                    id="table-users">
                    <tr>
                        <th class="p-2">Имя</th>
                        <th class="p-2">E-mail</th>
                        <th class="p-2">Статус</th>
                        {{-- <th class="p-2">Роль</th> --}}
                    </tr>
                    <tbody>
                        @foreach ($advertisers as $advertiser)
                            <tr class="table-users__tr relative" id="{{$advertiser->id}}">
                                <td class="p-2">{{$advertiser->name}}</td>
                                <td class="p-2">{{$advertiser->email}}</td>
                                <td class="p-2">
                                    <label class="switch">
                                        @if ($advertiser->status === 1)
                                            <input type="checkbox" name="status" class="table-offers__input-status mx-auto"
                                                title="выключить" checked>
                                        @else
                                            <input type="checkbox" name="status" class="table-offers__input-status mx-auto"
                                                title="включить">
                                        @endif
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                {{-- <td class="p-2">{{$advertiser->role}}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h4 class="font-semibold text-gray-700 p-3">Вебмастера:</h4>
                <table class="table-auto mx-auto w-11/12 text-sm text-left text-gray-500 dark:text-gray-400 border"
                    id="table-users">
                    <tr>
                        <th class="p-2">Имя</th>
                        <th class="p-2">E-mail</th>
                        <th class="p-2">Статус</th>
                        {{-- <th class="p-2">Роль</th> --}}
                    </tr>
                    <tbody>
                        @foreach ($webmasters as $webmaster)
                            <tr class="table-users__tr relative" id="{{$webmaster->id}}">
                                <td class="p-2">{{$webmaster->name}}</td>
                                <td class="p-2">{{$webmaster->email}}</td>
                                <td class="p-2">
                                    <label class="switch">
                                        @if ($webmaster->status === 1)
                                            <input type="checkbox" name="status" class="table-offers__input-status mx-auto"
                                                title="выключить" checked>
                                        @else
                                            <input type="checkbox" name="status" class="table-offers__input-status mx-auto"
                                                title="включить">
                                        @endif
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                {{-- <td class="p-2">{{$webmaster->role}}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection