<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use function Livewire\Volt\layout;

layout('components.layouts.app');

new class extends Component {
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->intended('/categories');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
};
?>

<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Welcome Back</h2>
    
    <form wire:submit.prevent="login" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input wire:model="email" type="email" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="name@company.com">
            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input wire:model="password" type="password" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="••••••••">
            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md">
            Login
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        Don't have an account? 
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 underline decoration-2 underline-offset-4">Register here</a>
    </div>
</div>
