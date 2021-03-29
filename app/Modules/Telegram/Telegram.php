<?php


namespace App\Modules\Telegram;


use App\Callbacks\Callback;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Telegram extends \Longman\TelegramBot\Telegram
{

    private $chatId;
    private $userId;

    /**
     * @return mixed
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @param mixed $chatId
     */
    public function setChatId($chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    protected $callbackPaths = [
        __DIR__ . '/../../Callbacks',
    ];

    protected $callbackObjects = [];

    public function prepareUpdate(Update $update)
    {
        switch ($update->getUpdateType()) {
            case 'callback_query':
                $callbackQuery = $update->getCallbackQuery();

                $this->setUserId($callbackQuery->getFrom()->getId());
                $this->setChatId($callbackQuery->getMessage()->getChat()->getId());
                break;
            case 'message':
                $message = $update->getMessage();

                $this->setUserId($message->getFrom()->getId());
                $this->setChatId($message->getChat()->getId());
                break;
        }
    }

    public function initializeCallbacks()
    {
        $this->callbackObjects = $this->getCallbackList();

        if ($this->update->getUpdateType() === 'callback_query') {
            return $this->executeCallback($this->update->getCallbackQuery()->getData());
        }
    }

    /**
     * @param string $sourceCallback
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    private function executeCallback(string $sourceCallback): ServerResponse
    {
        $callback = mb_strtolower($sourceCallback);

        $callbackObj = $this->callbackObjects[$callback] ??
            $this->callbackObjects[$sourceCallback] ?? null;

        $response = null;
        /** @var Callback $callbackObj */
        if (!$callbackObj || !$callbackObj->isEnabled()) {
            $response = Request::sendMessage([
                'chat_id' => $this->chatId,
                'text' => '❗️ Временно недоступно',
            ]);

        } else {
            $response = $callbackObj->preExecute();
        }

        return $response;
    }

    private function getCallbackList(): array
    {
        $callbacks = [];

        if (!$this->update->getCallbackQuery()) {
            return $callbacks;
        }

        foreach ($this->callbackPaths as $path) {
            try {
                //Get all "*Callback.php" files
                $files = new RegexIterator(
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path)
                    ),
                    '/^.+Callback.php$/'
                );

                foreach ($files as $file) {
                    //Remove "Command.php" from filename
                    $callback = $this->sanitizeCommand(substr($file->getFilename(), 0, -12));
                    $callbackName = mb_strtolower($callback);

                    if (empty($callbackName)) {
                        continue;
                    }

                    if (array_key_exists($callbackName, $callbacks)) {
                        continue;
                    }

                    require_once $file->getPathname();

                    $callback_obj = $this->getCallbackObject($callback, $file->getPathname());
                    if ($callback_obj instanceof Callback) {
                        $callbacks[$callbackName] = $callback_obj;

                        if (!empty($aliases = $callback_obj->aliases())) {
                            foreach ($aliases as $alias) {
                                $callbacks[$alias] = $callback_obj;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                throw new TelegramException('Error getting commands from path: ' . $path, $e);
            }
        }

        return $callbacks;
    }

    private function getCallbackObject(string $callback, string $filepath = ''): ?Callback
    {
        $callbackNamespace = $this->getFileNamespace($filepath);

        $callbackClass = $callbackNamespace . '\\' . $this->ucFirstUnicode($callback) . 'Callback';

        if (class_exists($callbackClass)) {
            return new $callbackClass($this, $this->update->getCallbackQuery());
        }

        return null;
    }
}
