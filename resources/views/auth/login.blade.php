<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Greenaxe CRM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #f0fdf4 100%);
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            border: 1px solid rgba(22, 163, 74, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 28px;
            color: #16a34a;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            background: white;
            font-family: inherit;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }

        .error-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .form-error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: #15803d;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
        }

        .footer-text a {
            color: #16a34a;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="header">
                <h1>Greenaxe</h1>
                <p>CRM Zarządzania Projektami Ogrodów</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    ✗ {{ $errors->first() }}
                </div>
            @endif

            <form method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="Wpisz adres email"
                        autocomplete="email"
                    />
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Wpisz hasło"
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Zaloguj się</button>
            </form>

            <div class="footer-text">
                <p>Greenaxe CRM - profesjonalny system zarządzania projektami ogrodów.</p>
                <p style="margin-top: 10px;">
                    Potrzebujesz konta? <a href="#contact">Skontaktuj się z nami</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
