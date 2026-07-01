@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center pb-6 pt-4 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                <p class="font-semibold text-gray-700 dark:text-gray-200">{{ __('users.new_user') }}:</p>
                <div
                    class=" w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-lg">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required 
                                autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required 
                                autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <x-select-input id="role" class="block mt-1 w-full" name="role" :selected="old('role')" required
                                :options="[
                                            'admin' => 'users.admin',
                                            'advertiser' => 'users.advertiser',
                                            'webmaster' => 'users.webmaster',
                                        ]"
                                :translate="true">
                            </x-select-input>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Button -->
                        <div class="flex items-center justify-center mt-4">
                            <x-secondary-button type="reset">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button class="ms-4">
                                {{ __('Create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                <div class="flex flex-col items-center pt-5 pb-1">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-xl">{{ __('users.current_users') }}:</h3>
                </div>

                <h4 class="font-semibold text-gray-700 dark:text-gray-200 p-3">{{ __('users.admins') }}:</h4>

                <x-table-user :users="$admins">
                </x-table-user>

                <h4 class="font-semibold text-gray-700 dark:text-gray-200 p-3">{{ __('users.advertisers') }}:</h4>

                <x-table-user :users="$advertisers">
                </x-table-user>

                <h4 class="font-semibold text-gray-700 dark:text-gray-200 p-3">{{ __('users.webmasters') }}:</h4>

                <x-table-user :users="$webmasters">

                </x-table-user>
            </div>
        </div>
    </section>
@endsection