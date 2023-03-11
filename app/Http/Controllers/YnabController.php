<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\TransactionStatus;
use App\Enums\TransferStatus;
use App\Models\Destination;
use App\Models\Transaction;
use App\Models\TransferLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YnabController extends Controller
{
    public function getOauthUrl() {
        $url = 'https://app.youneedabudget.com/oauth/authorize?client_id=' . env('YNAB_CLIENT_ID') . '&redirect_uri=' . route('connect.budgets.callback', ['budgetType' => BudgetType::YNAB()]) . '&response_type=code';
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got OAuth URL.', 'url' => $url], 201);
    }

    public function getAccessToken($authCode) {
        $response = Http::post('https://api.youneedabudget.com/oauth/token', [
            'client_id' => env('YNAB_CLIENT_ID'),
            'client_secret' => env('YNAB_CLIENT_SECRET'),
            'redirect_uri' => route('connect.budgets.callback', ['budgetType' => BudgetType::YNAB()]),
            'grant_type' => 'authorization_code',
            'code' => $authCode
        ]);

        if($response->successful()) {
            return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.', 'accessToken' => $response->json()['access_token'], 'refreshToken' => $response->json()['refresh_token'], 'expiresIn' => $response->json()['expires_in']], 201);
        } else {
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Failed to authenticate with YNAB.', 'response' => $response->json()], 400);
        }
    }

    public function fetchBudget($user, $accessToken): \Illuminate\Http\JsonResponse
    {
        //Get selected budget
        $budgetObj = Http::withToken($accessToken)->get('https://api.youneedabudget.com/v1/budgets/default');

        //Check if successful response and contains budget
        if($budgetObj->successful() && $budgetObj['data']['budget']['id']) {
            $budgetId = $budgetObj->json()['data']['budget']['id'];
            $budgetName = $budgetObj->json()['data']['budget']['name'];

            $destinations = [];

            // loop through each account
            foreach($budgetObj->json()['data']['budget']['accounts'] as $account) {
                $accountId = $account['id'];
                $accountName = $account['name'];
                $accountClosed = $account['closed'];

                //Create destination for each budget account if not closed
                if($accountClosed == false) {
                    $destination = Destination::updateOrCreate([
                        'user_id' => $user->id,
                        'type' => BudgetType::YNAB,
                        'identifier' => $budgetId,
                        'account_identifier' => $accountId,
                    ],[
                        'user_id' => $user->id,
                        'type' => BudgetType::YNAB,
                        'identifier' => $budgetId,
                        'account_identifier' => $accountId,
                        'name' => $budgetName,
                        'account_name' => $accountName,
                    ]);

                    $destinations[] = $destination;
                }
            }
        } else {
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Failed to get budget.', 'response' => $budgetObj], 400);
        }

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.', 'destinations' => $destinations], 201);
    }

    public function getUserId($accessToken) {
        //Get YNAB user id
        $ynabUserObj = Http::withToken($accessToken)->get('https://api.youneedabudget.com/v1/user');
        //Check if successful response
        if($ynabUserObj->failed()) {
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Failed to get YNAB user.'], 400);

        }
        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got YNAB user.', 'user_id' => $ynabUserObj->json()['data']['user']['id']], 201);
    }

    public function createTransactions($transactions, $budgetId, $apiCredential) {
        $accessToken = Crypt::decryptString($apiCredential->token1);

        // get budget by YNAB id
        $budget = Destination::where('identifier', $budgetId)->firstOrFail();

        $response = Http::withToken($accessToken)->post('https://api.youneedabudget.com/v1/budgets/' . $budgetId . '/transactions', [
            'transactions' => $transactions,
        ]);

        if($response->successful()) {
            $transactions = $response->json()['data']['transactions'];
            $duplicateImportIds = $response->json()['data']['duplicate_import_ids'];

            //Update transactions status
            foreach($transactions as $ynabTransaction) {
                $transaction = Transaction::where('uuid', $ynabTransaction['import_id'])->where('user_id', $apiCredential->user->id)->first();
                $transaction->status = TransactionStatus::TRANSFERRED();
                $transaction->save();

                // create transfer log
                $transferLog = new TransferLog();
                $transferLog->user_id = $apiCredential->user->id;
                $transferLog->source_id = $transaction->source_id;
                $transferLog->transaction_id = $transaction->id;
                $transferLog->status_code = TransferStatus::SUCCESS();
                $transferLog->status_message = 'Transaction transferred to account "' . $budget->account_name .'" in budget "'. $budget->name . '".';
                $transferLog->save();
            }

            //Update transactions status for duplicates
            foreach($duplicateImportIds as $importId) {
                $transaction = Transaction::where('uuid', $importId)->where('user_id', $apiCredential->user->id)->first();
                $transaction->status = TransactionStatus::FAILED_DUPLICATE();
                $transaction->save();

                // create transfer log
                $transferLog = new TransferLog();
                $transferLog->user_id = $apiCredential->user->id;
                $transferLog->source_id = $transaction->source_id;
                $transferLog->transaction_id = $transaction->id;
                $transferLog->status_code = TransferStatus::WARNING();
                $transferLog->status_message = 'Tried to transfer transaction to account "' . $budget->account_name .'" in budget "'. $budget->name . '", but YNAB reported it as a duplicate.';
                $transferLog->save();
            }

        } else {
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Failed to transfer transactions to YNAB.', 'response' => $transactions], 400);
        }

        return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Transferred transactions to YNAB.', 'response' => $transactions], 400);
    }

    public function checkAndRefreshAccessToken($apiCredential) {
        //Check if access token is expired
        if($apiCredential->expires_at > now()) {
            return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Access token is still valid.'], 201);
        }
        // refresh YNAB access token
        $response = Http::post('https://api.youneedabudget.com/oauth/token?client_id='. env('YNAB_CLIENT_ID') .'&client_secret='. env('YNAB_CLIENT_SECRET') .'&grant_type=refresh_token&refresh_token='. Crypt::decryptString($apiCredential->token2));

        // update ApiCredential
        $apiCredential->token1 = Crypt::encryptString($response->json()['access_token']);
        $apiCredential->token2 = Crypt::encryptString($response->json()['refresh_token']);
        $apiCredential->expires_at = now()->addSeconds($response->json()['expires_in']);
        $apiCredential->save();

        if($response->successful()) {
            return response()->json(['status' => 'success', 'statusTitle' => 'Success', 'statusMessage' => 'Got access token.'], 201);
        } else {
            return response()->json(['status' => 'error', 'statusTitle' => 'Error', 'statusMessage' => 'Failed to authenticate with YNAB.', 'response' => $response->json()], 400);
        }
    }
}
