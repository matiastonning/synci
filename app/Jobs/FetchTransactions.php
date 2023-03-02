<?php

namespace App\Jobs;

use App\Enums\SourceType;
use App\Enums\TransactionStatus;
use App\Enums\TransferStatus;
use App\Http\Controllers\TinkController;
use App\Models\Destination;
use App\Models\InboundEmail;
use App\Models\Source;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class FetchTransactions implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $maxExceptions = 2;
    public int $timeout = 10;
    public bool $failOnTimeout = true;

    /**
     * The source instance.
     *
     * @var Source
     */
    public Source $source;

    public string $request_id;
    public string $start_date;
    private ?string $status_message = '';
    private ?array $status_context;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Source $source, string $start_date, string $requestId)
    {
        $this->source = $source;
        $this->start_date = $start_date;

        //Set logging context
        $this->status_context = ['request_id' => $requestId, 'source_id' => $source->id];
        $this->request_id = $requestId;

        //Check if UUID exists in database and get user
        $this->user = $source->user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $source = $this->source;
        $user = $this->user;
        $startDate = empty($this->start_date) ? $source->last_synced : $this->start_date;

        $tink = new TinkController();
        $response = $tink->fetchTransactions($user->id, $source, $startDate);

        // handle error
        if($response->original['status'] == 'error') {
            $this->status_message = $response->original['statusMessage'];
            $this->status_context = ['source' => $source];
            $this->fail();
        }

        //Return success message
        Log::info('Fetched transactions.', ['source' => $source, 'start_date' => $startDate, 'request_id' => $this->request_id]);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->request_id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     *
    public function middleware(): array
    {
        return [new RateLimited];
    }*/

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Log::warning($this->status_message, $this->status_context);
    }
}
