<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        PLN NP CSR - Login
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('argon/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .login-bg {
            position: relative;
            min-height: 100vh;
            width: 100%;
            background-image: linear-gradient(120deg, rgba(20, 60, 20, 0.82), rgba(20, 70, 25, 0.55)), url('{{ asset('argon/img/login3.png') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: 'Open Sans', sans-serif;
            color: #fff;
        }

        .login-topbar {
            font-size: .8rem;
            color: rgba(255,255,255,.65);
            padding: 10px 32px 0;
        }

        .login-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 60px;
        }

        .login-left {
            max-width: 560px;
        }

        /* ==== FIX: brand row alignment (tanpa background putih) ==== */
        .brand-row {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
        }

        .brand-row img {
            height: 56px;
            width: auto;
            display: block;
            margin-left: -10px; /* kompensasi whitespace transparan di file PNG logo, sesuaikan angka ini kalau masih kurang/lebih */
        }
        /* ==== END FIX ==== */

        .login-left h1 {
            font-weight: 800;
            font-size: 2.6rem;
            line-height: 1.15;
            margin-bottom: 22px;
            color: #ffffff !important;
        }

        .login-left .desc {
            border-left: 3px solid #78C841;
            padding-left: 16px;
            color: rgba(255,255,255,.85);
            font-size: .95rem;
            margin-bottom: 40px;
            max-width: 460px;
        }

        .stats-row {
            display: flex;
            gap: 48px;
        }

        .stat-item .num {
            color: #78C841;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .stat-item .label {
            font-size: .7rem;
            letter-spacing: .5px;
            color: rgba(255,255,255,.75);
            text-transform: uppercase;
        }

        .login-card {
            background: #f4f5f2;
            border-radius: 18px;
            padding: 40px 36px;
            width: 100%;
            max-width: 380px;
            margin-left: auto;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
            color: #1a1a1a;
        }

        .login-card h4 {
            font-weight: 800;
            margin-bottom: 4px;
        }

        .login-card p.subtitle {
            color: #6b6b6b;
            font-size: .9rem;
            margin-bottom: 24px;
        }

        .login-card label {
            font-weight: 700;
            font-size: .85rem;
            margin-bottom: 6px;
            display: block;
        }

        .login-card .form-control {
            background: #fff;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .input-icon-group {
            position: relative;
        }

        .input-icon-group i,
        .input-icon-group .icon-left,
        .input-icon-group .icon-right {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #9a9a9a;
        }

        .input-icon-group .icon-left {
            left: 14px;
        }

        /* ==== FIX: eye icon toggle (SVG, tidak bergantung CDN) ==== */
        .input-icon-group .icon-right {
            right: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            color: #6b6b6b;
        }

        .input-icon-group .icon-right svg {
            width: 20px;
            height: 20px;
            pointer-events: none;
        }

        .input-icon-group .icon-right:hover {
            color: #4c9b1f;
        }
        /* ==== END FIX ==== */

        .input-icon-group .form-control {
            padding-left: 38px;
            padding-right: 40px;
        }

        .btn-signin {
            background-color: #4c9b1f;
            border-color: #4c9b1f;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px;
        }

        .btn-signin:hover {
            background-color: #3f8118;
            color: #fff;
        }

        .login-footer {
            font-size: .75rem;
            color: rgba(255,255,255,.7);
            padding: 14px 32px;
        }

        @media (max-width: 992px) {
            .login-content {
                flex-direction: column;
                padding: 40px 24px;
                gap: 40px;
            }
            .login-card {
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    <main class="main-content mt-0">
        <div class="login-bg">

            <div class="login-content">
                <div class="row w-100 align-items-center">
                    <div class="col-lg-7 login-left">
                        <div class="brand-row">
                            <img src="{{ asset('images/logos/logo-pln2.png') }}" alt="PLN Nusantara Power">
                        </div>

                        <h1>TJSL PLN<br>Nusantara Power</h1>

                        <div class="desc">
                            Membangun Masa Depan Hijau melalui Tanggung Jawab Sosial dan Lingkungan yang Terintegrasi dan Transparan.
                        </div>

                        <div class="stats-row">
                            <div class="stat-item">
                                <div class="num">{{ number_format($totalPengajuan ?? 0) }}+</div>
                                <div class="label">Pengajuan</div>
                            </div>
                            <div class="stat-item">
                                <div class="num">{{ number_format($totalCakupanDesa ?? 0) }}+</div>
                                <div class="label">Cakupan Desa</div>
                            </div>
                            <div class="stat-item">
                                <div class="num">{{ number_format($totalStakeholder ?? 0) }}+</div>
                                <div class="label">Stakeholder</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="login-card">
                            <h4>Sign In</h4>
                            <p class="subtitle">Masukkan Username dan Password</p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="username">Username</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-user icon-left"></i>
                                        <input type="text" name="username" id="username"
                                            class="form-control @error('username') is-invalid @enderror"
                                            placeholder="Username" value="{{ old('username') }}" required autofocus
                                            autocomplete="off">
                                    </div>
                                    @error('username')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-lock icon-left"></i>
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Password" required>
                                        <!-- FIX: eye icon pakai inline SVG, punya 2 state (eye / eye-slash) -->
                                        <span class="icon-right" id="togglePassword" role="button" aria-label="Tampilkan/sembunyikan password">
                                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <svg id="eyeSlashIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.94"></path>
                                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19"></path>
                                                <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                                                <line x1="1" y1="1" x2="23" y2="23"></line>
                                            </svg>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="remember"
                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Simpan Login</label>
                                </div>

                                <button type="submit" class="btn btn-signin w-100">
                                    Sign in <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="login-footer">PLN Nusantara Power UP Paiton</div>
        </div>
    </main>

    <!--   Core JS Files   -->
    <script src="{{ asset('argon/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('argon/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('argon/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('argon/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // FIX: toggle show/hide password pakai SVG eye / eye-slash
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        const eyeSlashIcon = document.querySelector('#eyeSlashIcon');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.style.display = isPassword ? 'none' : 'block';
                eyeSlashIcon.style.display = isPassword ? 'block' : 'none';
            });
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('argon/js/argon-dashboard.min.js?v=2.1.0') }}"></script>
</body>

</html>