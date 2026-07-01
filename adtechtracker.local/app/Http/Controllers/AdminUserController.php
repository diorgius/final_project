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
        $user = User::withTrashed()->find($id);
        
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
            'name' => ['required', 'string', 'max:255'],
            // не даем возможность менять email, т.к. по нему регистрация и по нему вход
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::find($id);
        // проверяем пароль, если изменился, то записываем новый, если нет, то старый
        $password = $request->password === $user->password ? $user->password : Hash::make($request->password);
        
        // проверяем статус
        $status = isset($request->status) ? 1 : 0;

        if ($user->status !== $status && $status === 0) {
            // отправляем письмо
            Mail::to($user->email)->locale($user->locale)->send(new UserBlockedMail($user));
        }

        $user->name = $request->name;
        $user->password = $password;
        $user->role =$request->role;
        $user->status = $status;
        $user->save();

        return redirect()->route('users.index');
    }

    /**
     * Восстанавливаем пользователя
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $id)
    {
        // используем транзакцию
        DB::transaction(function () use ($id) {

            // получаем пользователя
            $user = User::withTrashed()->findOrFail($id);

            // восстанавливаем пользователя
            $user->restore();
            
            if ($user->role === 'advertiser') {
                // получаем его офферы
                // $offers = $user->offers()->onlyTrashed()->get();
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

                    // отправляем сообщение !!! В ИДЕАЛЕ ВЫНЕСТИ ИЗ ТРАНЗАКЦИИ !!!
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
                    // отправляем сообщение !!! В ИДЕАЛЕ ВЫНЕСТИ ИЗ ТРАНЗАКЦИИ !!!
                    broadcast(new OfferSubscribeChanged($subscription->offer, $user->id, 'subscribed'));
                }           
            }
        });

        return redirect()->route('users.index');
    } 

    /**
     * Удаляем пользователя
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // получаем ползователя
        $user = User::findOrFail($id);

        // используем транзакцию
        DB::transaction(function () use ($user) {

            // удаляем офферы и подписки пользователя
            if ($user->role === 'advertiser') {
                foreach ($user->offers as $offer) {
                    // удаляем подписки и офферы
                    $offer->subscribe()->delete();
                    $offer->delete();
                    // отправляем сообщение об удалении оффера !!! В ИДЕАЛЕ ВЫНЕСТИ ИЗ ТРАНЗАКЦИИ !!!
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
                    // отправляем сообщение об отписке от оффера !!! В ИДЕАЛЕ ВЫНЕСТИ ИЗ ТРАНЗАКЦИИ !!!
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

        return redirect()->route('users.index');
    }
}
