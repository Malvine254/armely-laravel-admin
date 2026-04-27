<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - Armely Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2f5597 0%, #1f4788 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .small-text {
            font-size: 0.9rem;
            margin-top: 2rem;
            opacity: 0.7;
        }

        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            p {
                font-size: 1rem;
            }
            .icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>We'll Be Right Back</h1>
        <p>Our site is currently undergoing scheduled maintenance.</p>
        <p>We should be back shortly. Thank you for your patience!</p>
        <div class="badge">⚙️ Maintenance Mode</div>
        <p class="small-text">If you're an administrator, please log in to access the admin panel.</p>
    </div>
</body>
</html>
