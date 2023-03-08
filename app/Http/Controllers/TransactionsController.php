<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Jobs\ParseEmail;
use App\Models\InboundEmail;
use App\Models\Source;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $user = $request->user();
        $search = $request->query('search');
        $source = $request->query('source');

        $transactions = Transaction::where('user_id', $user->id)
            ->with('source:id,name,identifier')
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
            ->orderBy('booking_date', 'desc');


        return Inertia::render('Transactions', [
            'transactions' => $transactions->paginate(50)->onEachSide(1)->withQueryString(),
            'transactionStatuses' => TransactionStatus::asArray(),
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
