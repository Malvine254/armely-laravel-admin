<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Armely</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2f5597;
            --secondary-color: #1e3a6d;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .login-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 45px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header .logo {
            width: 75px;
            height: 75px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(47, 85, 151, 0.3);
        }
        
        .login-header h1 {
            font-size: 28px;
            color: var(--primary-color);
            margin: 0 0 8px 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            color: #666;
            margin: 0;
            font-size: 15px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #e8eef5;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(47, 85, 151, 0.15);
            background: white;
            outline: none;
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            pointer-events: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .input-group .form-control:focus ~ .icon,
        .input-group:focus-within .icon {
            color: var(--primary-color);
        }
        
        .input-group .form-control {
            padding-left: 50px;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-top: 15px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(47, 85, 151, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(47, 85, 151, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e8eef5;
        }
        
        .login-footer p {
            color: #666;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-footer a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            margin-bottom: 25px;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .alert-danger {
            background-color: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }
        
        .alert-danger strong {
            font-weight: 600;
        }
        
        .alert-danger ul {
            padding-left: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-success strong {
            font-weight: 600;
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 40px 30px;
            }
            
            .login-container {
                max-width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo"><i class="fas fa-shield-alt"></i></div>
                <h1>Armely Admin</h1>
                <p>Secure Admin Panel</p>
            </div>
            
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Login Failed</strong>
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
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            placeholder="admin@armely.com"
                            value="{{ old('email') }}"
                            required 
                            autofocus
                        >
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                    </div>
                    @error('email')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                        <span class="icon"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>Forgot your password? <a href="{{ route('admin.reset') }}">Reset here</a></p>
                <p>Not an admin? <a href="/">Go back home</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
