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
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
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
        }
        
        .reset-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .reset-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            padding: 40px;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reset-header .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin: 0 auto 15px;
        }
        
        .reset-header h1 {
            font-size: 24px;
            color: var(--primary-color);
            margin: 0 0 5px 0;
            font-weight: 700;
        }
        
        .reset-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }
        
        .input-group .form-control {
            padding-left: 45px;
        }
        
        .btn-reset {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-back {
            width: 100%;
            padding: 12px;
            background: #f0f0f0;
            color: var(--primary-color);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
            cursor: pointer;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .reset-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .reset-footer a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .reset-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <div class="logo"><i class="fas fa-key"></i></div>
                <h1>Reset Password</h1>
                <p>Enter your email and new password</p>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Reset Failed</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.reset.post') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <span class="icon"><i class="fas fa-envelope"></i></span>
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
                    </div>
                    @error('email')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <div class="input-group">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                    <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>
                
                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-check"></i> Reset Password
                </button>
                
                <a href="{{ route('admin.login') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </form>
            
            <div class="reset-footer">
                <p>Remember your password? <a href="{{ route('admin.login') }}">Login here</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
