<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke Sistem Verifikasi Tanda Tangan Elektronik">
    <title>Login — Jasa Izin Verified</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0f1117; --surface: #1a1d27; --surface2: #22263a;
            --border: #2e3348; --accent: #4f7cff; --accent2: #7c5cfc;
            --red: #ef4444; --text: #e2e8f0; --muted: #8892a4;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(79,124,255,.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(124,92,252,.1) 0%, transparent 50%);
            pointer-events: none;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 36px;
            position: relative;
            z-index: 1;
            box-shadow: 0 24px 64px rgba(0,0,0,.4);
        }
        .brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .brand-icon {
            width: 60px; height: 60px;
            margin: 0 auto 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 8px 24px rgba(79,124,255,.3);
        }
        .brand h1 { font-size: 1.4rem; font-weight: 700; }
        .brand p  { font-size: .82rem; color: var(--muted); margin-top: 4px; }

        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: .8rem; font-weight: 500; color: var(--muted); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: .9rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,124,255,.15);
        }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { font-size: .75rem; color: var(--red); margin-top: 4px; display: block; }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px;
        }
        .remember-row input[type="checkbox"] { accent-color: var(--accent); width: 15px; height: 15px; cursor: pointer; }
        .remember-row label { font-size: .82rem; color: var(--muted); cursor: pointer; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: .95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            box-shadow: 0 4px 16px rgba(79,124,255,.35);
        }
        .btn-login:hover   { opacity: .92; }
        .btn-login:active  { transform: scale(.99); }

        .alert-danger {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            color: var(--red);
            padding: 10px 14px;
            border-radius: 10px;
            font-size: .82rem;
            margin-bottom: 16px;
        }
        .footer-text {
            text-align: center;
            font-size: .73rem;
            color: var(--muted);
            margin-top: 24px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <img src="{{ asset('logo.png') }}" style="width: 75px; height: auto; margin-bottom: 12px; filter: drop-shadow(0 4px 12px rgba(79,124,255,.15));" alt="Logo Jasa Izin Verified">
        <h1 style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0; line-height: 1.2;">Jasa Izin Verified</h1>
        <p style="font-size: .82rem; color: var(--muted); margin-top: 6px;">Sistem Verifikasi Tanda Tangan Elektronik</p>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Alamat Email</label>
            <input type="email" id="email" name="email"
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="admin@tte.local"
                   autocomplete="email" required autofocus>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="••••••••"
                   autocomplete="current-password" required>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="remember-row">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn-login">Masuk</button>
    </form>

    <div style="margin-top: 16px; border-top: 1px dashed var(--border); padding-top: 16px; text-align: center;">
        <a href="{{ route('verifikasi.index') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 12px; background: rgba(79,124,255,.07); border: 1px solid rgba(79,124,255,.2); border-radius: 10px; color: var(--accent); font-size: .92rem; font-weight: 600; text-decoration: none; font-family: 'Inter', sans-serif; transition: all .2s; box-shadow: 0 4px 12px rgba(79,124,255,.05);">
            🔍 Verifikasi Mandiri Dokumen (TTE)
        </a>
    </div>

    <p class="footer-text">© {{ date('Y') }} Jasa Izin Verified — Sistem Verifikasi TTE</p>
</div>
</body>
</html>
