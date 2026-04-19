<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Greenaxe Offer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            width: 100%;
        }

        .login-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            margin: 25px 0;
            position: relative;
            text-align: center;
            color: #999;
            font-size: 13px;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            background: white;
            padding: 0 10px;
            position: relative;
        }

        .api-hint {
            background: #f0f7ff;
            border: 1px solid #d4e6f1;
            border-radius: 4px;
            padding: 12px;
            color: #2c3e50;
            font-size: 12px;
            line-height: 1.5;
            margin-top: 20px;
        }

        .api-hint strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="header">
                <h1>🌱 Greenaxe Offer</h1>
                <p>Garden Project Estimator</p>
            </div>

            @if ($errors->any())
                <div style="background: #ffe5e5; border: 1px solid #ffb3b3; color: #c41e3a; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="hello@jirasoft.pl"
                    />
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Enter password"
                    />
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="api-hint">
                <strong>Demo Credentials:</strong><br>
                Email: hello@jirasoft.pl<br>
                Password: admin123
            </div>

            <div class="divider">
                <span>or use API</span>
            </div>

            <div class="api-hint">
                <strong>API Endpoint:</strong><br>
                POST /api/login<br>
                Body: { "email": "hello@jirasoft.pl", "password": "admin123" }
            </div>
        </div>
    </div>
</body>
</html>
