<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where('user_id', $request->user()->id)->latest()->get();
        return AccountResource::collection($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'balance' => 'required|numeric',
        ]);

        $account = Account::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
        ]);

        return new AccountResource($account);
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);
        return new AccountResource($account);
    }

    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'balance' => 'required|numeric',
        ]);

        $account->update($validated);

        return new AccountResource($account);
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    /**
     * Authorize action (Simple check for now, can be moved to Policies)
     */
    protected function authorize($action, $model)
    {
        if ($model->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
