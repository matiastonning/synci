<?php

namespace App\Jobs;

use App\Enums\BudgetType;
use App\Enums\SourceType;
use App\Enums\TransactionStatus;
use App\Enums\TransferStatus;
use App\Http\Controllers\TinkController;
use App\Http\Controllers\TransactionsController;
use App\Models\ApiCredential;
use App\Models\Destination;
use App\Models\InboundEmail;
use App\Models\Source;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TransferTransactions implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $maxExceptions = 2;
    public int $timeout = 20;
    public bool $failOnTimeout = true;

    /**
     * The transactions object.
     *
     * @var Collection
     */
    public Collection $transactions;

    public string $request_id;
    private ?string $status_message = '';
    private ?array $status_context;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $transactions, $requestId)
    {
        $this->transactions = $transactions;

        //Set logging context
        $this->status_context = ['request_id' => $requestId];
        $this->request_id = $requestId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transactionsController = new TransactionsController();
        $transPerUser = [];

        foreach ($this->transactions as $transaction) {
            $userId = $transaction->user_id;
            $sourceId = $transaction->source_id;

            if (!isset($transPerUser[$userId])) {
                // if user is not in array, create a new empty array for their transactions
                $transPerUser[$userId] = [];
            }

            if (!isset($transPerUser[$userId][$sourceId])) {
                // if source is not in user array, create a new empty array for their transactions
                $transPerUser[$userId][$sourceId] = [];
            }

            // add transaction to user's array
            $transPerUser[$userId][$sourceId][] = $transaction;
        }


        // loop through users and get each source
        foreach ($transPerUser as $userId => $userTransactions) {

            //get api credentials belonging to user
            $apiCredentials = ApiCredential::where('user_id', $userId)->where('destination_type', BudgetType::YNAB())->first();


            // loop through sources and transfer their transactions
            foreach ($userTransactions as $sourceId => $sourceTransactions) {
                //get source by ID
                $source = Source::find($sourceId);

                //get connection link belonging to source, then get destination ID
                $destinationId = $source->connectionLink->destination_id;

                //get destination by ID
                $destination = Destination::find($destinationId);

                Log::info('Transferring ' . count($sourceTransactions) . ' transactions for user with ID ' . $userId . ' to destination with ID ' . $destination->id . '.');

                $transactionsController->transferTransactions($sourceTransactions, $destination, $apiCredentials);
            }
        }

        //Return success message
        Log::info('Transfer job complete.', ['request_id' => $this->request_id]);
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
