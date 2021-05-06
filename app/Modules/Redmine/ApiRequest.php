<?php


namespace App\Modules\Redmine;


use App\Helpers\CacheHelper;

class ApiRequest
{

    private array $params = [];

    private string $method;

    private int $userId;

    /**
     * ApiRequest constructor.
     * @param string $method
     * @param int $telegramUserId
     */
    public function __construct(int $telegramUserId, string $method = '')
    {
        // method - my.account, url - my/account.json
        $this->method = str_replace('.', '/', $method);
        $this->userId = $telegramUserId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setParam($key, $value): void
    {
        $this->params[$key] = $value;
    }

    /**
     * @param array $params - [['key' => 'value'], ['key' => 'value']]
     */
    public function setParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function exec(ApiCallback $callback)
    {
        $executor = new APIExecutor();
        $executor->setApiKey(CacheHelper::getRedmineUserApiKey($this->userId));

        return $executor->exec($this, $callback);
    }

}
