<?php

namespace App\Http\Controllers;

use App\Enums\ApiCredentialType;
use App\Enums\SourceType;
use App\Enums\TransferStatus;
use App\Models\ApiCredential;
use App\Models\Source;
use App\Models\Transaction;
use App\Models\TransferLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TinkController extends Controller
{
    public function createUser($user, $countryCode): JsonResponse
    {
        if(ApiCredential::where('user_id', $user->id)->where('source_type', SourceType::Tink)->exists()) {
            return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'API credentials already exist for this source.'], 204);
        } else {
            // authorize app
            $response = Http::asForm()
                ->post('https://api.tink.com/api/v1/oauth/token', [
                    'client_id' => env('TINK_CLIENT_ID'),
                    'client_secret' => env('TINK_CLIENT_SECRET'),
                    'grant_type' => 'client_credentials',
                    'scope' => 'user:create'
                ]);

            if ($response->failed()) {
                Log::error('Unable to get authorize application.', ['response' => $response]);
                return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get authorize application.'], 401);
            }

            // get the access token
            $accessToken = $response->json()['access_token'];


            // create a new API credential
            $apiCredential = new ApiCredential();
            $apiCredential->user_id = $user->id;
            $apiCredential->type = ApiCredentialType::OAuth;
            $apiCredential->source_type = SourceType::Tink;

            // create a new user
            $response = Http::withToken($accessToken)
                ->post('https://api.tink.com/api/v1/user/create', [
                    'external_user_id' => $user->uuid,
                    'market' => $countryCode,
                    'locale' => 'en_US',
                ]);

            if ($response->failed()) {
                Log::error('Unable to create Tink user.', ['response' => $response]);
                return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to create Tink user.'], 401);
            }

            // get the Tink user ID
            $tinkUserId = $response->json()['user_id'];

            // set API credential identifier to Tink user ID
            $apiCredential->identifier = $tinkUserId;
            $apiCredential->save();
        }
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'API credentials and Tink user created.'], 201);
    }

    public function getOauthUrl($user, $countryCode): JsonResponse
    {
        // authorize app with authorization:grant scope
        $response = $this->generateClientAuthCode('client_credentials', 'authorization:grant')->getData();
        if ($response->status == 'error') {
            Log::error('Unable to authorize application.', ['response' => $response]);
            return $response;
        }
        $clientAccessToken = $response->access_token;

        // get API credentials for Tink, set token3 to the access token
        $apiCredential = ApiCredential::where('user_id', $user->id)->where('source_type', SourceType::Tink)->first();

        // grant user access
        $response = Http::asForm()->withToken($clientAccessToken)
            ->post('https://api.tink.com/api/v1/oauth/authorization-grant/delegate', [
                'actor_client_id' => 'df05e4b379934cd09963197cc855bfe9',
                'user_id' => $apiCredential->identifier,
                'id_hint' => $user->email,
                'scope' => 'authorization:read,authorization:grant,credentials:refresh,credentials:read,credentials:write,providers:read,user:read',
            ]);
        if ($response->failed()) {
            Log::error('Unable to get user authorization code.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get user authorization code.'], 401);
        }

        // get user auth code
        $userAuthCode = $response->json()['code'];

        // build & return URL
        $url = 'https://link.tink.com/1.0/transactions/connect-accounts?client_id=' . env('TINK_CLIENT_ID') . '&redirect_uri=http://localhost/connect/sources/1/callback&authorization_code=' . $userAuthCode . '&market=' . $countryCode . '&locale=en_US';

        return response()->json(['status' => 'success','statusTitle' => 'Success',  'statusMessage' => $url], 200);
    }

    public function refreshConsent($user, $apiCredential, $reauthenticate = false): JsonResponse
    {
        // get access token
        $accessTokenObj = $this->getAccessToken($user, $apiCredential)->getData();
        $accessToken = $accessTokenObj->accessToken;

        if($accessTokenObj->status == 'error') {
            Log::error('Unable to get access token.', ['response' => $accessTokenObj]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get access token.'], 401);
        }

        $response = Http::withToken($accessToken)
            ->post('https://api.tink.com/api/v1/credentials/'. $apiCredential->identifier2 .'/refresh', [
                'appUri' => env('APP_URL'),
                'callbackUri' => 'http://localhost/connect/sources/1/callback',
                'userAvailability' => [
                    'userAvailableForInteraction' => $reauthenticate,
                    'userPresent' => $reauthenticate
                ]
            ]);

        if ($response->failed()) {
            Log::error('Unable to refresh consent.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to refresh consent.'], 401);
        }

        // return the access token
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Refreshed consent.'], 200);
    }

    public function pollConsent($user, $apiCredential): JsonResponse
    {
        // get access token
        $accessTokenObj = $this->getAccessToken($user, $apiCredential)->getData();
        $accessToken = $accessTokenObj->accessToken;

        if($accessTokenObj->status == 'error') {
            Log::error('Unable to get access token.', ['response' => $accessTokenObj]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get access token.'], 401);
        }

        $response = $this->checkConsent($apiCredential, $accessToken)->getData();

        if($response->status == 'error') {
            Log::error('Unable to poll for changes in consent.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to poll for changes in consent.'], 401);
        }

        // return the access token
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Refreshed consent.'], 200);
    }

    private function checkConsent($apiCredential, $accessToken): JsonResponse
    {
        sleep(1);

        // get provider consent
        $response = Http::withToken($accessToken)
            ->get('https://api.tink.com/api/v1/provider-consents', [
                'credentialsId' => $apiCredential->identifier2,
            ]);

        // handle error
        if ($response->failed()) {
            Log::error('Unable to poll for consents.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to poll for consents.'], 401);
        }

        // get provider consent
        $providerConsent = $response->json()['providerConsents'][0];

        Log::info('Checking consent', $providerConsent);

        // if consent is not updated, check again
        if ($providerConsent['status'] == 'UPDATED') {

        } else {
            $this->checkConsent($apiCredential, $accessToken);
            exit;
        }

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Refreshed consent.'], 200);
    }

    private function generateClientAuthCode($grantType, $scope): JsonResponse {
        $response = Http::asForm()
            ->post('https://api.tink.com/api/v1/oauth/token', [
                'client_id' => env('TINK_CLIENT_ID'),
                'client_secret' => env('TINK_CLIENT_SECRET'),
                'grant_type' => $grantType,
                'scope' => $scope,
            ]);

        if ($response->failed()) {
            Log::error('Unable to authorize application.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to authorize application.'], 401);

        }

        // return the access token
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.', 'access_token' => $response->json()['access_token']], 200);
    }

    /**
     * Get the access token for the user.
     * Requires client auth code with scope: authorization:grant.
     * @param $user
     * @param $apiCredential
     * @return JsonResponse
     */
    public function getAccessToken($user, $apiCredential): JsonResponse
    {
        // check if ApiCredential has expired, return access token if not
        $expiryDate = Carbon::parse($apiCredential->expires_at);
        if (!$expiryDate->isPast()) {
            // get the access token from the ApiCredential
            $accessToken = Crypt::decryptString($apiCredential->token1);
            return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.', 'accessToken' => $accessToken], 200);
        } else {
            Log::info('ApiCredential is expired, getting new.', ['ApiCredential' => $apiCredential]);
        }

        // generate new auth code with scope: authorization:grant
        $response = $this->generateClientAuthCode('client_credentials', 'authorization:grant')->getData();
        if ($response->status == 'error') {
            Log::error('Unable to authorize application.', ['response' => $response]);
            return $response;
        }
        $clientAccessToken = $response->access_token;

        // get user auth code
        $response = Http::asForm()->withToken($clientAccessToken)
            ->post('https://api.tink.com/api/v1/oauth/authorization-grant', [
                'user_id' => $apiCredential->identifier,
                'scope' => 'accounts:read,balances:read,transactions:read,provider-consents:read,credentials:refresh',
            ]);

        if ($response->failed()) {
            Log::error('Unable to get user authorization code.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get user authorization code.'], 400);
        }

        // get user auth code
        $userAuthCode = $response->json()['code'];

        // use user auth code to get user access token
        $response = Http::asForm()
            ->post('https://api.tink.com/api/v1/oauth/token', [
                'code' => $userAuthCode,
                'client_id' => env('TINK_CLIENT_ID'),
                'client_secret' => env('TINK_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
            ]);
        if ($response->failed()) {
            Log::error('Unable to get user access token.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get user access token.'], 400);
        }

        // get the access & refresh tokens, and expiry time
        $accessToken = $response->json()['access_token'];
        $expiresIn = $response->json()['expires_in'];

        // set the access & refresh tokens, and expiry time in the API credential
        $apiCredential->token1 = Crypt::encryptString($accessToken);
        $apiCredential->expires_at = Carbon::now()->addSeconds($expiresIn);
        $apiCredential->active = true;
        $apiCredential->save();

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.', 'accessToken' => $accessToken], 200);
    }

    public function fetchAccounts($userId, $apiCredential, $source = null): JsonResponse
    {
        $user = User::findOrFail($userId);

        // get access token
        $accessToken = $this->getAccessToken($user, $apiCredential)->getData()->accessToken;

        // get accounts with access token
        if($source) {
            $response = Http::withToken($accessToken)
                ->get('https://api.tink.com/data/v2/accounts', [
                    'idIn' => $source->external_id,
                ]);
        } else {
            $response = Http::withToken($accessToken)
                ->get('https://api.tink.com/data/v2/accounts');
        }


        if ($response->failed()) {
            Log::error('Unable to get bank accounts.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get bank accounts.'], 400);
        }

        $accounts = $response->json()['accounts'];

        // loop through accounts, create sources if they don't exist
        foreach ($accounts as $account) {
            // check if source exists
            $source = Source::where('user_id', $userId)->where('type', SourceType::Tink)->where('identifier', $account['identifiers']['iban']['bban'])->first();
            if($source == null) {
                // create source
                $source = new Source();
                $source->user_id = $userId;
                $source->type = SourceType::Tink;
                $source->external_id = $account['id'];
                $source->name = $account['name'];
                $source->provider = $account['financialInstitutionId'];
                $source->last_synced = Carbon::parse($account['dates']['lastRefreshed']);
                $source->identifier = $account['identifiers']['iban']['bban'];
                $source->active = false;
                $source->save();
            } else {
                // update source
                $source->name = $account['name'];
                $source->last_synced = Carbon::parse($account['dates']['lastRefreshed']);
                $source->save();
            }
        }

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Accounts fetched.'], 200);
    }

    public function fetchTransactions($userId, $source, $startDate, $nextPageToken = null): JsonResponse
    {
        //TODO: Add consent check, prompt user to re-consent if needed

        // check if source is active
        if(!$source->active) {
            return response()->json([
                'statusTitle' => 'Error',
                'statusMessage' => 'The source has not been activated.',
                'status' => 'error',
            ]);
        }

        $apiCredential = $source->user->apiCredentials()->where('active', true)->where('source_type', $source->type)->first();

        // get access token
        $accessToken = $this->getAccessToken(User::findOrFail($userId), $apiCredential)->getData()->accessToken;

        // use Carbon to format start date as YYYY-MM-DD
        $startDate = Carbon::parse($startDate)->format('Y-m-d');

        // get booked transactions from start date to today
        $response = Http::withToken($accessToken)
            ->get('https://api.tink.com/data/v2/transactions', [
                'accountIdIn' => $source->external_id,
                'bookedDateGte' => $startDate,
                'statusIn' => 'BOOKED',
                'pageToken' => $nextPageToken

            ]);

        if ($response->failed()) {
            Log::error('Unable to get transactions.', ['response' => $response]);
            return response()->json([
                'statusTitle' => 'Error',
                'statusMessage' => 'The source has been activated, but Synci could not fetch transactions at this time.',
                'status' => 'error'
            ]);
        }

        $transactionsObj = $response->json();
        $transactions = $transactionsObj['transactions'];


        foreach ($transactions as $transaction) {
            // add transaction to database
            $transaction = Transaction::firstOrCreate([
                'source_id' => $source->id,
                'user_id' => $userId,
                'external_id' => $transaction['id'],
            ],[
                'source_id' => $source->id,
                'user_id' => $userId,
                'external_id' => $transaction['id'],

                'amount' => $transaction['amount']['value']['unscaledValue'],
                'currency' => $transaction['amount']['currencyCode'],

                'description_long' => $transaction['descriptions']['original'] ?? null,
                'description_short' => $transaction['descriptions']['display'] ?? null,
                'category' => $transaction['categories']['pfm']['id'] ?? null,
                'reference' => $transaction['reference'] ?? null,

                'merchant_category' => $transaction['merchantInformation']['merchantCategoryCode'] ?? null,
                'merchant_name' => $transaction['merchantInformation']['merchantName'] ?? null,

                'payment_date' => $transaction['dates']['booked'],
                'booking_date' => $transaction['dates']['booked'],

                'uuid' => Str::uuid(),
            ]);

            // create transfer log
            if($transaction) {
                $transferLog = new TransferLog();
                $transferLog->user_id = $userId;
                $transferLog->source_id = $source->id;
                $transferLog->transaction_id = $transaction->id;
                $transferLog->status_code = TransferStatus::PENDING();
                $transferLog->status_message = 'Transaction fetched from activated source "' . $source->name . ' (' . $source->identifier . ')".';
                $transferLog->save();
            }
        }

        if(isset($transactionsObj['nextPageToken']) && $transactionsObj['nextPageToken'] != "") {
            // get booked transactions from start date to today, with next page token
            $this->fetchTransactions($userId, $source, $startDate, $transactionsObj['nextPageToken']);
            return response()->json([
                'statusTitle' => 'Getting next page',
                'statusMessage' => 'Transactions fetched, getting next page.',
                'status' => 'info'
            ]);
        }

        return response()->json([
            'statusTitle' => 'Success',
            'statusMessage' => 'Transactions fetched.',
            'status' => 'success'
        ]);
    }
}
