<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Account;

new class extends Component {
    public $amount;
    public $type = 'expense';
    public $category_id;
    public $account_id;
    public $description;
    public $date;
    public $transactionId;
    public $isEdit = false;
    public $showDeleteModal = false;
    public $transactionIdToDelete;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'type' => 'required|in:income,expense',
        'category_id' => 'required|exists:categories,id',
        'account_id' => 'required|exists:accounts,id',
        'date' => 'required|date',
        'description' => 'nullable|string|max:255',
    ];

    public $showMobileForm = false;

    public function mount()
    {
        $this->date = date('Y-m-d');
        // Set default category and account if exists
        $firstCategory = Category::where('user_id', auth()->id())->first();
        if ($firstCategory) {
            $this->category_id = $firstCategory->id;
        }

        $firstAccount = Account::where('user_id', auth()->id())->first();
        if ($firstAccount) {
            $this->account_id = $firstAccount->id;
        }
    }

    public function store()
    {
        $this->validate();

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
        ]);

        // Update Account Balance
        if ($this->account_id) {
            $account = Account::find($this->account_id);
            if ($account) {
                if ($this->type === 'income') {
                    $account->increment('balance', $this->amount);
                } else {
                    $account->decrement('balance', $this->amount);
                }
            }
        }

        $this->reset(['amount', 'description', 'showMobileForm']);
        $this->date = date('Y-m-d');
    }

    public function edit($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);

        $this->transactionId = $transaction->id;
        $this->amount = $transaction->amount;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->account_id = $transaction->account_id;
        $this->description = $transaction->description;
        $this->date = $transaction->date;
        $this->isEdit = true;
        $this->showMobileForm = true;
    }

    public function update()
    {
        $this->validate();

        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($this->transactionId);
        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;
        $oldAccountId = $transaction->account_id;

        // Revert old balance
        if ($oldAccountId) {
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                if ($oldType === 'income') {
                    $oldAccount->decrement('balance', $oldAmount);
                } else {
                    $oldAccount->increment('balance', $oldAmount);
                }
            }
        }

        // Update transaction
        $transaction->update([
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
        ]);

        // Apply new balance
        if ($this->account_id) {
            $newAccount = Account::find($this->account_id);
            if ($newAccount) {
                if ($this->type === 'income') {
                    $newAccount->increment('balance', $this->amount);
                } else {
                    $newAccount->decrement('balance', $this->amount);
                }
            }
        }

        $this->reset(['amount', 'description', 'transactionId', 'isEdit', 'showMobileForm']);
        $this->date = date('Y-m-d');
    }

    public function confirmDelete($id)
    {
        $this->transactionIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($this->transactionIdToDelete);
        
        // Revert balance before deleting
        if ($transaction->account_id) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                if ($transaction->type === 'income') {
                    $account->decrement('balance', $transaction->amount);
                } else {
                    $account->increment('balance', $transaction->amount);
                }
            }
        }

        $transaction->delete();
        $this->showDeleteModal = false;
        $this->reset('transactionIdToDelete');
    }

    public function getTransactionsProperty()
    {
        return Transaction::where('user_id', auth()->id())
            ->with(['category', 'account'])
            ->latest('date')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return Category::where('user_id', auth()->id())->get();
    }

    public function getAccountsProperty()
    {
        return Account::where('user_id', auth()->id())->get();
    }
}; ?>

<div class="max-w-6xl mx-auto px-2 sm:px-0">
    <div class="flex flex-row items-center justify-between gap-4 mb-8 md:mb-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Transaksi</h2>
            <p class="hidden md:block text-slate-500 mt-1">Lacak pemasukan dan pengeluaran Anda secara rinci.</p>
        </div>
        <button wire:click="$toggle('showMobileForm')" class="xl:hidden flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2.5 rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all">
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
        <div class="lg:col-span-12 xl:col-span-4 {{ $showMobileForm ? 'block' : 'hidden xl:block' }} sticky top-24 z-20">
            <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 p-6 md:p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Transaksi' : 'Transaksi Baru' }}</h3>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jumlah (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" wire:model="amount" placeholder="0"
                                       class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 pl-12 text-slate-700 font-bold text-lg">
                            </div>
                            @error('amount') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe</label>
                            <select wire:model="type" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700 font-medium">
                                <option value="expense">Pengeluaran</option>
                                <option value="income">Pemasukan</option>
                            </select>
                            @error('type') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal</label>
                            <input type="date" wire:model="date"
                                   class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700">
                            @error('date') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kategori</label>
                        <select wire:model="category_id" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700">
                            <option value="">Pilih Kategori</option>
                            @foreach($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Akun</label>
                        <select wire:model="account_id" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700">
                            <option value="">Pilih Akun</option>
                            @foreach($this->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('account_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Keterangan</label>
                        <input wire:model="description" placeholder="Catatan transaksi..."
                               class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700 placeholder:text-slate-300 font-medium">
                        @error('description') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        @if($isEdit)
                            <button wire:click="update"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Update Transaksi
                            </button>
                            <button wire:click="$set('isEdit', false)"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 p-4 rounded-2xl font-bold transition-all active:scale-[0.98]">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Simpan Transaksi
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-12 xl:col-span-8">
            <!-- Mobile Card List -->
            <div class="xl:hidden space-y-4 mb-10">
                @forelse($this->transactions as $transaction)
                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-slate-50 relative overflow-hidden" 
                                 style="background-color: {{ $transaction->category?->color ?? '#cbd5e1' }}20; color: {{ $transaction->category?->color ?? '#64748b' }}">
                                <span class="relative z-10">{{ $transaction->category?->icon ?? '📁' }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $transaction->description ?: ($transaction->category?->name ?? 'Tanpa Keterangan') }}</h4>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">{{ \Carbon\Carbon::parse($transaction->date)->format('d M') }}</span>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $transaction->account?->name ?? 'Tunai' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end space-y-2">
                            <span class="text-sm font-black {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                            <div class="flex space-x-1 opacity-100 transition-opacity">
                                <button wire:click="edit({{ $transaction->id }})" class="p-1.5 text-slate-300 hover:text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $transaction->id }})" class="p-1.5 text-slate-300 hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center bg-white rounded-3xl border-2 border-dashed border-slate-100">
                        <p class="text-slate-400 font-bold text-sm">Belum ada transaksi.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table List -->
            <div class="hidden xl:block bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Tanggal</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Keterangan & Detail</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 text-right">Jumlah</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($this->transactions as $transaction)
                                <tr class="group hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($transaction->date)->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-slate-800 text-sm mb-2">{{ $transaction->description ?: 'Tanpa keterangan' }}</div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider" 
                                                  style="background-color: {{ $transaction->category?->color ?? '#cbd5e1' }}20; color: {{ $transaction->category?->color ?? '#64748b' }}; border: 1px solid {{ $transaction->category?->color ?? '#cbd5e1' }}40">
                                                {{ $transaction->category?->name ?? 'Tanpa Kategori' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-slate-400 border border-slate-100 bg-slate-50">
                                                {{ $transaction->account?->name ?? 'Tanpa Akun' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right whitespace-nowrap">
                                        <div class="text-base font-black {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button wire:click="edit({{ $transaction->id }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-xl transition-all border border-transparent hover:border-slate-100 hover:shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $transaction->id }})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-white rounded-xl transition-all border border-transparent hover:border-slate-100 hover:shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-slate-400 font-bold tracking-tight">Belum ada transaksi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
        <x-slot name="title">Hapus Transaksi</x-slot>
        
        <p class="text-lg">Apakah Anda yakin ingin menghapus transaksi ini?</p>
        <p class="mt-2 text-sm opacity-80">Saldo akun Anda akan disesuaikan kembali secara otomatis. Tindakan ini tidak dapat dibatalkan.</p>
        
        <x-slot name="footer">
            <button wire:click="delete" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-red-100 active:scale-95">
                Ya, Hapus Transaksi
            </button>
            <button wire:click="$set('showDeleteModal', false)" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-600 px-8 py-3 rounded-2xl font-bold transition-all active:scale-95">
                Batal
            </button>
        </x-slot>
    </x-modal>
</div>
