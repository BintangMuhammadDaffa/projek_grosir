@extends('layouts.app')

@section('content')
<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-10">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="row g-0">
                        <!-- Bagian Kiri: Form Login -->
                        <div class="col-md-6 p-5">
                            <h3 class="mb-4 fw-bold text-primary">Welcome Back!</h3>

                            <form id="loginForm" method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                    <input id="email" type="email" 
                                        class="form-control @error('email') is-invalid @enderror" 
                                        name="email" value="{{ old('email') }}" placeholder="Masukkan Email..." required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password dengan Icon Mata -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <div class="input-group">
                                        <input id="password" type="password" 
                                            class="form-control @error('password') is-invalid @enderror" 
                                            name="password" placeholder="Masukkan Password..." required>
                                        <span class="input-group-text password-toggle" style="cursor: pointer; border-left: 0; background-color: #f8f9fa;">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" 
                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                                </div>

                                <!-- Tombol -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                                    <a href="{{ route('register') }}" class="btn btn-outline-secondary">Create Account</a>
                                </div>

                                <!-- Lupa Password -->
                                <div class="mt-3 text-center">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        <!-- Bagian Kanan: Animasi Rive -->
                        <div class="col-md-6 d-none d-md-flex justify-content-center align-items-center bg-light rounded-end-4">
                            <canvas id="teddy" width="400" height="400" class="p-4"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .login-wrapper {
        background: linear-gradient(135deg, #ff005c 0%, #00fff7 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }
    
    .login-wrapper .card {
        border-radius: 15px !important;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .login-wrapper h3.text-primary {
        color: #ff005c !important;
    }
    
    .login-wrapper .btn.btn-primary {
        background-color: #ff005c !important;
        border-color: #ff005c !important;
        transition: all 0.3s ease;
    }
    
    .login-wrapper .btn.btn-primary:hover {
        background-color: #d4004f !important;
        border-color: #d4004f !important;
        transform: translateY(-2px);
    }
    
    .login-wrapper .btn.btn-outline-secondary {
        border-color: #00fff7;
        color: #00fff7;
        transition: all 0.3s ease;
    }
    
    .login-wrapper .btn.btn-outline-secondary:hover {
        background-color: #00fff7;
        color: black;
    }

    #teddy {
        width: 100%;
        height: 400px;
        display: block;
    }

    /* Styling untuk Icon Mata */
    .password-toggle {
        color: #6c757d;
        transition: color 0.3s ease, background-color 0.3s ease;
        border-color: #ced4da !important;
    }
    
    .password-toggle:hover {
        color: #ff005c !important;
        background-color: #f0f0f0 !important;
    }
    
    .input-group-text i {
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .login-wrapper .col-md-6:last-child {
            display: none !important;
        }
        
        .login-wrapper .card {
            margin: 1rem;
        }
    }
</style>

<!-- Tambahkan library Rive -->
<script src="https://unpkg.com/@rive-app/canvas@2.19.4"></script>

<!-- Script interaktif Rive + Toggle Password -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const riveCanvas = document.getElementById('teddy');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const form = document.getElementById('loginForm');
    const toggleIcon = document.getElementById('toggleIcon');
    const passwordToggle = document.querySelector('.password-toggle');

    let teddyRive;
    let isPasswordVisible = false;
    let peep, fail, handsUp;
    let ignoreBlur = false;

    // === Fungsi Toggle Password Visibility ===
    function togglePasswordVisibility() {
        isPasswordVisible = !isPasswordVisible;

        // Aktifkan animasi 'peep'
        if (peep) peep.value = isPasswordVisible;

        // Cegah blur menurunkan tangan
        ignoreBlur = true;

        if (isPasswordVisible) {
            passwordInput.type = 'text';
            toggleIcon.className = 'bi bi-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'bi bi-eye';
        }

        // Fokus ulang agar animasi tidak reset
        setTimeout(() => {
            passwordInput.focus();
            ignoreBlur = false;
        }, 10);
    }

    // === Event listener icon mata ===
    if (toggleIcon && passwordToggle) {
        passwordToggle.addEventListener('mousedown', (e) => e.preventDefault()); // cegah blur
        passwordToggle.addEventListener('click', togglePasswordVisibility);
    }

    // === Load Rive Animation ===
    teddyRive = new rive.Rive({
        src: "{{ asset('assets/animation/auth_teddy.riv') }}",
        canvas: riveCanvas,
        stateMachines: ['Login Machine'],
        autoplay: true,
        fit: rive.Fit.CONTAIN,
        onLoad: () => {
            const inputs = teddyRive.stateMachineInputs('Login Machine');
            const look = inputs.find(i => i.name === 'isFocus');
            handsUp = inputs.find(i => i.name === 'isPrivateField');
            peep = inputs.find(i => i.name === 'isPrivateFieldShow');
            const success = inputs.find(i => i.name === 'successTrigger');
            fail = inputs.find(i => i.name === 'failTrigger');

            // === Jika muncul pesan invalid-feedback ===
            const hasInvalidFeedback = document.querySelector('.invalid-feedback');
            if (hasInvalidFeedback && fail) {
                setTimeout(() => fail.fire(), 400); 
                // delay kecil agar animasi muncul setelah halaman selesai render
            }

            // === Animasi Email Look ===
            if (emailInput && look) {
                emailInput.addEventListener('input', (e) => {
                    look.value = Math.min(e.target.value.length / 10, 1);
                });
                emailInput.addEventListener('blur', () => {
                    look.value = 0;
                });
            }

            // === Animasi Password HandsUp ===
            if (passwordInput && handsUp) {
                passwordInput.addEventListener('focus', () => {
                    handsUp.value = true;
                });
                passwordInput.addEventListener('blur', () => {
                    if (!ignoreBlur) handsUp.value = false;
                });
            }

            // === Submit Form ===
            if (form && success) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    success.fire();
                    setTimeout(() => form.submit(), 800);
                });
            }
        },
        onLoadError: (err) => console.error('Rive load error:', err)
    });
});
</script>


@endsection