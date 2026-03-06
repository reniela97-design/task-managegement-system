<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset Password - Emergency Task Management</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --royal-blue: #002366;
                --maroon: #ce0202;
                --main-bg: #f3f6f9;
            }
            body {
                font-family: 'Instrument Sans', sans-serif;
                background: linear-gradient(135deg, var(--royal-blue) 0%, var(--maroon) 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow-x: hidden;
            }

            /* Card Container */
            .glass-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
                display: flex;
                width: 100%;
                max-width: 900px; /* Slightly smaller than login */
                min-height: 500px;
                position: relative;
            }

            /* Static Sidebar */
            .sidebar {
                background: linear-gradient(135deg, var(--maroon) 0%, var(--royal-blue) 100%);
                color: white;
                padding: 50px;
                width: 40%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
            }

            /* Main Content Area */
            .main-content {
                width: 60%;
                background-color: var(--main-bg);
                padding: 60px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            /* Typography */
            .form-header {
                font-size: 2rem;
                font-weight: 700;
                color: var(--royal-blue);
                margin-bottom: 1rem;
                letter-spacing: -0.5px;
            }
            .form-text {
                font-size: 0.9rem;
                color: #64748b;
                line-height: 1.6;
                margin-bottom: 2rem;
            }

            /* Custom Input Styling */
            .input-group { margin-bottom: 1.5rem; }
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

            /* Button Styling */
            .btn-auth {
                background: var(--royal-blue);
                color: white;
                padding: 15px 0;
                width: 100%;
                border-radius: 12px;
                font-weight: 600;
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

            /* Sidebar Decor */
            .sidebar-title {
                writing-mode: vertical-lr;
                transform: rotate(180deg);
                font-size: 3rem;
                font-weight: 700;
                opacity: 0.2;
                position: absolute;
                left: 40px;
                top: 50px;
            }
            .back-link {
                color: var(--royal-blue);
                font-size: 0.85rem;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                margin-top: 20px;
                transition: color 0.2s;
            }
            .back-link:hover { color: var(--maroon); }
        </style>
    </head>
    <body>

        <div class="glass-card">
            
            <div class="sidebar">
                <div class="sidebar-title">RECOVERY</div>
                <div style="margin-top: auto; position: relative; z-index: 10;">
                    <h2 class="text-xs font-black tracking-widest uppercase mb-2">Account Security</h2>
                    <h1 class="text-3xl font-bold mb-4">Reset</h1>
                    <p class="text-white/80 text-sm leading-relaxed">
                        Don't worry. It happens. We'll help you regain access to your emergency dashboard securely.
                    </p>
                </div>
            </div>

            <div class="main-content">
                <div class="max-w-md mx-auto w-full">
                    
                    <h2 class="form-header">Forgot Password?</h2>
                    
                    <div class="form-text">
                        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="input-group">
                            <label class="input-label" for="email">Email Address</label>
                            <input id="email" class="form-input" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your registered email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-bold" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="btn-auth">
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ url('/') }}" class="back-link">
                                ← Back to Login
                            </a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

    </body>
</html>