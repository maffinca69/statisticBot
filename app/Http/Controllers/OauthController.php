<?php

namespace App\Http\Controllers;

use App\Services\OAuthService;
use Illuminate\Http\Request;
use Telegram\Bot\Objects\Message;

/**
 * Controller for Google OAuth 2.0
 *
 * Class OauthController
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{
    private OAuthService $service;

    public function __construct(OAuthService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Callback function after logged
     *
     * @param    Request    $request
     * @return string
     */
    public function callback(Request $request)
    {
        $id = $this->service->authByCode($request->all());
        if ($id) {
            $this->notifySuccessfullyAuthorize($id);
            return view('successful_auth');
        }

        return view('error_auth');
    }

    private function notifySuccessfullyAuthorize(int $userId): Message
    {
        return $this->api->sendMessage([
            'chat_id' => $userId,
            'text' => '🎉 Успешно авторизованы!' . PHP_EOL . 'Теперь отправьте ссылку на свою расчетку'
        ]);
    }
}
