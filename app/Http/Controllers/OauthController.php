<?php

namespace App\Http\Controllers;

use App\Services\OAuthService;
use Illuminate\Http\Request;

class OauthController extends Controller
{
    private OAuthService $service;

    public function __construct(Request $request, OAuthService $service)
    {
        parent::__construct($request);

        $this->service = $service;
    }

    public function callback(Request $request)
    {
        $authenticated = $this->service->authByCode($request->all());
        if ($authenticated) {
            return 'Авторизация прошла успешно. Можете вернуться обратно в бота';
        }

        return 'Произошла ошибка';
    }
}
