<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Armely Admin</title>
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
        
        .reset-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .reset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 45px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }
        
        .reset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .reset-header .logo {
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
        
        .reset-header h1 {
            font-size: 28px;
            color: var(--primary-color);
            margin: 0 0 8px 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .reset-header p {
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
        
        .btn-reset {
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
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(47, 85, 151, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-reset:active {
            transform: translateY(0);
        }
        
        .reset-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e8eef5;
        }
        
        .reset-footer p {
            color: #666;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .reset-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .reset-footer a:hover {
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
        
        .text-muted {
            color: #6c757d;
            font-size: 13px;
        }
        
        @media (max-width: 576px) {
            .reset-card {
                padding: 40px 30px;
            }
            
            .reset-container {
                max-width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <div class="logo"><i class="fas fa-key"></i></div>
                <h1>Set New Password</h1>
                <p>Create a secure password for your account</p>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Error</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            value="{{ $email }}"
                            disabled
                        >
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            autofocus
                        >
                        <span class="icon"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle"></i> Minimum 8 characters
                    </small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="••••••••"
                            required
                        >
                        <span class="icon"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-check"></i> Reset Password
                </button>
            </form>
            
            <div class="reset-footer">
                <p><a href="{{ route('admin.login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
