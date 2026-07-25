<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AQPA Indonesia Dashboard</title>
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Inter / Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Premium Micro-animations */
        .glass-panel {
            background: rgba(19, 35, 58, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .input-glow:focus-within {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-[#0b1320] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Premium Background Effects -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#1a365d] via-[#0b1320] to-[#05080e] -z-10"></div>
    <div class="absolute -right-40 -top-40 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[100px] pointer-events-none animate-float"></div>
    <div class="absolute -left-40 -bottom-40 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[80px] pointer-events-none animate-float" style="animation-delay: -3s;"></div>

    <!-- Main Card Container (Glassmorphism) -->
    <div class="w-full max-w-4xl glass-panel rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative z-10 transition-all duration-500 hover:shadow-[0_0_40px_rgba(59,130,246,0.15)]">

        <!-- Left Section (Branding & Info) -->
        <div class="w-full md:w-1/2 p-10 md:p-12 flex flex-col justify-between bg-gradient-to-br from-[#1b3459]/80 via-[#10233d]/80 to-[#0d1a2d]/80 relative overflow-hidden">
            
            <!-- Background Decorative Elements -->
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full border border-blue-400/10 pointer-events-none"></div>
            <div class="absolute -bottom-10 -right-10 w-72 h-72 rounded-full border border-blue-300/10 pointer-events-none"></div>

            <!-- Top Logo Card -->
            <div class="z-10 mb-8 transform transition hover:scale-105 duration-300">
                <div class="bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-xl border border-white/10 inline-flex items-center gap-3 shadow-lg">
                    <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-inner">
                        A
                    </div>
                    <span class="font-bold text-white tracking-wide text-sm">AQPA INDONESIA</span>
                </div>
            </div>

            <!-- Middle Text Content -->
            <div class="z-10 my-auto">
                <p class="text-xs font-semibold text-blue-400 tracking-widest uppercase mb-3 flex items-center gap-2">
                    <span class="w-8 h-[1px] bg-blue-400"></span>
                    Integrated Monitoring
                </p>
                <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-5 drop-shadow-lg">
                    AQPA<br>Dashboard
                </h1>
                <p class="text-slate-300 text-sm leading-relaxed max-w-sm font-light">
                    Kontrol data accounting, persediaan, pembelian, penjualan, dan project dalam satu ruang kerja yang elegan.
                </p>
            </div>

            <!-- Bottom Badges -->
            <div class="z-10 mt-10 flex flex-wrap gap-3">
                <span class="px-4 py-1.5 bg-white/5 border border-white/10 text-slate-200 text-xs font-medium rounded-full backdrop-blur-md hover:bg-white/10 transition-colors cursor-default">
                    <i class="fa-solid fa-chart-pie mr-1.5 text-blue-400"></i> ERP
                </span>
                <span class="px-4 py-1.5 bg-white/5 border border-white/10 text-slate-200 text-xs font-medium rounded-full backdrop-blur-md hover:bg-white/10 transition-colors cursor-default">
                    <i class="fa-solid fa-wallet mr-1.5 text-indigo-400"></i> Finance
                </span>
                <span class="px-4 py-1.5 bg-white/5 border border-white/10 text-slate-200 text-xs font-medium rounded-full backdrop-blur-md hover:bg-white/10 transition-colors cursor-default">
                    <i class="fa-solid fa-gears mr-1.5 text-cyan-400"></i> Operations
                </span>
            </div>
        </div>

        <!-- Right Section (Login Form) -->
        <div class="w-full md:w-1/2 bg-white/5 backdrop-blur-xl p-10 md:p-12 flex flex-col justify-center relative">
            
            <div class="w-full max-w-sm mx-auto">
                <!-- Form Title -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Masuk Dashboard</h2>
                    <p class="text-sm text-slate-400 font-light">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-start gap-3 backdrop-blur-sm">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form Inputs -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-300 ml-1">Alamat Email</label>
                        <div class="relative flex items-center input-glow rounded-xl transition-all">
                            <span class="absolute left-4 text-slate-400 text-sm">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', 'admin@mail.com') }}" placeholder="admin@mail.com" required autofocus
                                class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:bg-white/10 focus:border-blue-500 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-xs font-medium text-slate-300">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Lupa sandi?</a>
                            @endif
                        </div>
                        <div class="relative flex items-center input-glow rounded-xl transition-all">
                            <span class="absolute left-4 text-slate-400 text-sm">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                class="w-full pl-11 pr-10 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:bg-white/10 focus:border-blue-500 focus:outline-none transition-all">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 text-slate-400 hover:text-white text-sm focus:outline-none transition-colors">
                                <i class="fa-regular fa-eye-slash" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mt-2 ml-1">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                        <label for="remember_me" class="ml-2 text-xs text-slate-400">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-medium text-sm py-3 rounded-xl transition-all shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:shadow-[0_0_25px_rgba(59,130,246,0.5)] active:scale-[0.98] mt-6 flex justify-center items-center gap-2">
                        Masuk ke Dashboard
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <!-- Footer Copyright -->
            <div class="text-center absolute bottom-6 left-0 w-full">
                <p class="text-[10px] text-slate-500">
                    &copy; 2026 AQPA Indonesia Monitoring System
                </p>
            </div>
        </div>

    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
