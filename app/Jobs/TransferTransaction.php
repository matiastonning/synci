<?php

namespace App\Jobs;

use App\Enums\SourceType;
use App\Enums\TransactionStatus;
use App\Enums\TransferStatus;
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

class TransferTransaction implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $maxExceptions = 2;
    public int $timeout = 10;
    public bool $failOnTimeout = true;

    /**
     * The transaction instance.
     *
     * @var Transaction
     */
    public Transaction $transaction;

    public string $request_id;
    private ?string $status_message = '';
    private ?array $status_context;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction, string $requestId)
    {
        $this->transaction = $transaction;

        //Set logging context
        $this->status_context = ['request_id' => $requestId, 'transaction_id' => $transaction->id];
        $this->request_id = $requestId;

        //Check if transaction has already been transferred
        if($transaction->status === TransactionStatus::TRANSFERRED()) {
            $this->status_message = 'Transaction has already been transferred.';
            Log::info($this->status_message, $this->status_context);
            $this->fail();
        }

        //Check if UUID exists in database and get user
        $this->user = $transaction->user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = $this->transaction;
        $user = $this->user;

        //Get destinations
        $destinations = Destination::where('source_id', $transaction->source_id)->where('active', true)->get();

        //Check if there are any active destinations
        if($destinations->count() === 0) {
            $this->status_message = 'No active destinations found.';
            Log::info($this->status_message, $this->status_context);
            return;
        }

        foreach ($destinations as $destination) {
            //Create new transfer log for each destination
            $transferLog = $transaction->transferLog()->create([
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'destination_id' => $destination->id,
                'status_code' => TransferStatus::PENDING(),
                'status_message' => 'Transfer pending.',
            ]);

            //Do some stuff


            //Update transfer log
            $transferLog->update([
                'status_code' => TransferStatus::SUCCESS(),
                'status_message' => 'Transfer successful.',
            ]);
        }

        $transaction->status = TransactionStatus::TRANSFERRED();
        $transaction->save();

        //Return success message
        Log::info('Transferred transaction.', ['transaction' => $transaction]);
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
     * @return array
     */
    public function middleware()
    {
        return [new RateLimited('email_parse')];
    }

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

    private function cleanEmailBody($body): string
    {
        $cleanBody = '';
        $i = 0;

        //Split up body into array of lines
        $lines = explode("\n", str_replace("\r", '', trim($body)));

        //Iterate through each line of the email body
        foreach ($lines as $line) {
            //Limit the number of lines to 80
            if(++$i > 80) {
                break;
            }

            //If line starts with "> ", remove it
            if(str_starts_with($line, '> ')) {
                $line = substr($line, 2);
            }

            //Trim line
            $line = trim($line);

            //Add line to new body
            $cleanBody = $cleanBody . $line . "\n";
        }

        return $cleanBody;
    }

    private function fetchStringWithRegex($regex, $body, $group = 0): string
    {
        //Use regex to extract element from the email body, return empty string if no match found
        if(!preg_match($regex, $body, $match)) {
            return '';
        }

        //Trim and return match
        return trim($match[$group]);
    }
}
