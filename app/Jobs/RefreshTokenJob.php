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

    private ApiClient $client;
    private TokenService $service;

    public function __construct(TokenService $service)
    {
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service->refreshToken();
    }
}
