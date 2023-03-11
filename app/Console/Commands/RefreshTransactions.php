<?php

namespace App\Console\Commands;

use App\Enums\SourceType;
use App\Http\Controllers\TinkController;
use App\Jobs\FetchAccounts;
use App\Jobs\FetchTransactions;
use App\Models\ApiCredential;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isNan;

class RefreshTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshing transactions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tink = new TinkController();

        //Get active sources and refresh transactions for each
        $activeSources = Source::where('active', true)->where('last_synced', '<=', Carbon::now()->subHours(0)->toDateTimeString())->get();

        //If no sources need to be refreshed, exit
        if($activeSources->count() === 0) {
            $logMessage = 'No sources to refresh.';
            $this->info($logMessage);
            return 1;
        }

        $apiCredentialsRefreshed = [];

        foreach ($activeSources as $source) {
            $logMessage = 'Refreshing accounts and transactions for source with ID ' . $source->id . '.';
            Log::info($logMessage);
            $this->info($logMessage);

            //Get API credential
            $apiCredential = $source->user->apiCredentials()->where('active', true)->where('source_type', $source->type)->first();

            // check if API credential exists
            if(!$apiCredential) {
                $logMessage = 'No active API credential found for source with ID ' . $source->id . '.';
                $this->info($logMessage);
                return 1;
            }

            /* refresh API credential consent
            if($source->type == SourceType::Tink) {
                $this->info('Refreshing consent');
                $response = $tink->refreshConsent($source->user, $apiCredential);
                if($response->original['status'] == 'error') {
                    $logMessage = $response->original['statusMessage'];
                    $this->info($logMessage);
                    return 1;
                }
            }


            // poll for consent update
            if($source->type == SourceType::Tink) {
                $this->info('Polling for consent update');
                $response = $tink->pollConsent($source->user, $apiCredential);
                if($response->original['status'] == 'error') {
                    $logMessage = $response->original['statusMessage'];
                    $this->info($logMessage);
                    return 1;
                }
            }*/

            // get most recent transaction
            $lastTransactionDate = $source->transactions()->orderBy('booking_date', 'desc')->first()->booking_date ?? $source->last_synced;

            // dispatch fetch transactions job
            FetchTransactions::dispatch($source, $lastTransactionDate, Str::uuid());
            $logMessage = 'Dispatched transaction job for source with ID ' . $source->id . '.';
            $this->info($logMessage);

            // check if API credential has already been refreshed
            if(in_array($apiCredential->id, $apiCredentialsRefreshed)) {
                $logMessage = 'API credential with ID ' . $apiCredential->id . ' has already been refreshed.';
            } else {
                // dispatch fetch accounts job
                FetchAccounts::dispatch($source, $apiCredential, Str::uuid());
                $apiCredentialsRefreshed[] = $apiCredential->id;
                $logMessage = 'Dispatched accounts job for source with ID ' . $source->id . '.';
            }

            $this->info($logMessage);
        }

        $this->info('Done.');
        return 0;
    }
}
