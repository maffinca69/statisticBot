<?php


namespace App\Modules\Redmine\Api\Callbacks;


use App\Modules\Redmine\ApiCallback;
use Illuminate\Support\Facades\Log;

class UserInfoCallback implements ApiCallback
{

    public function onFailed(\Exception $error)
    {
        Log::error($error->getMessage());
    }

    public function onSuccess(array $response)
    {
        return $response;
    }
}
