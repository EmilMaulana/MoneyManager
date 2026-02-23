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

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:income,expense',
    ];

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

        $this->reset(['name','color','icon']);
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

        $this->reset(['name','color','icon','categoryId','isEdit']);
        $this->type = 'expense';
    }

    public function delete($id)
    {
        Category::where('user_id', auth()->id())
            ->findOrFail($id)
            ->delete();
    }

    public function getCategoriesProperty()
    {
        return Category::where('user_id', auth()->id())
            ->latest()
            ->get();
    }
};
?>

<div class="max-w-4xl mx-auto p-6">

    <h2 class="text-2xl font-bold mb-6">Manajemen Kategori</h2>

    <div class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-2 gap-4">

            <input type="text" wire:model="name" placeholder="Nama"
                   class="border rounded p-2">

            <select wire:model="type" class="border rounded p-2">
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>

            <input type="color" wire:model="color"
                   class="border rounded p-2">

            <input type="text" wire:model="icon"
                   placeholder="Icon"
                   class="border rounded p-2">
        </div>

        <div class="mt-4">
            @if($isEdit)
                <button wire:click="update"
                        class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update
                </button>
            @else
                <button wire:click="store"
                        class="bg-green-500 text-white px-4 py-2 rounded">
                    Tambah
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        @foreach($this->categories as $category)
            <div class="flex justify-between border-b py-2">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded"
                         style="background: {{ $category->color }}"></div>
                    {{ $category->name }}
                </div>

                <div class="space-x-2">
                    <button wire:click="edit({{ $category->id }})"
                            class="text-blue-500">Edit</button>
                    <button wire:click="delete({{ $category->id }})"
                            class="text-red-500">Hapus</button>
                </div>
            </div>
        @endforeach
    </div>

</div>