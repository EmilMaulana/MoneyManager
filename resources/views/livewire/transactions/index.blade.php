<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use App\Models\Category;

new class extends Component {
    public $amount;
    public $type = 'expense';
    public $category_id;
    public $description;
    public $date;
    public $transactionId;
    public $isEdit = false;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'type' => 'required|in:income,expense',
        'category_id' => 'required|exists:categories,id',
        'date' => 'required|date',
        'description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->date = date('Y-m-d');
        // Set default category if exists
        $firstCategory = Category::where('user_id', auth()->id())->first();
        if ($firstCategory) {
            $this->category_id = $firstCategory->id;
        }
    }

    public function store()
    {
        $this->validate();

        Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
        ]);

        $this->reset(['amount', 'description']);
        $this->date = date('Y-m-d');
    }

    public function edit($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);

        $this->transactionId = $transaction->id;
        $this->amount = $transaction->amount;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->description = $transaction->description;
        $this->date = $transaction->date;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        Transaction::where('user_id', auth()->id())
            ->findOrFail($this->transactionId)
            ->update([
                'category_id' => $this->category_id,
                'type' => $this->type,
                'amount' => $this->amount,
                'description' => $this->description,
                'date' => $this->date,
            ]);

        $this->reset(['amount', 'description', 'transactionId', 'isEdit']);
        $this->date = date('Y-m-d');
    }

    public function delete($id)
    {
        Transaction::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function getTransactionsProperty()
    {
        return Transaction::where('user_id', auth()->id())
            ->with('category')
            ->latest('date')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return Category::where('user_id', auth()->id())->get();
    }
}; ?>

<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Transaksi</h2>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">{{ $isEdit ? 'Edit Transaksi' : 'Transaksi Baru' }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                <input type="number" wire:model="amount" placeholder="0"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <select wire:model="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select wire:model="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                    <option value="">Pilih Kategori</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" wire:model="date"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea wire:model="description" rows="2" placeholder="Catatan transaksi..."
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border"></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            @if($isEdit)
                <button wire:click="update"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    Update Transaksi
                </button>
                <button wire:click="$set('isEdit', false)"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold transition-colors">
                    Batal
                </button>
            @else
                <button wire:click="store"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors shadow-md">
                    Simpan Transaksi
                </button>
            @endif
        </div>
    </div>

    <!-- List -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Jumlah</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($this->transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="font-medium">{{ $transaction->description ?: 'Tanpa keterangan' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $transaction->category->color }}15; color: {{ $transaction->category->color }}">
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-right whitespace-nowrap {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                            <button wire:click="edit({{ $transaction->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium mr-3">Edit</button>
                            <button wire:click="delete({{ $transaction->id }})" 
                                    wire:confirm="Yakin ingin menghapus transaksi ini?"
                                    class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Belum ada transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
