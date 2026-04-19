<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Greenaxe Offer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        header h1 {
            font-size: 24px;
        }

        .nav-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 13px;
        }

        .user-email {
            font-weight: 600;
        }

        .user-role {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .welcome-section {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .welcome-section h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .welcome-section p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .card p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .stat-box {
            background: #f0f7ff;
            border: 1px solid #d4e6f1;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .api-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #856404;
        }

        .api-section strong {
            color: #664d03;
        }
    </style>
</head>
<body>
    <header>
        <h1>🌱 Greenaxe Offer</h1>
        <div class="nav-right">
            <div class="user-info">
                <div class="user-email">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome, {{ Auth::user()->name }}!</h2>
            <p>You have successfully logged in to Greenaxe Offer - the garden project estimator tool.</p>

            <div class="stats">
                <div class="stat-box">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Clients</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Projects</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Offers</div>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h3>📋 Manage Clients</h3>
                <p>View, create, and manage your garden project clients.</p>
                <a href="/api/clients" class="btn-primary">API Endpoint</a>
            </div>

            <div class="card">
                <h3>🌿 Garden Projects</h3>
                <p>Create and organize garden design projects for your clients.</p>
                <a href="/api/garden-projects" class="btn-primary">API Endpoint</a>
            </div>

            <div class="card">
                <h3>💰 Offers & Estimates</h3>
                <p>Generate detailed cost estimates and offers for garden projects.</p>
                <a href="/api/offers" class="btn-primary">API Endpoint</a>
            </div>

            <div class="card">
                <h3>🔧 Cost Items</h3>
                <p>Manage line items and costs within your estimates.</p>
                <a href="/api/cost-items" class="btn-primary">API Endpoint</a>
            </div>
        </div>

        <div class="api-section">
            <strong>💡 API Note:</strong> All API endpoints require authentication. Include your session cookie or use the /api/login endpoint with your credentials to get a Sanctum token.
        </div>
    </div>
</body>
</html>
