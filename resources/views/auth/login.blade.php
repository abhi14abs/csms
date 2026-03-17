<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <title>CSMS Login</title>
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #c7c7e6, #8f94fb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            border: none;
            font-size: 18px;
            border-radius: 10px;
            background: #ffcc00;
            color: #000;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #ffdd33;
            transform: scale(1.03);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .footer-link {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.8;
        }

        .footer-link a {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Welcome Back</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control" name="email" required autofocus>
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control" name="password" required>
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="remember">
                <input type="checkbox" name="remember"> <span>Remember me</span>
            </div>
            <button class="btn-primary">Login</button>
            <div class="footer-link">
                <p>Forgot your password? <a href="#">Click Here</a></p>
            </div>
        </form>
    </div>
</body>

</html>
