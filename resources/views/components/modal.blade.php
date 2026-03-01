@props(['id' => null, 'maxWidth' => null])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth ?? 'md'];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    @if($id ?? null) id="{{ $id }}" @endif
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-[100]"
    style="display: none;"
>
    <!-- Background overlay -->
    <div x-show="show" class="fixed inset-0 transform transition-all z-[100]" 
        x-on:click="show = false" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    </div>

    <!-- Modal Content -->
    <div x-show="show" 
        class="relative z-[110] mb-6 bg-white rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.1)] transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto mt-20 border border-slate-100"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">
        
        <div class="p-8">
            @if(isset($icon))
                <div class="mb-5 flex justify-center">
                    <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                        {{ $icon }}
                    </div>
                </div>
            @endif

            @if(isset($title))
                <h3 class="text-xl font-extrabold text-slate-800 mb-3 text-center">{{ $title }}</h3>
            @endif
            
            <div class="text-slate-500 text-center leading-relaxed">
                {{ $slot }}
            </div>
        </div>

        @if(isset($footer))
            <div class="bg-slate-50/50 px-8 py-5 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
