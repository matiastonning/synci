<?php

namespace App\Http\Controllers;

use App\Enums\ApiCredentialType;
use App\Enums\SourceType;
use App\Models\ApiCredential;
use App\Models\Source;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TinkController extends Controller
{
    public function createUser($userId, $countryCode): \Illuminate\Http\JsonResponse
    {
        if(ApiCredential::where('user_id', $userId)->where('source_type', SourceType::Tink)->exists()) {
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



            // create a new user
            $response = Http::withToken($accessToken)
                ->post('https://api.tink.com/api/v1/user/create', [
                    'external_user_id' => 'test12:'.$userId,
                    'market' => $countryCode,
                    'locale' => 'en_US',
                ]);

            if ($response->failed()) {
                Log::error('Unable to create Tink user.', ['response' => $response]);
                return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to create Tink user.'], 401);
            }

            // get the Tink user ID
            $tinkUserId = $response->json()['user_id'];

            // create a new API credential with the Tink user ID
            $apiCredential = new ApiCredential();
            $apiCredential->user_id = $userId;
            $apiCredential->type = ApiCredentialType::OAuth;
            $apiCredential->source_type = SourceType::Tink;
            $apiCredential->identifier = Crypt::encryptString($tinkUserId);
            $apiCredential->save();
        }
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'API credentials and Tink user created.'], 201);
    }

    public function getOauthUrl($user, $countryCode): \Illuminate\Http\JsonResponse
    {
        // authorize app with authorization:grant scope
        $response = Http::asForm()
            ->post('https://api.tink.com/api/v1/oauth/token', [
                'client_id' => env('TINK_CLIENT_ID'),
                'client_secret' => env('TINK_CLIENT_SECRET'),
                'grant_type' => 'client_credentials',
                'scope' => 'authorization:grant',
            ]);

        if ($response->failed()) {
            Log::error('Unable to authorize application.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to authorize application.'], 401);

        }

        // get the access token
        $accessToken2 = $response->json()['access_token'];

        // get API credentials for Tink, set token3 to the access token
        $apiCredential = ApiCredential::where('user_id', $user->id)->where('source_type', SourceType::Tink)->first();
        $apiCredential->token3 = Crypt::encryptString($accessToken2);
        $apiCredential->save();

        // grant user access
        $response = Http::asForm()->withToken($accessToken2)
            ->post('https://api.tink.com/api/v1/oauth/authorization-grant/delegate', [
                'actor_client_id' => 'df05e4b379934cd09963197cc855bfe9',
                'user_id' => Crypt::decryptString($apiCredential->identifier),
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

    public function getAccessToken($user, $apiCredential): \Illuminate\Http\JsonResponse
    {
        // get user auth code
        $response = Http::asForm()->withToken(Crypt::decryptString($apiCredential->token3))
            ->post('https://api.tink.com/api/v1/oauth/authorization-grant/delegate', [
                'actor_client_id' => env('TINK_CLIENT_ID'),
                'user_id' => Crypt::decryptString($apiCredential->identifier),
                'id_hint' => $user->email,
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
        $refreshToken = $response->json()['refresh_token'] ?? '';
        $expiresIn = $response->json()['expires_in'];

        // set the access & refresh tokens, and expiry time in the API credential
        $apiCredential->token1 = Crypt::encryptString($accessToken);
        $apiCredential->token2 = Crypt::encryptString($refreshToken);
        $apiCredential->expires_at = Carbon::now()->addSeconds($expiresIn);
        $apiCredential->active = true;
        $apiCredential->save();

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => $accessToken], 200);
    }

    public function fetchAccounts($userId, $accessToken): \Illuminate\Http\JsonResponse
    {
        // get accounts with access token
        $response = Http::withToken($accessToken)
            ->get('https://api.tink.com/data/v2/accounts');

        if ($response->failed()) {
            Log::error('Unable to get bank accounts.', ['response' => $response]);
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Unable to get bank accounts.'], 400);
        }

        $accounts = $response->json()['accounts'];

        // loop through accounts, create sources if they don't exist
        foreach ($accounts as $account) {
            // check if source exists
            $source = Source::where('user_id', $userId)->where('type', SourceType::Tink)->where('external_id', $account['id'])->first();
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

    public function fetchTransactions($userId, $source, $startDate): \Illuminate\Http\JsonResponse
    {
        // check if source is active
        if(!$source->active) {
            return response()->json([
                'statusTitle' => 'Error',
                'statusMessage' => 'The source has not been activated.',
                'status' => 'error'
            ]);
        }

        // use Carbon to format start date as YYYY-MM-DD
        $startDate = Carbon::parse($startDate)->format('Y-m-d');

        // get access token
        $accessToken = Crypt::decryptString(ApiCredential::where('user_id', $userId)->where('source_type', SourceType::Tink)->first()->token1);

        // get booked transactions from start date to today
        $response = Http::withToken($accessToken)
            ->get('https://api.tink.com/data/v2/transactions',[
                'accountIdIn' => $source->external_id,
                'bookedDateGte' => $startDate,
                'statusIn' => 'BOOKED'
            ]);

        if ($response->failed()) {
            Log::error('Unable to get transactions.', ['response' => $response]);
            return response()->json([
                'statusTitle' => 'Error',
                'statusMessage' => 'The source has been activated, but Synci could not fetch transactions at this time.',
                'status' => 'error'
            ]);
        }

        $transactions = $response->json()['transactions'];

        foreach ($transactions as $transaction) {
            // add transaction to database
            Transaction::firstOrCreate([
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
        }

        return response()->json([
            'statusTitle' => 'Success',
            'statusMessage' => 'Transactions fetched.',
            'status' => 'success'
        ]);
    }
}
