<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;

class TransactionService
{
    /**
     * Update account balance after creating a transaction.
     */
    public function afterCreate(Transaction $transaction)
    {
        $account = Account::find($transaction->account_id);
        if ($account) {
            if ($transaction->type === 'income') {
                $account->increment('balance', $transaction->amount);
            } else {
                $account->decrement('balance', $transaction->amount);
            }
        }
    }

    /**
     * Update account balance before updating a transaction.
     * This reverts the old transaction's effect.
     */
    public function beforeUpdate(Transaction $transaction)
    {
        $account = Account::find($transaction->account_id);
        if ($account) {
            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } else {
                $account->increment('balance', $transaction->amount);
            }
        }
    }

    /**
     * Update account balance after updating a transaction.
     */
    public function afterUpdate(Transaction $transaction)
    {
        $this->afterCreate($transaction);
    }

    /**
     * Update account balance when a transaction is deleted.
     */
    public function beforeDelete(Transaction $transaction)
    {
        $this->beforeUpdate($transaction);
    }
}
