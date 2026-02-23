<?php

use Livewire\Volt\Component;
use App\Models\Account;

new class extends Component {
    public $name;
    public $type = 'cash';
    public $balance = 0;
    public $accountId;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'balance' => 'required|numeric',
    ];

    public function store()
    {
        $this->validate();

        Account::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'type' => $this->type,
            'balance' => $this->balance,
        ]);

        $this->reset(['name', 'type', 'balance']);
    }

    public function edit($id)
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);

        $this->accountId = $account->id;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->balance = $account->balance;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        Account::where('user_id', auth()->id())
            ->findOrFail($this->accountId)
            ->update([
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
            ]);

        $this->reset(['name', 'type', 'balance', 'accountId', 'isEdit']);
    }

    public function delete($id)
    {
        Account::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function getAccountsProperty()
    {
        return Account::where('user_id', auth()->id())->latest()->get();
    }
}; ?>

<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Akun</h2>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">{{ $isEdit ? 'Edit Akun' : 'Akun Baru' }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Akun</label>
                <input type="text" wire:model="name" placeholder="misal: Bank BCA, Cash"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <select wire:model="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="bank">Bank</option>
                    <option value="e-wallet">E-Wallet</option>
                    <option value="other">Lainnya</option>
                </select>
                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Awal (Rp)</label>
                <input type="number" wire:model="balance" placeholder="0"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                @error('balance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            @if($isEdit)
                <button wire:click="update"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    Update Akun
                </button>
                <button wire:click="$set('isEdit', false)"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold transition-colors">
                    Batal
                </button>
            @else
                <button wire:click="store"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    Simpan Akun
                </button>
            @endif
        </div>
    </div>

    <!-- List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($this->accounts as $account)
            <div class="bg-white shadow-md rounded-xl p-5 border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $account->type }}</span>
                        <div class="flex space-x-2">
                            <button wire:click="edit({{ $account->id }})" class="text-indigo-600 hover:text-indigo-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $account->id }})" wire:confirm="Hapus akun ini?" class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">{{ $account->name }}</h4>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Saldo saat ini</p>
                    <p class="text-2xl font-extrabold text-indigo-600">Rp {{ number_format($account->balance, 0, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500">Belum ada akun terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
