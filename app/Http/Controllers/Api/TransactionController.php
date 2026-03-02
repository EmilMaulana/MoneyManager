<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->with(['category', 'account'])
            ->latest('date')
            ->get();
        return TransactionResource::collection($transactions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'account_id' => $validated['account_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'date' => $validated['date'],
            ]);

            $this->transactionService->afterCreate($transaction);

            return new TransactionResource($transaction);
        });
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return new TransactionResource($transaction->load(['category', 'account']));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        return DB::transaction(function () use ($validated, $transaction) {
            $this->transactionService->beforeUpdate($transaction);
            
            $transaction->update($validated);

            $this->transactionService->afterUpdate($transaction);

            return new TransactionResource($transaction);
        });
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        DB::transaction(function () use ($transaction) {
            $this->transactionService->beforeDelete($transaction);
            $transaction->delete();
        });

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    protected function authorize($action, $model)
    {
        if ($model->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
