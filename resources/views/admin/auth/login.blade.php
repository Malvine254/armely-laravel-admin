<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Armely</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
    <style>
        :root { --primary-color:#2f5597; --secondary-color:#1e3a6d; --ink:#17233c; --muted:#667085; }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh; margin: 0; padding: 24px; display: flex; align-items: center; justify-content: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; color: var(--ink);
            background: linear-gradient(135deg, rgba(47,85,151,.96), rgba(30,58,109,.98)); position: relative; overflow-x: hidden;
        }
        body::before {
            content: ""; position: fixed; inset: 0; pointer-events: none;
            background-image: linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 44px 44px;
        }
        .auth-container { width: 100%; max-width: 420px; position: relative; z-index: 1; }
        .auth-card {
            background: rgba(255,255,255,.98); border: 1px solid rgba(255,255,255,.75); border-radius: 16px;
            box-shadow: 0 24px 70px rgba(8,22,48,.34), 0 2px 8px rgba(8,22,48,.08);
            padding: 34px 34px 30px; backdrop-filter: blur(12px);
        }
        .auth-header { text-align: center; margin-bottom: 26px; }
        .auth-logo {
            width: 58px; height: 58px; margin: 0 auto 16px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: #fff; font-size: 25px;
            box-shadow: 0 10px 22px rgba(47,85,151,.25);
        }
        .auth-header h1 { color: var(--primary-color); font-size: 24px; font-weight: 800; margin: 0 0 6px; letter-spacing: 0; }
        .auth-header p { color: var(--muted); font-size: 14px; margin: 0; font-weight: 500; }
        .form-group { margin-bottom: 18px; }
        .form-label { color: var(--primary-color); display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; }
        .input-group { position: relative; }
        .input-group .icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #98a2b3; pointer-events: none; z-index: 5; }
        .form-control {
            border: 1px solid #d9e2ef; border-radius: 9px !important; background: #f8fafc; color: var(--ink);
            font-size: 14px; min-height: 46px; padding: 11px 14px 11px 44px; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .form-control:focus { background: #fff; border-color: var(--primary-color); box-shadow: 0 0 0 .22rem rgba(47,85,151,.14); }
        .btn-auth {
            width: 100%; min-height: 46px; border: 0; border-radius: 9px; margin-top: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: #fff;
            font-size: 15px; font-weight: 800; box-shadow: 0 10px 22px rgba(47,85,151,.24); transition: transform .2s ease, box-shadow .2s ease;
        }
        .btn-auth:hover { color:#fff; transform: translateY(-1px); box-shadow: 0 14px 26px rgba(47,85,151,.3); }
        .auth-footer { text-align: center; margin-top: 22px; padding-top: 18px; border-top: 1px solid #e8eef5; }
        .auth-footer p { color: var(--muted); font-size: 14px; margin: 0; }
        .auth-footer a { color: var(--primary-color); font-weight: 800; text-decoration: none; }
        .auth-footer a:hover { color: var(--secondary-color); text-decoration: underline; }
        .alert { border: 0; border-radius: 10px; margin-bottom: 20px; padding: 12px 14px; font-size: 14px; box-shadow: 0 6px 18px rgba(15,23,42,.08); }
        .alert-danger { background: #fff1f1; color: #b42318; border-left: 4px solid #d92d20; }
        .alert-success { background: #ecfdf3; color: #027a48; border-left: 4px solid #12b76a; }
        @media (max-width: 576px) { body { padding: 16px; } .auth-card { padding: 28px 22px; } }
    </style>
</head>
<body>
    <main class="auth-container">
        <section class="auth-card">
            <div class="auth-header">
                <div class="auth-logo"><i class="fas fa-shield-alt"></i></div>
                <h1>Armely Admin</h1>
                <p>Sign in to manage website content</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i>{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Login failed</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="admin@armely.com" value="{{ old('email') }}" required autofocus>
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                    </div>
                    @error('email')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <span class="icon"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
                </div>

                <button type="submit" class="btn btn-auth"><i class="fas fa-sign-in-alt me-2"></i>Login</button>
            </form>

            <div class="auth-footer">
                <p>Forgot your password? <a href="{{ route('admin.reset') }}">Reset here</a></p>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
