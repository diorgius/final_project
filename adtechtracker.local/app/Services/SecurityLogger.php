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
    private const LOGIN_UNKNOWN_EMAIL = 'Login attempt with unknown email.';
    private const LOGIN_WRONG_PASSWORD = 'Login attempt with wrong password.';
    private const LOGIN_BLOCKED_USER = 'Blocked user login attempt.';
    private const LOGIN_SUCCESSFUL = 'Successful login.';
    private const REGISTRATION_EXIST_EMAIL = 'Registration attempt with existing email.';
    private const REGISTRATION_SUCCESSFUL = 'Successful registration.';
    
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
     * Попытка регистрации с уже существующим Email.
     * @param string $email
     * @param Request $request
     * @return void
     */
    public static function registrationWithExistingEmail(string $email, Request $request): void {

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

    /** Успешный вход
    * @param string $email
    * @param Request $request
    * @return void
    */
    public static function successfulRegistration(string $email, Request $request): void {

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
}