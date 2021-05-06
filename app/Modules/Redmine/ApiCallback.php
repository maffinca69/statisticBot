<?php


namespace App\Modules\Redmine;


interface ApiCallback
{
    public function onFailed(\Exception $error);

    /**
     * @param array $response - decoded json
     * @return mixed
     */
    public function onSuccess(array $response);
}
