<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\TransactionStatus;
use App\Enums\TransferStatus;
use App\Jobs\ParseEmail;
use App\Models\Destination;
use App\Models\InboundEmail;
use App\Models\Source;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    public function transferTransactions($transactions, $destination, $apiCredential = null): \Illuminate\Http\JsonResponse
    {
        if($destination->type === BudgetType::YNAB) {
            // check if access token is valid
            $ynab = new YnabController();
            $response = $ynab->checkAndRefreshAccessToken($apiCredential);


            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return response()->json([
                    'statusTitle' => $response->original['statusTitle'],
                    'statusMessage' => $response->original['statusMessage'],
                    'status' => $response->original['status']
                ], 400);
            }

            $ynabTransactions = [];
            foreach($transactions as $transaction) {
                $ynabTransactions[] = [
                    'account_id' => $destination->account_identifier,
                    'date' => Carbon::parse($transaction->booking_date)->format('Y-m-d'),
                    'amount' => $transaction->amount * 1000,
                    'payee_name' => $transaction->description_short,
                    'memo' => $transaction->description_long,
                    'import_id' => $transaction->uuid,
                    'cleared' => 'cleared',
                    'approved' => false,
                    'flag_color' => 'green',
                ];
            }

            // create transactions in YNAB
            $budgetId = $destination->identifier;
            $ynab->createTransactions($ynabTransactions, $budgetId, $apiCredential);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return response()->json([
                    'statusTitle' => $response->original['statusTitle'],
                    'statusMessage' => $response->original['statusMessage'],
                    'status' => $response->original['status']
                ], 400);
            }
        }

        return response()->json([
            'status' => 'success',
            'statusTitle' => 'Success',
            'statusMessage' => 'Transactions transferred.',
            'destination' => $destination
        ], 201);
    }

    public function index(Request $request): \Inertia\Response
    {
        $user = $request->user();
        $search = $request->query('search');
        $source = $request->query('source');

        $transactions = Transaction::where('user_id', $user->id)
            ->with('source:id,name,identifier,start_date')
            ->with(['transferLog' => function ($query) {
                $query->select('status_code','status_message','transaction_id','created_at')->orderBy('created_at', 'DESC');
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('description_long', 'LIKE', '%'.$search.'%')
                        ->orWhere('category', 'LIKE', '%'.$search.'%')
                        ->orWhere('reference', 'LIKE', '%'.$search.'%')
                        ->orWhere('merchant_category', 'LIKE', '%'.$search.'%')
                        ->orWhere('amount', 'LIKE', '%'.$search.'%')
                        ->orWhereRelation('source', 'name', 'LIKE', '%' . $search . '%')
                        ->orWhereRelation('source', 'identifier', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($source, function ($query, $source) {
                return $query->whereRelation('source', 'name', $source);
            })
            ->whereHas('source', function ($query) {
                $query->where('start_date', '<', DB::raw('transactions.booking_date'));
            })
            ->orderBy('booking_date', 'desc');


        return Inertia::render('Transactions', [
            'transactions' => $transactions->paginate(50)->onEachSide(1)->withQueryString(),
            'transactionStatuses' => TransactionStatus::asArray(),
            'transferLogStatuses' => TransferStatus::asArray(),
            'sources' => Source::where('user_id', $user->id)->whereNotNull('name')->whereNotNull('identifier')->orderBy('created_at', 'desc')->with('connectionLink')->get(),
        ]);
    }

    public function inboundEmailHandler(Request $request): \Illuminate\Http\JsonResponse
    {
        //Define request ID for logging
        $requestId = Str::uuid();

        //Create inbound email record
        $email = InboundEmail::create([
            'to' => $request->input("to"),
            'from' => $request->input("from"),
            'subject' => $request->input("subject"),
            'body' => $request->input("text"),
            'headers' => $request->input("headers"),
            'sender_ip' => $request->input("sender_ip"),
            'uuid' => Str::uuid(),
        ]);

        //Log event
        Log::info('Job queued.', ['job' => 'ParseEmail', 'request_id' => $requestId, 'to' => $email->to, 'from' => $email->from]);

        //Dispatch email parsing job
        ParseEmail::dispatch($email, $requestId);

        //Return successful response to SendGrid
        return response()->json(["success" => true]);
    }
}
