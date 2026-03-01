<?php

use Livewire\Component;
use App\Models\Category;

new class extends Component {

    public $name;
    public $type = 'expense';
    public $color;
    public $icon;
    public $categoryId;
    public $isEdit = false;
    public $showDeleteModal = false;
    public $categoryIdToDelete;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:income,expense',
    ];

    public $showMobileForm = false;

    public function store()
    {
        $this->validate();

        Category::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'type' => $this->type,
            'color' => $this->color,
            'icon' => $this->icon,
        ]);

        $this->reset(['name','color','icon','showMobileForm']);
    }

    public function edit($id)
    {
        $category = Category::where('user_id', auth()->id())
            ->findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->color = $category->color;
        $this->icon = $category->icon;
        $this->isEdit = true;
        $this->showMobileForm = true;
    }

    public function update()
    {
        $this->validate();

        Category::where('user_id', auth()->id())
            ->findOrFail($this->categoryId)
            ->update([
                'name' => $this->name,
                'type' => $this->type,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);

        $this->reset(['name','color','icon','categoryId','isEdit', 'showMobileForm']);
        $this->type = 'expense';
    }

    public function confirmDelete($id)
    {
        $this->categoryIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Category::where('user_id', auth()->id())
            ->findOrFail($this->categoryIdToDelete)
            ->delete();

        $this->showDeleteModal = false;
        $this->reset('categoryIdToDelete');
    }

    public function getCategoriesProperty()
    {
        return Category::where('user_id', auth()->id())
            ->latest()
            ->get();
    }
};
?>

<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <div class="flex flex-row items-center justify-between gap-4 mb-8 md:mb-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Kategori</h2>
            <p class="hidden md:block text-slate-500 mt-1">Atur pengelompokan transaksi Anda agar lebih terorganisir.</p>
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
        <div class="lg:col-span-4 {{ $showMobileForm ? 'block' : 'hidden lg:block' }} sticky top-24 z-20">
            <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 p-6 md:p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Kategori' : 'Kategori Baru' }}</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Kategori</label>
                        <input type="text" wire:model="name" placeholder="misal: Makanan, Transportasi"
                               class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700 placeholder:text-slate-300">
                        @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe</label>
                        <select wire:model="type" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700">
                            <option value="expense">Pengeluaran (Expense)</option>
                            <option value="income">Pemasukan (Income)</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Warna</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" wire:model="color"
                                       class="w-12 h-12 bg-slate-50 border-none rounded-xl cursor-pointer p-1">
                                <span class="text-xs font-mono text-slate-400">{{ $color ?: '#000000' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Icon (Emoji)</label>
                            <input type="text" wire:model="icon" placeholder="🍔"
                                   class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all p-4 text-slate-700 text-center text-xl">
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        @if($isEdit)
                            <button wire:click="update"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Update Kategori
                            </button>
                            <button wire:click="$set('isEdit', false)"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 p-4 rounded-2xl font-bold transition-all active:scale-[0.98]">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-[0.98]">
                                Tambah Kategori
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center px-8">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Kategori</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase bg-white px-2 py-0.5 rounded-full border border-slate-100">{{ count($this->categories) }} Total</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($this->categories as $category)
                        <div class="group flex items-center justify-between p-6 px-8 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center space-x-5">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-slate-100/50 relative overflow-hidden" 
                                     style="background-color: {{ $category->color }}10;">
                                    <div class="absolute inset-0 opacity-20" style="background-color: {{ $category->color }};"></div>
                                    <span class="relative z-10">{{ $category->icon ?: '📁' }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-lg tracking-tight">{{ $category->name }}</div>
                                    <div class="flex items-center space-x-2 mt-0.5">
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $category->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $category->type }}
                                        </span>
                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                        <span class="text-[10px] font-mono text-slate-400 italic uppercase">{{ $category->color }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-4 group-hover:translate-x-0 transition-transform">
                                <button wire:click="edit({{ $category->id }})" class="p-3 bg-white text-slate-400 hover:text-indigo-600 rounded-xl shadow-sm border border-slate-100 transition-all active:scale-90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $category->id }})" class="p-3 bg-white text-slate-400 hover:text-red-500 rounded-xl shadow-sm border border-slate-100 transition-all active:scale-90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 0 010 2.828l-7 7a2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <p class="text-slate-400 font-bold tracking-tight">Belum ada kategori terdaftar.</p>
                        </div>
                    @endforelse
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
        <x-slot name="title">Hapus Kategori</x-slot>
        
        <p class="text-lg">Apakah Anda yakin ingin menghapus kategori ini?</p>
        <p class="mt-2 text-sm opacity-80">Transaksi yang terkait dengan kategori ini akan kehilangan referensinya. Tindakan ini tidak dapat dibatalkan.</p>
        
        <x-slot name="footer">
            <button wire:click="delete" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-red-100 active:scale-95">
                Ya, Hapus Kategori
            </button>
            <button wire:click="$set('showDeleteModal', false)" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-600 px-8 py-3 rounded-2xl font-bold transition-all active:scale-95">
                Batal
            </button>
        </x-slot>
    </x-modal>
</div>