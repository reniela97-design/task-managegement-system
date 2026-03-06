<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Emergence Task Management</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --royal-blue: #002366;
                --maroon: #a90606;
                --main-bg: #f3f6f9; 
                --transition-speed: 0.75s;
                --primary-color: #F5F5DC;
                --secondary-color: #cbc1bc;
            }
            body {
                font-family: 'Instrument Sans', sans-serif;
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                overflow-x: hidden;
                min-height: 100vh;
                margin: 0;
            }
            
            /* Main Card */
            .glass-card {
                background: white; 
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5); 
                display: flex;
                width: 100%;
                max-width: 950px;
                min-height: 600px;
                position: relative;
            }

            /* --- SIDEBAR WITH PANNING PATTERN --- */
            .sidebar {
                /* 1. Gradient & Image */
                background: 
                    linear-gradient(135deg, rgba(169, 6, 6, 0.9) 0%, rgba(0, 35, 102, 0.9) 100%),
                    url("{{ asset('download.png') }}"); /* Ensure this file is in public folder */
                
                background-blend-mode: overlay;
                background-size: cover;
                
                /* 2. Start position: Left side of the image */
                background-position: 0% center; 

                color: white;
                padding: 50px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: absolute;
                width: 50%; 
                height: 100%;
                z-index: 20;
                left: 0;
                
                /* Slant: Top-Right to Bottom-Left */
                clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
                
                transition: all var(--transition-speed) cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            /* Main Content */
            .main-content {
                flex: 1;
                padding: 40px 60px;
                background-color: var(--main-bg); 
                width: 60%;
                margin-left: 40%; 
                display: flex;
                flex-direction: column;
                justify-content: center;
                transition: all var(--transition-speed) cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            /* --- ANIMATION STATES --- */
            
            /* Move Sidebar to Right & Flip Slant & PAN IMAGE */
            .active-register .sidebar { 
                left: 50%; 
                
                /* Flip the slant direction (Top-Left to Bottom-Right) */
                clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
                
                /* 3. End position: Slide image to the Right side */
                background-position: 100% center;
            }
            
            .active-register .main-content { 
                margin-left: 0; 
                margin-right: 40%; 
            }

            /* Typography */
            .form-header {
                font-size: 2.2rem;
                font-weight: 700;
                color: var(--royal-blue);
                margin-bottom: 1.5rem;
                letter-spacing: -0.5px;
            }

            /* Inputs */
            .input-group {
                position: relative;
                margin-bottom: 1.25rem;
            }
            .input-label {
                font-size: 0.85rem;
                color: var(--royal-blue);
                font-weight: 600;
                margin-bottom: 8px;
                display: block;
                margin-left: 4px;
            }
            .form-input {
                width: 100%;
                padding: 14px 20px;
                background-color: white;
                border: 1px solid rgba(0, 35, 102, 0.1);
                border-radius: 12px;
                color: #333;
                font-size: 1rem;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            }
            .form-input:focus {
                outline: none;
                border-color: var(--maroon);
                box-shadow: 0 4px 12px rgba(128, 0, 0, 0.15);
                transform: translateY(-2px);
            }

            /* Buttons */
            .btn-auth {
                background: var(--royal-blue);
                color: white;
                padding: 15px 0;
                width: 100%;
                border-radius: 12px;
                font-weight: 600;
                margin-top: 10px;
                cursor: pointer;
                border: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(0, 35, 102, 0.2);
            }
            .btn-auth:hover {
                background: var(--maroon);
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(128, 0, 0, 0.25);
            }
            .switch-btn {
                color: var(--maroon);
                font-weight: 600;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 0.9rem;
                text-decoration: underline;
                text-underline-offset: 4px;
            }
            
            /* Sidebar Decor */
            .sidebar-title {
                writing-mode: vertical-lr;
                transform: rotate(180deg);
                font-size: 3rem;
                font-weight: 700;
                opacity: 0.5;
            }
            
            /* Form Visibility Animation */
            .hidden-form { display: none; }
            .show-form { 
                display: block; 
                animation: fadeUp 0.6s forwards;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4">

        <div class="glass-card" id="authCard">
            <div class="sidebar">
                <div class="sidebar-title">Emergence</div>
                <div class="relative z-10">
                    <h2 class="text-xs font-black tracking-widest uppercase mb-2">Task Management</h2>
                    <h1 id="panelTitle" class="text-3xl font-bold mb-4">Welcome</h1>
                    <p id="panelText" class="text-white/80 text-sm leading-relaxed">Introducing your streamlined emergence operations system.</p>
                </div>
            </div>

            <div class="main-content">
                <div class="max-w-xs mx-auto w-full">
                    
                    <div class="flex justify-center mb-2">
                        <img src="{{ asset('logoo.png') }}" alt="Logo" class="h-24 object-contain">
                    </div>
                    
                    <div id="loginForm" class="show-form">
                        <h2 class="form-header text-center">LOGIN</h2>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="input-group">
                                <label class="input-label">Email Address</label>
                                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="officer@emergence.com">
                                @if($errors->has('email')) <p class="text-red-500 text-xs mt-1">{{ $errors->first('email') }}</p> @endif
                            </div>
                            <div class="input-group">
                                <label class="input-label">Password</label>
                                <input type="password" name="password" class="form-input" required placeholder="••••••••">
                            </div>
                            
                            <div class="flex flex-col items-center gap-6 mt-6">
                                <button type="submit" class="btn-auth">Sign In</button>
                                
                                <div class="flex flex-col gap-3 items-center w-full">
                                    {{-- <div class="text-sm text-gray-400">or</div>
                                    <button type="button" onclick="toggleAuth(true)" class="switch-btn">Create New Account</button> --}}
                                    
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-xs text-slate-400 hover:text-maroon mt-2">Forgot Password?</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="registerForm" class="hidden-form">
                        <h2 class="form-header text-center">SIGN UP</h2>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="input-group">
                                <label class="input-label">Full Name</label>
                                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="John Doe">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Email Address</label>
                                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required placeholder="john@example.com">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Password</label>
                                <input type="password" name="password" class="form-input" required placeholder="Create password">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-input" required placeholder="Confirm password">
                            </div>
                            
                            <div class="flex flex-col items-center gap-6 mt-6">
                                <button type="submit" class="btn-auth">Register</button>
                                <button type="button" onclick="toggleAuth(false)" class="switch-btn">Back to Login</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <script>
            function toggleAuth(isRegister) {
                const card = document.getElementById('authCard'), loginForm = document.getElementById('loginForm'), registerForm = document.getElementById('registerForm');
                const panelTitle = document.getElementById('panelTitle'), panelText = document.getElementById('panelText');
                
                // Delay matched to the bounce animation speed
                const delay = 350;

                if (isRegister) {
                    card.classList.add('active-register');
                    setTimeout(() => {
                        loginForm.classList.replace('show-form', 'hidden-form');
                        registerForm.classList.replace('hidden-form', 'show-form');
                        panelTitle.innerText = "Join Us";
                        panelText.innerText = "Create your account to start managing tasks.";
                    }, delay);
                } else {
                    card.classList.remove('active-register');
                    setTimeout(() => {
                        registerForm.classList.replace('show-form', 'hidden-form');
                        loginForm.classList.replace('hidden-form', 'show-form');
                        panelTitle.innerText = "Welcome";
                        panelText.innerText = "Introducing your streamlined emergence operations system.";
                    }, delay);
                }
            }
        </script>
    </body>
</html>