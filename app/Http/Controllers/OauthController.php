<?php

namespace App\Http\Controllers;

use App\Services\OAuthService;
use Illuminate\Http\Request;

/**
 * Controller for Google OAuth 2.0
 *
 * Class OauthController
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{
    private OAuthService $service;

    public function __construct(Request $request, OAuthService $service)
    {
        parent::__construct($request);

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
        $authenticated = $this->service->authByCode($request->all());
        if ($authenticated) {
            return view('successful_auth');
        }

        return view('error_auth');
    }
}
