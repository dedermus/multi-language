<?php

namespace OpenAdminCore\Admin\MultiLanguage\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MultiLanguageController extends Controller
{

    /**
     * Установка локали для cookie
     * @param Request $request
     *
     * @return ResponseFactory|Application|Response|void
     */
    public function locale(Request $request) {
        // Сохранение данных формы в сессии
        Session::flash('form_data', $request->except('_token', 'locale'));

        $locale = $request->input('locale');
        $languages = MultiLanguage::config('languages');
        $cookie_name = MultiLanguage::config('cookie-name', 'locale');

        // Проверка, что локаль является допустимой
        if (array_key_exists($locale, $languages)) {
            Config::set('app.locale', $locale);
            App::setLocale($locale);            //locale        ru
            return response('ok')->cookie($cookie_name, $locale);
        } else {
            // Логирование ошибки, если локаль недопустима
            Log::warning("Invalid locale attempted: " . $locale);
            return response('Invalid locale', 400);
        }
    }

    /**
     * Получение локали из cookie
     * @return Factory|View|Application
     */
    public function getLogin(): Factory|View|Application
    {
        $languages = MultiLanguage::config("languages");
        $cookie_name = MultiLanguage::config('cookie-name', 'locale');
        $current = MultiLanguage::config('default');

        if(Cookie::has($cookie_name)) {
            $current = Cookie::get($cookie_name);
            Config::set('app.locale', $current);
            App::setLocale($current);
        }
        return view("multi-language::login", compact('languages', 'current'));
    }
}
