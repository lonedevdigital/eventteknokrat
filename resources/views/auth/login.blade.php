<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Inter & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        :root {
            --primary: #1f2937;
            --primary-700: #111827;
            --text: #1f2937;
            --muted: #6b7280;
            --muted-2: #9ca3af;
            --bg: #f3f4f6;
            --surface: #fff;
            --surface-2: #f8fafc;
            --border: #e5e7eb;
            --error: #dc2626;
            --success: #059669;
            --r: 16px;
            --shadow: 0 10px 30px rgba(17, 24, 39, .12);
            --shadow-lg: 0 24px 60px rgba(17, 24, 39, .18);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html,
        body {
            height: 100%
        }

        body {
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: grid;
            place-items: center;
            padding: 16px;
        }

        /* gentle moving noise bg */
        .bg-noise {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .25;
            background:
                radial-gradient(40rem 40rem at 120% -10%, rgba(0, 0, 0, .05), transparent 60%),
                radial-gradient(40rem 40rem at -20% 120%, rgba(0, 0, 0, .04), transparent 60%);
            animation: bgFloat 30s ease-in-out infinite alternate;
        }

        @keyframes bgFloat {
            from {
                transform: translate(0, 0)
            }

            to {
                transform: translate(-6px, -6px)
            }
        }

        @media (prefers-reduced-motion:reduce) {
            .bg-noise {
                animation: none
            }
        }

        .card {
            width: min(100%, 460px);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            box-shadow: var(--shadow);
            transition: box-shadow .25s ease, transform .25s ease;
            padding: 28px 24px;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px)
        }

        @media (max-width:480px) {
            .card {
                padding: 22px 18px
            }
        }

        /* logo wrapper full center & responsive */
        .logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 8px 0 18px;
        }

        .logo-img {
            width: min(360px, 80%);
            max-width: 360px;
            aspect-ratio: 16/5;
            /* biar proporsional banner */
            object-fit: contain;
            border-radius: 12px;
            background: var(--surface);
        }

        .title {
            text-align: center;
            font-weight: 600;
            font-size: 20px;
            letter-spacing: -.015em;
            margin-bottom: 18px;
            color: var(--text);
        }

        .form {
            display: grid;
            gap: 14px
        }

        .group {
            display: grid;
            gap: 8px
        }

        .label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text)
        }

        .input-wrap {
            position: relative
        }

        .input {
            width: 100%;
            height: 44px;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0 44px 0 42px;
            font-size: 13px;
            color: var(--text);
            background: var(--surface);
            outline: 0;
            transition: border-color .2s, box-shadow .2s, transform .15s;
        }

        .input:focus {
            border-color: var(--primary);
            box-shadow: 0 4px 14px rgba(31, 41, 55, .12);
            background: var(--surface-2);
            transform: translateY(-1px);
        }

        .input.error {
            border-color: var(--error);
            box-shadow: 0 4px 12px rgba(220, 38, 38, .12);
        }

        .icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-2);
            font-size: 14px;
            transition: color .2s, transform .2s;
        }

        .input:focus+.icon {
            color: var(--primary);
            transform: translateY(-50%) scale(1.05)
        }

        .toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            padding: 6px;
            border-radius: 8px;
            color: var(--muted-2);
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .toggle:hover {
            background: #f3f4f6;
            color: var(--primary)
        }

        .error-msg {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-top: 8px;
            font-size: 11px;
            color: var(--error);
            background: rgba(220, 38, 38, .06);
            padding: 8px 10px;
            border-left: 3px solid var(--error);
            border-radius: 8px;
            animation: slideIn .2s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-6px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 4px;
        }

        @media (max-width:480px) {
            .row {
                flex-direction: column;
                align-items: flex-start
            }
        }

        /* custom checkbox accessible */
        .check {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .check input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
        }

        .box {
            width: 18px;
            height: 18px;
            border: 2px solid var(--border);
            border-radius: 5px;
            display: grid;
            place-items: center;
            background: var(--surface);
            transition: all .15s ease;
        }

        .check input:checked+.box {
            border-color: var(--primary);
            background: var(--primary);
            transform: scale(1.02);
        }

        .check input:checked+.box i {
            color: #fff;
        }

        .check span {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500
        }

        .submit {
            margin-top: 10px;
            width: 100%;
            border: 0;
            border-radius: 12px;
            height: 46px;
            font-weight: 700;
            letter-spacing: .02em;
            color: #fff;
            background: var(--primary);
            cursor: pointer;
            transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
            position: relative;
            overflow: hidden;
        }

        .submit:hover {
            background: var(--primary-700);
            box-shadow: 0 10px 26px rgba(31, 41, 55, .28);
            transform: translateY(-1px)
        }

        .submit:active {
            transform: translateY(0)
        }

        .submit[disabled] {
            opacity: .8;
            cursor: not-allowed
        }

        .shine::before {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
            transition: transform .5s ease;
        }

        .submit:hover.shine::before {
            transform: translateX(100%)
        }

        .spinner {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .35);
            border-top-color: #fff;
            margin-inline: auto;
            animation: spin .8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        @media (prefers-reduced-motion:reduce) {
            .spinner {
                animation: none
            }
        }

        .flash {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 50;
            background: var(--success);
            color: #fff;
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            animation: slideRight .25s ease, fadeOut .25s ease 2.7s forwards;
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(20px)
            }

            to {
                opacity: 1;
                transform: translateX(0)
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(20px)
            }
        }

        .checkbox-inline {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-inline input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            appearance: auto;
            -webkit-appearance: auto;
            -moz-appearance: auto;
            cursor: pointer;
        }

        /* door selection cards */
        .door-grid {
            display: grid;
            gap: 12px;
        }

        .door-card {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            text-align: left;
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s, transform .15s;
            font-family: inherit;
        }

        .door-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(31, 41, 55, .10);
            transform: translateY(-1px);
        }

        .door-icon {
            flex: 0 0 auto;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--surface-2);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 17px;
        }

        .door-text {
            display: grid;
            gap: 2px;
        }

        .door-title {
            font-weight: 700;
            font-size: 13px;
            color: var(--text);
        }

        .door-desc {
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.4;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: 0;
            padding: 0;
            margin-bottom: 14px;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            font-family: inherit;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .form-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 4px;
            color: var(--text);
        }

        .form-subtitle {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    @php
        $doors = $doors ?? [
            'mahasiswa' => [
                'cardTitle' => 'Mahasiswa Teknokrat',
                'cardDesc' => 'Masuk menggunakan NPM & password mahasiswa',
                'icon' => 'fa-user-graduate',
                'loginTitle' => $loginTitle ?? 'Login Mahasiswa',
                'loginSubtitle' => $loginSubtitle ?? 'Gunakan NPM sebagai username dan password.',
                'formAction' => $formAction ?? route('login.store'),
                'usernameLabel' => $usernameLabel ?? 'NPM',
                'usernamePlaceholder' => $usernamePlaceholder ?? 'Masukkan NPM',
                'submitLabel' => $submitLabel ?? 'Sign In Mahasiswa',
            ],
            'external' => [
                'cardTitle' => 'Mahasiswa Luar Teknokrat',
                'cardDesc' => 'Pendaftaran & login peserta luar kampus',
                'icon' => 'fa-user-plus',
                'loginTitle' => 'Login Mahasiswa Luar',
                'loginSubtitle' => 'Masuk dengan email & password terdaftar',
                'formAction' => route('login.store'),
                'usernameLabel' => 'Email',
                'usernamePlaceholder' => 'Masukkan Email',
                'submitLabel' => 'Sign In Peserta Luar',
                'registerUrl' => route('register.external'),
                'registerText' => 'Daftar Akun Baru (Mahasiswa Luar)',
            ],
            'admin' => [
                'cardTitle' => 'Panitia / Staff / Admin',
                'cardDesc' => 'Khusus pengurus event, BAAK, Kemahasiswaan & Superuser',
                'icon' => 'fa-user-shield',
                'loginTitle' => 'Login Admin & Panitia',
                'loginSubtitle' => 'Khusus akun admin (BAAK, Kemahasiswaan, Superuser, PJ)',
                'formAction' => route('admin.login.store'),
                'usernameLabel' => 'Username / Email Admin',
                'usernamePlaceholder' => 'Masukkan username atau email',
                'submitLabel' => 'Sign In Admin',
            ],
        ];

        $activeMode = old('_mode', $loginMode ?? null);
    @endphp

    <div class="bg-noise" aria-hidden="true"></div>

    <main class="card" role="main">
        <div class="logo-wrap">
            <img src="{{ asset('logo_login.jpg') }}" alt="Logo" class="logo-img" />
        </div>

        <h1 class="title">Sistem Manajemen Event</h1>

        @if (session('status'))
            <div class="flash" role="status" aria-live="polite">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <div id="doorSelection" class="door-grid">
            @foreach ($doors as $key => $door)
                <button type="button" class="door-card" data-mode="{{ $key }}">
                    <span class="door-icon"><i class="fa-solid {{ $door['icon'] }}" aria-hidden="true"></i></span>
                    <span class="door-text">
                        <span class="door-title">{{ $door['cardTitle'] }}</span>
                        <span class="door-desc">{{ $door['cardDesc'] }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        @foreach ($doors as $key => $door)
            <div class="door-form" id="form-{{ $key }}" style="display:none;">
                <button type="button" class="back-link" data-back>
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke pilihan login
                </button>

                <h2 class="form-title">{{ $door['loginTitle'] }}</h2>
                @if(!empty($door['loginSubtitle']))
                    <p class="form-subtitle">{{ $door['loginSubtitle'] }}</p>
                @endif

                <form class="form" method="POST" action="{{ $door['formAction'] }}">
                    @csrf
                    <input type="hidden" name="_mode" value="{{ $key }}">
                    <div class="group">
                        <label for="email-{{ $key }}" class="label">{{ $door['usernameLabel'] }}</label>
                        <div class="input-wrap">
                            <input id="email-{{ $key }}" name="email" type="text"
                                class="input @error('email') error @enderror"
                                value="{{ old('_mode') === $key ? old('email') : '' }}" required
                                autocomplete="username" placeholder="{{ $door['usernamePlaceholder'] }}" />
                            <i class="fa-solid fa-user icon" aria-hidden="true"></i>
                        </div>
                        @if(old('_mode') === $key)
                            @error('email')
                                <div class="error-msg"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $message }}</span>
                                </div>
                            @enderror
                        @endif
                    </div>
                    <div class="group">
                        <label for="password-{{ $key }}" class="label">Password</label>
                        <div class="input-wrap">
                            <input id="password-{{ $key }}" name="password" type="password"
                                class="input @error('password') error @enderror" required
                                autocomplete="current-password" placeholder="" />
                            <i class="fa-solid fa-lock icon" aria-hidden="true"></i>
                            <button class="toggle" type="button" aria-label="Tampilkan / sembunyikan password">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @if(old('_mode') === $key)
                            @error('password')
                                <div class="error-msg"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $message }}</span>
                                </div>
                            @enderror
                        @endif
                    </div>
                    <div class="row">
                        <label for="remember-{{ $key }}" class="checkbox-inline">
                            <input type="checkbox" id="remember-{{ $key }}" name="remember"
                                {{ old('_mode') === $key && old('remember') ? 'checked' : '' }} />
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="submit shine">
                        <span class="btn-text">{{ $door['submitLabel'] }}</span>
                        <span class="spinner" aria-hidden="true"></span>
                    </button>

                    @if(!empty($door['registerUrl']))
                        <a href="{{ $door['registerUrl'] }}" class="submit shine"
                           style="display:flex; align-items:center; justify-content:center; margin-top:10px; text-decoration:none; background:var(--surface); color:var(--primary); border:2px solid var(--primary);">
                            {{ $door['registerText'] }}
                        </a>
                    @endif
                </form>
            </div>
        @endforeach
    </main>

    <script>
        const doorSelection = document.getElementById('doorSelection');
        const doorForms = document.querySelectorAll('.door-form');

        function showDoor(mode) {
            doorSelection.style.display = 'none';
            doorForms.forEach(f => {
                f.style.display = f.id === 'form-' + mode ? 'block' : 'none';
            });
        }

        function showSelection() {
            doorSelection.style.display = 'grid';
            doorForms.forEach(f => f.style.display = 'none');
        }

        document.querySelectorAll('.door-card').forEach(card => {
            card.addEventListener('click', () => showDoor(card.dataset.mode));
        });

        document.querySelectorAll('[data-back]').forEach(btn => {
            btn.addEventListener('click', showSelection);
        });

        @if(!empty($activeMode))
            showDoor(@json($activeMode));
        @endif

        // wire up password toggle, submit spinner, and focus micro-interaction per form
        doorForms.forEach(block => {
            const toggleBtn = block.querySelector('.toggle');
            const pwdInput = block.querySelector('input[type="password"]');
            toggleBtn?.addEventListener('click', () => {
                const icon = toggleBtn.querySelector('i');
                const isPwd = pwdInput.type === 'password';
                pwdInput.type = isPwd ? 'text' : 'password';
                icon.className = isPwd ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            });

            const form = block.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner');
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                spinner.style.display = 'inline-block';
            });

            block.querySelectorAll('.input').forEach(el => {
                el.addEventListener('focus', () => el.parentElement.style.transform = 'scale(1.01)');
                el.addEventListener('blur', () => el.parentElement.style.transform = 'scale(1)');
            });
        });
    </script>
</body>

</html>
