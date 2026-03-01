<?php

use Livewire\Volt\Component;
use App\Models\Account;

new class extends Component {
    public $name;
    public $type = 'cash';
    public $balance = 0;
    public $accountId;
    public $isEdit = false;
    public $showDeleteModal = false;
    public $accountIdToDelete;
    public $showMobileForm = false;

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
        $this->showMobileForm = true;
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

        $this->reset(['name', 'type', 'balance', 'accountId', 'isEdit', 'showMobileForm']);
    }

    public function confirmDelete($id)
    {
        $this->accountIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        \Illuminate\Support\Facades\Log::info('Deleting account', [
            'user_id' => auth()->id(),
            'account_id' => $this->accountIdToDelete
        ]);

        Account::where('user_id', auth()->id())
            ->findOrFail($this->accountIdToDelete)
            ->delete();

        $this->showDeleteModal = false;
        $this->reset('accountIdToDelete');
    }

    public function getAccountsProperty()
    {
        return Account::where('user_id', auth()->id())->latest()->get();
    }
}; ?>

<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <div class="flex flex-row items-center justify-between gap-4 mb-8 md:mb-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Akun</h2>
            <p class="hidden md:block text-slate-500 mt-1">Kelola sumber dana dan pantau saldo Anda di sini.</p>
        </div>
        <button wire:click="$toggle('showMobileForm')" class="lg:hidden flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2.5 rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all">
            @if($showMobileForm)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-sm">Tutup</span>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm">Tambah</span>
            @endif
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Section -->
        <div class="lg:col-span-4 {{ $showMobileForm ? 'block' : 'hidden lg:block' }} sticky top-24 z-20 overflow-visible">
            <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 p-6 md:p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Akun' : 'Akun Baru' }}</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Akun</label>
                        <input type="text" wire:model="name" placeholder="misal: Bank BCA, Cash"
                               class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700 placeholder:text-slate-300">
                        @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe</label>
                        <select wire:model="type" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="bank">Bank</option>
                            <option value="e-wallet">E-Wallet</option>
                            <option value="other">Lainnya</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Saldo Awal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="number" wire:model="balance" placeholder="0"
                                   class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 pl-12 text-slate-700 placeholder:text-slate-300 font-bold">
                        </div>
                        @error('balance') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        @if($isEdit)
                            <button wire:click="update"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Update Akun
                            </button>
                            <button wire:click="$set('isEdit', false)"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 p-4 rounded-2xl font-bold transition-all active:scale-[0.98]">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Simpan Akun
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($this->accounts as $account)
                    <div class="group bg-white hover:bg-indigo-600/5 rounded-3xl p-6 border border-slate-100 transition-all duration-300 relative overflow-hidden flex flex-col justify-between min-h-[180px]">
                        <!-- Background Accent -->
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-extrabold uppercase tracking-widest rounded-full border border-indigo-100/50">
                                    {{ $account->type }}
                                </span>
                                <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 transition-transform duration-300">
                                    <button wire:click="edit({{ $account->id }})" class="p-2 bg-white text-slate-400 hover:text-indigo-600 rounded-xl shadow-sm border border-slate-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $account->id }})" class="p-2 bg-white text-slate-400 hover:text-red-600 rounded-xl shadow-sm border border-slate-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800 tracking-tight group-hover:text-indigo-700 transition-colors">{{ $account->name }}</h4>
                        </div>

                        <div class="mt-8 relative">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Saldo Terkini</p>
                            <p class="text-2xl font-black text-slate-900 tracking-tight">
                                <span class="text-sm font-bold text-slate-400 mr-0.5">Rp</span>{{ number_format($account->balance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100 flex flex-col items-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <p class="text-slate-400 font-bold tracking-tight">Belum ada akun terdaftar.</p>
                        <p class="text-slate-300 text-sm mt-1">Gunakan formulir di samping untuk menambahkan akun pertama Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-modal wire:model="showDeleteModal" maxWidth="md">
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </x-slot>
        <x-slot name="title">Hapus Akun</x-slot>
        
        <p class="text-lg">Apakah Anda yakin ingin menghapus akun ini?</p>
        <p class="mt-2 text-sm opacity-80">Data transaksi yang terkait dengan akun ini akan kehilangan referensinya. Tindakan ini tidak dapat dibatalkan.</p>
        
        <x-slot name="footer">
            <button wire:click="delete" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-red-100 active:scale-95">
                Ya, Hapus Akun
            </button>
            <button wire:click="$set('showDeleteModal', false)" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-600 px-8 py-3 rounded-2xl font-bold transition-all active:scale-95">
                Batal
            </button>
        </x-slot>
    </x-modal>
</div>
