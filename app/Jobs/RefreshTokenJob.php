<?php


namespace App\Jobs;

use Exception;
use App\Services\TokenService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshTokenJob extends Job  implements ShouldQueue
{
    use SerializesModels;

    private TokenService $service;
    private int $userId;

    public function __construct(TokenService $service, int $userId)
    {
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service->refreshToken($this->userId);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error($exception->getTrace());
    }
}
