<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\SecurityLogger;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Изменяем язык системы у пользователя
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateLocale(Request $request): RedirectResponse
    {
        // проверяем язык
        $request->validate([
            'lang' => ['required', Rule::in(array_keys(config('app.available_locales')))],
        ]);

        // обновляем язык в БД
        $request->user()->update([
            'locale' => $request->input('lang'),
        ]);

        // записываем в сессию
        session([
            'locale' => $request->input('lang'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'locale-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // пишем событие самообновления в лог
        SecurityLogger::updatingUserInformation($request->user(), $request);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // проверка если последний админ в системе, то нельзя удалить
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            throw ValidationException::withMessages([
                'password' => __('users.last_admin_cannot_delete_himself'),
            ])->errorBag('userDeletion');
        }

        // пишем событие самоудаления в лог
        SecurityLogger::deletingUser($user, $request);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
