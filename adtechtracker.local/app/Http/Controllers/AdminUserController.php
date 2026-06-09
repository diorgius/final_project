<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Выводим страницу пользователей
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        $advertisers = User::where('role', 'advertiser')->get();
        $webmasters = User::where('role', 'webmaster')->get();

        return view('admin.users', compact('admins', 'advertisers', 'webmasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создаем нового пользователя
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => ($request->role),
            'status' => 1
        ]);

        event(new Registered($user));

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Редактируем пользователя
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        
        return view('admin.edit', compact('user'));
    }

    /**
     * Обновляем данные пользователя
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . User::class],
            // не даем возможность менять email, т.к. по нему регистрация и по нему вход
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::find($id);
        // проверяем пароль, если изменился, то записываем новый, если нет, то старый
        $password = $request->password === $user->password ? $user->password : Hash::make($request->password);
        // проверяем статус
        $status = isset($request->status) ? 1 : 0;

        $user->name = $request->name;
        $user->password = $password;
        $user->role =$request->role;
        $user->status = $status;
        $user->save();

        return redirect()->route('users.index');
    }

    /**
     * Удаляем пользователя
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index');
    }
}
