<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greenaxe - Zarządzanie Projektami Ogrodów</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: white;
            color: #1f2937;
            line-height: 1.6;
        }

        nav {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #16a34a;
        }

        nav a {
            color: #6b7280;
            text-decoration: none;
            margin-left: 2rem;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav a:hover {
            color: #16a34a;
        }

        .hero {
            background: #f9fafb;
            padding: 100px 20px;
            text-align: center;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #16a34a;
            font-weight: 800;
        }

        .hero p {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
        }

        .btn-primary:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: white;
            color: #16a34a;
            border: 2px solid #16a34a;
        }

        .btn-secondary:hover {
            background: #f0fdf4;
        }

        .features {
            padding: 80px 20px;
            background: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #1f2937;
            font-weight: 700;
        }

        .section-title p {
            font-size: 16px;
            color: #6b7280;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
            transition: all 0.2s;
        }

        .feature-card:hover {
            border-color: #16a34a;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.1);
        }

        .feature-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: #1f2937;
            font-weight: 600;
        }

        .feature-card p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        footer {
            background: #1f2937;
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid #111827;
        }

        footer p {
            font-size: 14px;
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .cta-buttons {
                flex-direction: column;
            }

            nav a {
                display: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <div class="logo">Greenaxe</div>
            <div>
                <a href="#features">Funkcje</a>
                <a href="#tech">Technologia</a>
                <a href="/login">Logowanie</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Greenaxe CRM</h1>
            <p>Profesjonalny system zarządzania projektami ogrodów</p>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 30px;">Zarządzaj klientami, projektami i generuj profesjonalne oferty w jednym miejscu</p>
            <div class="cta-buttons">
                <a href="/login" class="btn btn-primary">Zaloguj się</a>
                <a href="#features" class="btn btn-secondary">Dowiedz się więcej</a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Zaawansowane Funkcje</h2>
                <p>Wszystko oprócz zarządzania projektami ogrodów</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-title">CRM Klientów</div>
                    <p>Zarządzaj bazą klientów z pełną historią kontaktów, informacjami o firmie i preferencjami komunikacji.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-title">Zarządzanie Projektami</div>
                    <p>Śledź projekty ogrodów, specyfikacje, lokalizacje i style od pierwszych pomysłów do realizacji.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-title">Szacowanie Kosztów</div>
                    <p>Automatyczne obliczanie kosztów materiałów, pracy i marż do generowania profesjonalnych ofert.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-title">Bezpieczna Autentykacja</div>
                    <p>Szyfrowana autoryzacja i bezpieczne API dla integracji z innymi systemami.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-title">Szybkość i Niezawodność</div>
                    <p>Zbudowane na nowoczesnym stosie technologicznym dla maksymalnej wydajności i skalowości.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-title">Responsywny Design</div>
                    <p>Dostęp do systemu na dowolnym urządzeniu - komputer, tablet lub smartfon.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="tech" style="background: #f9fafb;">
        <div class="container">
            <div class="section-title">
                <h2>Nowoczesna Technologia</h2>
                <p>Zbudowane na niezawodnych narzędziach klasy enterprise</p>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;">
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">Laravel 13</span>
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">PHP 8.5</span>
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">MySQL 8</span>
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">Docker</span>
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">REST API</span>
                <span style="background: white; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #16a34a; border: 1px solid #e5e7eb;">Sanctum Auth</span>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2026 Greenaxe. Zarządzanie projektami ogrodów made simple.</p>
        </div>
    </footer>
</body>
</html>
