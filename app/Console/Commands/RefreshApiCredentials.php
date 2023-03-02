<?php

namespace App\Console\Commands;

use App\Models\ApiCredential;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshApiCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apicredentials:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring API credentials, and refresh them if necessary.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logMessage = 'Refreshing API credentials.';
        Log::info($logMessage);
        $this->info($logMessage);

        //Get active API credentials that expire in the next hour
        $expiringApiCredentials = ApiCredential::where('active', true)->where('expires_at', '<', now()->addHour())->get();

        //If no credentials expire in the next hour, exit
        if($expiringApiCredentials->count() === 0) {
            $logMessage = 'No credentials to refresh.';
            Log::info($logMessage);
            $this->info($logMessage);
            return 0;
        }

        foreach($expiringApiCredentials as $apiCredential) {
            $logMessage = 'Refreshing API credential with ID ' . $apiCredential->id . '.';
            Log::info($logMessage);
            $this->info($logMessage);

            //Refresh API credential
            $refreshToken = Crypt::decryptString($apiCredential->token2);

            //Use refresh token to get new access token
            $response = Http::post('https://app.youneedabudget.com/oauth/token', [
                'client_id' => env('YNAB_CLIENT_ID'),
                'client_secret' => env('YNAB_CLIENT_SECRET'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            ]);

            $accessToken = $response->json()['access_token'];
            $expires_at = Carbon::now()->addSeconds($response->json()['expires_in']);

            $apiCredential->token1 = Crypt::encryptString($accessToken);
            $apiCredential->expires_at = $expires_at;
            $apiCredential->active = true;
            $apiCredential->save();

            $logMessage = 'Successfully refreshed API credential with ID ' . $apiCredential->id . '.';
            Log::info($logMessage);
            $this->info($logMessage);
        }

        return true;
    }
}
