<?php

use Livewire\Volt\Component;
use function Livewire\Volt\layout;

layout('layouts.guest');

new class extends Component {
    //
};
?>

<div class="h-screen w-full flex flex-col justify-between p-12 text-white relative z-20">
    <div class="pt-20">
        <h1 class="splash-title">Welcome Back!</h1>
        <p class="mt-6 text-xl font-medium opacity-80 leading-relaxed max-w-xs">
            Enter personal details to you employee account
        </p>
    </div>

    <div class="pb-10 space-y-4">
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-2 border border-white/20">
            <a href="{{ route('login') }}" class="flex-1 py-4 text-center font-bold text-lg hover:bg-white/10 rounded-xl transition-all">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="flex-1 py-4 text-center font-bold text-lg bg-white text-[#4d6ef6] rounded-xl shadow-xl transition-all">
                Sign up
            </a>
        </div>
    </div>
</div>
