<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\UserDeletedMail;
use App\Mail\UserBlockedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Events\OfferCreate;
use App\Events\OfferDelete;
use App\Events\OfferSubscribeChanged;
use App\Services\SecurityLogger;

/**
 * Summary of AdminUserController
 */
class AdminUserController extends Controller
{
    /**
     * Выводим страницу пользователей
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->withTrashed()->get();
        $advertisers = User::where('role', 'advertiser')->withTrashed()->get();
        $webmasters = User::where('role', 'webmaster')->withTrashed()->get();

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => ($request->role),
            'locale' => config('app.locale'),
            'status' => 1
        ]);

        event(new Registered($user));

        // пишем событие в лог
        SecurityLogger::creatingUser($user, $request, auth()->user()->email);

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
        $user = User::withTrashed()->findOrFail($id);
        
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
        // получаем пользователя
        $user = User::findOrFail($id);

        // проверяем email
        if ($request->email === $user->email) {
            $email = $user->email;

        } else {
            $request->validate([
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            ]);
            $email = $request->email;
        }

        // обычные проверки
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        // проверяем пароль, если изменился, то записываем новый, если нет, то старый
        $password = $request->password === $user->password ? $user->password : Hash::make($request->password);
        
        // проверяем статус
        $status = $request->has('status') ? 1 : 0;

        // если пользователя заблокирован
        if ($user->status !== $status && $status === 0) {

            // отправляем письмо
            Mail::to($user->email)->locale($user->locale)->send(new UserBlockedMail($user));

            // пишем событие в лог
            SecurityLogger::blockingUser($user, $request, auth()->user()->email);

        // если разблокировали
        } elseif ($user->status !== $status && $status === 1) {

            // пишем событие в лог
            SecurityLogger::unblockingUser($user, $request, auth()->user()->email);
        }
        
        // если изменился пароль
        if ($request->password !== $user->password) {
            
            // пишем событие в лог
            SecurityLogger::updatingUserPassword($user, $request, auth()->user()->email);
        }

        // если изменили имя или роль
        if ($request->name !== $user->name || $request->role !== $user->role) {

            // пишем событие в лог
            SecurityLogger::updatingUserInformation($user, $request, auth()->user()->email);
        }


        // сохраняем в БД
        $user->name = $request->name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $request->role;
        $user->status = $status;
        $user->save();

        return redirect()->route('users.index');
    }

    /**
     * Удаляем пользователя
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, string $id)
    {
        // получаем пользователя
        $user = User::findOrFail($id);

        // !!!??? ПРОВЕРКА АДМИН НЕ МОЖЕТ САМ СЕБЯ УДАЛИТЬ

        // используем транзакцию
        DB::transaction(function () use ($user) {

            // удаляем офферы и подписки пользователя
            if ($user->role === 'advertiser') {
                foreach ($user->offers as $offer) {

                    // удаляем подписки и офферы
                    $offer->subscribe()->delete();
                    $offer->delete();

                    // отправляем сообщение об удалении оффера
                    broadcast(new OfferDelete($offer->id));
                }
            }

            // удаляем подписки пользователя
            if ($user->role === 'webmaster') {
                
                // получаем офферы на которые подписан вебмастер
                $offers = Offer::query()
                                ->whereHas('subscribe', function ($query) use ($user) {
                                    $query->where('webmaster_id', $user->id);
                                })->get();
                                
                 // удаляем подписки
                $user->subscriptions()->delete();

                foreach ($offers as $offer) {

                    // отправляем сообщение об отписке от оффера
                    broadcast(new OfferSubscribeChanged($offer, $user->id, 'unsubscribed'));
                }
            }

            // удаляем сессии пользователя
            DB::table('sessions')->where('user_id', $user->id)->delete();
            
            // удаляем пользователя
            $user->delete();
        });

        // отправляем письмо удаленному пользователю
        Mail::to($user->email)->locale($user->locale)->send(new UserDeletedMail($user));

        // пишем событие в лог
        SecurityLogger::deletingUser($user, $request, auth()->user()->email);

        return redirect()->route('users.index');
    }

    /**
     * Восстанавливаем пользователя
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, string $id)
    {
        // получаем пользователя
        $user = User::withTrashed()->findOrFail($id);

        // используем транзакцию
        DB::transaction(function () use ($user) {

            // восстанавливаем пользователя
            $user->restore();

            if ($user->role === 'advertiser') {

                // получаем его офферы
                $offers = $user->offers()
                    ->onlyTrashed()
                    ->with('subscribe')
                    ->get();

                // восстанавливаем его офферы
                $user->offers()->onlyTrashed()->restore();

                foreach ($offers as $offer) {

                    // восстанавливаем подписки
                    $offer->subscribe()->onlyTrashed()->restore();

                    // получаем восстановленные подписки
                    $subscriptions = $offer->subscribe()->get();

                    // отправляем сообщение
                    broadcast(new OfferCreate($offer));

                    // отправляем сообщение о подписках
                    foreach ($subscriptions as $subscription) {
                        broadcast(new OfferSubscribeChanged($offer, $subscription->webmaster_id, 'subscribed'));
                    }
                }
            } elseif ($user->role === 'webmaster') {

                // получаем подписки
                $subscriptions = OfferSubscription::onlyTrashed()
                    ->with('offer')
                    ->where('webmaster_id', $user->id)
                    ->whereHas('offer', function ($query) {
                        $query->whereNull('deleted_at');
                    })->get();

                foreach ($subscriptions as $subscription) {

                    // восстанавливаем подписки
                    $subscription->restore();

                    // отправляем сообщение
                    broadcast(new OfferSubscribeChanged($subscription->offer, $user->id, 'subscribed'));
                }
            }
        });

        // пишем событие в лог
        SecurityLogger::restoringUser($user, $request, auth()->user()->email);

        return redirect()->route('users.index');
    }
}
