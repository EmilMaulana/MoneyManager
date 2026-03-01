<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="theme-color" content="#4263eb">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
            
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #f7f9ff;
            }

            .auth-bg {
                background: linear-gradient(135deg, #2b44aa 0%, #4d6ef6 100%);
                position: fixed;
                inset: 0;
                overflow: hidden;
                z-index: -1;
            }

            .wave-container {
                position: absolute;
                inset: 0;
                opacity: 0.6;
            }

            .sphere {
                position: absolute;
                border-radius: 50%;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0.1) 100%);
                box-shadow: 
                    inset 4px 4px 10px rgba(255, 255, 255, 0.5),
                    inset -4px -4px 10px rgba(0, 0, 0, 0.05),
                    20px 20px 60px rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(10px);
            }

            .sphere-dark {
                background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
                box-shadow: 20px 20px 60px rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .sphere-1 { width: 140px; height: 140px; top: 5%; right: 10%; }
            .sphere-2 { width: 90px; height: 90px; top: 60%; left: 5%; }
            .sphere-3 { width: 110px; height: 110px; top: 15%; left: 8%; opacity: 0.8; }
            .sphere-4 { width: 220px; height: 220px; bottom: -60px; right: -40px; }
            .sphere-5 { width: 160px; height: 160px; top: -50px; left: -50px; }

            .auth-card {
                background: white;
                border-radius: 40px;
                width: 90%;
                max-width: 460px;
                padding: 48px 32px;
                box-shadow: 0 40px 100px rgba(0, 0, 0, 0.2);
                position: relative;
                z-index: 10;
                margin: 2rem auto;
            }

            .form-input {
                border: 1px solid #edf2f7;
                border-radius: 16px;
                padding: 16px 20px;
                width: 100%;
                transition: all 0.2s;
                font-weight: 500;
                color: #2d3748;
            }

            .form-input:focus {
                border-color: #4d6ef6;
                box-shadow: 0 0 0 4px rgba(77, 110, 246, 0.1);
                outline: none;
            }

            .btn-primary {
                background: #4d6ef6;
                color: white;
                font-weight: 700;
                padding: 18px;
                border-radius: 20px;
                width: 100%;
                transition: all 0.3s;
                box-shadow: 0 12px 24px rgba(77, 110, 246, 0.35);
            }

            .btn-primary:hover {
                background: #3d5de0;
                box-shadow: 0 16px 32px rgba(77, 110, 246, 0.45);
                transform: translateY(-2px);
            }

            .btn-primary:active {
                transform: translateY(0) scale(0.98);
            }

            .social-btn {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: 1px solid #edf2f7;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }

            .social-btn:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                transform: scale(1.05);
            }

            .splash-title {
                font-size: 3.5rem;
                font-weight: 800;
                letter-spacing: -0.04em;
                line-height: 1.1;
                color: white;
                text-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
        </style>
    </head>
    <body class="antialiased text-slate-900 min-h-screen flex items-center justify-center overflow-x-hidden">
        <!-- Background Elements -->
        <div class="auth-bg">
            <!-- Organic Waves (Simplified for CSS fidelity) -->
            <div class="wave-container">
                <svg viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" class="absolute w-[200%] h-[150%] -top-[25%] -left-[50%] opacity-40">
                    <path d="M0,100 C150,200 350,0 500,100 L500,00 L0,0 Z" style="stroke: none; fill:rgba(255,255,255,0.15);"></path>
                </svg>
                <svg viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" class="absolute w-[180%] h-[130%] -top-[10%] -left-[30%] opacity-30">
                    <path d="M0,150 C120,50 380,250 500,150 L500,00 L0,0 Z" style="stroke: none; fill:rgba(255,255,255,0.2);"></path>
                </svg>
            </div>

            <!-- Enhanced Spheres -->
            <div class="sphere sphere-dark sphere-5"></div>
            <div class="sphere sphere-3"></div>
            <div class="sphere sphere-1"></div>
            <div class="sphere sphere-2"></div>
            <div class="sphere sphere-dark sphere-4"></div>
        </div>

        <div class="w-full flex justify-center">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
