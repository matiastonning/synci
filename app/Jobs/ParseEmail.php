<?php

namespace App\Jobs;

use App\Enums\SourceType;
use App\Enums\TransactionStatus;
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

class ParseEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $maxExceptions = 2;
    public int $timeout = 10;
    public bool $failOnTimeout = true;

    /**
     * The inbound email instance.
     *
     * @var InboundEmail
     */
    public InboundEmail $email;

    public string $request_id;
    private ?string $status_message = '';
    private ?array $status_context;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(InboundEmail $email, string $requestId)
    {
        //Set logging context
        $this->status_context = ['request_id' => $requestId, 'to' => $email->to, 'from' => $email->from];

        $this->email = $email;
        $this->request_id = $requestId;

        //Check if receiver address contains valid UUID
        if(!preg_match("/[a-f\d]{8}(-[a-f\d]{4}){3}-[a-f\d]{12}/i", $this->email->to, $uuid)) {
            $this->status_message = 'Received payload without valid UUID.';
            $this->fail();
        }

        //Check if UUID exists in database and get user
        $user = User::where('uuid', $uuid[0])->first();
        if($user === null) {
            $this->status_message = 'Received payload, but the UUID does not exist.';
            $this->fail();
        } else {
            $this->email->user_id = $user->id;
            $this->email->save();
            $this->user = $user;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $from = $this->email->from;

        if(str_contains($from, 'forwarding-noreply@google.com')) {
            //Verification email, try to parse and store code and/or link
            //Regex matching all URLS: /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)/m
            Log::info('Detected verification email.', $this->status_context);

            //Get confirmation link and code
            preg_match('/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.google.com\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)/m', $this->email->body, $link);
            preg_match('/(?<=Confirmation code: )(.*)(?=\n)/m', $this->email->body, $code);
            $this->email->confirmation_url = trim($link[0]);
            $this->email->confirmation_code = trim($code[0]);

            //If neither code nor link is found, fail
            if(!isset($link[0]) && !isset($code[0])) {
                $this->status_message = 'Received verification email, but could not find confirmation link or code.';
                $this->fail($this->status_message);
            } else {
                //If link or code is found, save to inbound email, so they may be presented to the user. Also, update or create source.
                $this->email->save();
                Source::updateOrCreate(
                    ['user_id' => $this->user->id, 'type' => 0],
                    ['user_id' => $this->user->id, 'type' => 0]
                );
            }

            return;
        }

        $transaction = new Transaction(['id' => $this->request_id]);
        $to = $this->email->to;

        //Declare and clean the email body
        $body = $this->cleanEmailBody($this->email->body);

        //Get source from "to" email address
        if(str_contains($to, 'curve')) {
            $sourceType = SourceType::Curve;
        } else {
            $this->status_message = 'Received transaction payload without valid source.';
            $this->fail();
        }

        //Get source from database if it exists, otherwise create it
        $source = Source::where('user_id', $this->user->id)->where('type', $sourceType)->first();
        if ($source === null) {
            $source = new Source(['user_id' => $this->user->id, 'type' => $sourceType]);
        }
        $source->active = true;
        $source->save();

        //Fetch key strings from the email body
        $memo = $this->fetchStringWithRegex("/(?<=bank statement as:[\r\n])(.*)(?=\n)/mi", $body);
        $amount = $this->fetchStringWithRegex("/\d+\.\d\d/mi", $body);
        $currency = $this->fetchStringWithRegex("/(?<=\d\.\d\d )(\w\w\w)(?=\n)/mi", $body);
        $paymentDate = Carbon::parse($this->fetchStringWithRegex("/(?<=Generated on )(.*)(?=\n)/mi", $body));
        $card = $this->fetchStringWithRegex("/(?<=XXXX-)(\d\d\d\d)(?=\n)/mi", $body);
        $method = $this->fetchStringWithRegex("/(.*)\n(.*)XXXX-\d\d\d\d\n/mi", $body, 1);
        $merchant = $this->fetchStringWithRegex("/(?<=You made a purchase at:\n)(.*)(?=(\t| )\d)/mi", $body);

        //Set transaction values and save
        $transaction->user_id = $this->user->id;
        $transaction->source_id = $source->id;
        $transaction->status = TransactionStatus::PENDING();
        $transaction->amount = $amount;
        $transaction->currency = $currency;
        $transaction->payment_date = $paymentDate;
        $transaction->card = $card;
        $transaction->method = $method;
        $transaction->merchant = $merchant;
        $transaction->to = $merchant;
        $transaction->from = $card;
        $transaction->memo = $memo;
        $transaction->email_id = $this->email->id;
        $transaction->uuid = Str::uuid();
        $transaction->save();

        //Return success message
        Log::info('Created transaction, adding to transfer queue.', ['transaction' => $transaction]);

        //Send transaction to transfer job
        TransferTransactions::dispatch($transaction, Str::uuid());
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
