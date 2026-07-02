<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Регистрируем нового пользователя и перенаправляем на страницу в зависимости от роли
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // проверка на ранее удаленные записи, если пользователь с таким email уже был и удален, направляем сообщение 
        $deletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->exists();

        if ($deletedUser) {
            throw ValidationException::withMessages([
                'email' => __('auth.deleted_account'),
            ]);
        }

        // далее обычные проверки
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required'
        ]);

        // если все хорошо, создаем учетку
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => ($request->role),
            'locale' => session('locale', config('app.locale')),
            'status' => 1
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
