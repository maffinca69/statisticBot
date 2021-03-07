<?php


namespace App\Jobs;


use App\Modules\Google\ApiClient;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

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
}
