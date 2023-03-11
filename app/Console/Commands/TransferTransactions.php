<?php

namespace App\Console\Commands;

use App\Enums\BudgetType;
use App\Enums\TransactionStatus;
use App\Http\Controllers\TransactionsController;
use App\Models\ApiCredential;
use App\Models\Destination;
use App\Models\Source;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransferTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer transactions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // get all transactions after start date, with an active source that have status pending/failed/fetched
        $transactions = Transaction::whereHas('source', function ($query) {
            $query->where('active', true)->where('start_date', '<', DB::raw('transactions.booking_date'));
        })
            ->whereHas('source.connectionLink', function ($query) {
                $query->where('active', true);
            })
            ->whereIn('status', [TransactionStatus::PENDING(), TransactionStatus::FAILED_ALL(), TransactionStatus::FAILED_SOME()]);

        // declare transactions object
        $transactionsObj = $transactions->get();

        // dispatch job
        \App\Jobs\TransferTransactions::dispatch($transactionsObj, Str::uuid());
        $this->info('TransferTransactions job dispatched');

        // update transactions status to transferring
        $transactions->update(['status' => TransactionStatus::TRANSFERRING()]);

        return 0;
    }
}
