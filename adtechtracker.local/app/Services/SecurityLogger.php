<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    /**
     * Определяем тексты сообщений для записи в лог.
    */
    private const REGISTRATION_EXIST_EMAIL = 'Registration attempt with existing email.';
    private const REGISTRATION_SUCCESSFUL = 'Successful registration.';
    private const LOGIN_UNKNOWN_EMAIL = 'Login attempt with unknown email.';
    private const LOGIN_WRONG_PASSWORD = 'Login attempt with wrong password.';
    private const LOGIN_BLOCKED_USER = 'Blocked user login attempt.';
    private const LOGIN_SUCCESSFUL = 'Successful login.';
    private const LOGOUT_SUCCESSFUL = 'Successful logout.';
    private const CREATING_USER = 'Creating the user.';
    private const BLOCKING_USER = 'Blocking the user.';
    private const UNBLOCKING_USER = 'Unblocking the user.';
    private const DELETING_USER = 'Deleting the user.';
    private const RESTORING_USER = 'Restoring the user.';
    private const UPDATE_USER_INFORMATION = 'Updating profile information.';
    private const UPDATE_USER_PASSWORD = "Updating the user's password.";
    

    /**
     * Запись в лог
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    private static function log(string $level, string $message, array $context = []): void {
        
        // определяем данные для записи в лог
        Log::channel('security')->log(
            $level,
            $message,
            $context
        );
    }

    /**
     * Получаем необходимые данные из запроса
     * @param Request $request
     * @return array{ip: string|null, user_agent: string|null}
     */
    private static function requestContext(Request $request): array
    {
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }

    /**
     * Попытка регистрации с уже существующим Email.
     * @param string $email
     * @param Request $request
     * @return void
     */
    public static function registrationWithExistingEmail(string $email, Request $request): void
    {

        // запись в лог
        self::log(
            'warning',
            self::REGISTRATION_EXIST_EMAIL,
            array_merge(
                self::requestContext($request),
                [
                    'email' => $email,
                ]
            )
        );
    }

    /** Успешная регистрация
     * @param string $email
     * @param Request $request
     * @return void
     */
    public static function successfulRegistration(string $email, Request $request): void
    {

        // запись в лог
        self::log(
            'info',
            self::REGISTRATION_SUCCESSFUL,
            array_merge(
                self::requestContext($request),
                [
                    'email' => $email,
                ]
            )
        );
    }

    /**
     * Попытка входа с несуществующим email
     * @param string $email
     * @param Request $request
     * @return void
     */
    public static function loginWithUnknownEmail(string $email, Request $request): void {

        // запись в лог
        self::log(
            'warning',
            self::LOGIN_UNKNOWN_EMAIL,
            array_merge(
                self::requestContext($request),
                [
                    'email' => $email,
                ]
            )
        );
    }

    /**
     * Попытка входа с неверным паролем
     * @param User $user
     * @param Request $request
     * @return void
     */
    public static function loginWithWrongPassword(User $user, Request $request): void {

        // запись в лог
        self::log(
            'warning',
            self::LOGIN_WRONG_PASSWORD,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            )
        );
    }

    /**
     * Попытка входа заблокированного пользователя.
     * @param User $user
     * @param Request $request
     * @return void
     */
    public static function blockedUserLogin(User $user, Request $request): void {

        // запись в лог
        self::log(
            'warning',
            self::LOGIN_BLOCKED_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            )
        );
    }

    /**
     * Успешный вход
     * @param User $user
     * @param Request $request
     * @return void
     */
    public static function successfulLogin(User $user, Request $request): void {

        // запись в лог
        self::log(
            'info',
            self::LOGIN_SUCCESSFUL,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            )
        );
    }

    /**
     * Успешный выход
     * @param User $user
     * @param Request $request
     * @return void
     */
    public static function successfulLogout(User $user, Request $request): void
    {

        // запись в лог
        self::log(
            'info',
            self::LOGOUT_SUCCESSFUL,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            )
        );
    }

    /**
     * Создание пользователя админом
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function creatingUser(User $user, Request $request, string $admin): void
    {

        // запись в лог
        self::log(
            'info',
            self::CREATING_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Блокировка пользователя админом
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function blockingUser(User $user, Request $request, string $admin): void
    {

        // запись в лог
        self::log(
            'info',
            self::BLOCKING_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Разблокировка пользователя админом
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function unblockingUser(User $user, Request $request, string $admin): void
    {

        // запись в лог
        self::log(
            'info',
            self::UNBLOCKING_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Удаление пользователя
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function deletingUser(User $user, Request $request, string $admin = ''): void
    {

        // запись в лог
        self::log(
            'info',
            self::DELETING_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Восстановление пользователя админом
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function restoringUser(User $user, Request $request, string $admin): void
    {

        // запись в лог
        self::log(
            'info',
            self::RESTORING_USER,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Изменение информации пользователя
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function updatingUserInformation(User $user, Request $request, string $admin = ''): void
    {

        // запись в лог
        self::log(
            'info',
            self::UPDATE_USER_INFORMATION,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }

    /**
     * Изменение пароля пользователя
     * @param User $user
     * @param Request $request
     * @param string $admin
     * @return void
     */
    public static function updatingUserPassword(User $user, Request $request, string $admin = ''): void
    {

        // запись в лог
        self::log(
            'info',
            self::UPDATE_USER_PASSWORD,
            array_merge(
                self::requestContext($request),
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'admin' => $admin,
                ]
            )
        );
    }
}