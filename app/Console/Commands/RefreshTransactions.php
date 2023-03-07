<?php

namespace App\Console\Commands;

use App\Enums\SourceType;
use App\Http\Controllers\TinkController;
use App\Models\ApiCredential;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $logMessage = 'Refreshing transactions.';
        Log::info($logMessage);
        $this->info($logMessage);

        //Get active sources and refresh transactions for each
        $activeSources = Source::where('active', true)->where('last_synced', '<=', Carbon::now()->subHours(6)->toDateTimeString())->get();

        //If no sources need to be refreshed, exit
        if($activeSources->count() === 0) {
            $logMessage = 'No sources to refresh.';
            $this->info($logMessage);
            return 1;
        }

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

            //Get last synced date
            $lastSynced = $source->last_synced;

            //Refresh transactions
            $response = $tink->fetchTransactions($source->user->id, $source, $lastSynced);

            //Refresh accounts
            $tink->fetchAccounts($source->user->id, $apiCredential, $source);

            $logMessage = 'Successfully refreshed transactions for source with ID ' . $source->id . '.';

            // handle error
            if($response->original['status'] == 'error') {
                $logMessage = $response->original['statusMessage'];
                $this->info($logMessage);
                return 1;
            }
        }

        $this->info($logMessage);
        return 0;
    }
}
