{{--
    Shared look of the K-Connect error pages (404, 419, 500, 503).
    Self-contained: no session, database or partials, so it renders even when those are the problem.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('title') - K-Connect</title>
    <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kc-blue: #009ada;
            --kc-green: #76bc21;
            --kc-dark: #0f172a;
            --kc-muted: #64748b;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 24px 16px;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 50%, #f3fbe9 100%);
            color: var(--kc-dark);
        }
        .error-card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            text-align: center;
        }
        .error-card .brand-bar {
            height: 6px;
            background: linear-gradient(90deg, var(--kc-blue), var(--kc-green));
        }
        .error-card .card-inner {
            padding: 2.5rem 2rem 2rem;
        }
        .error-logo {
            max-height: 48px;
            max-width: 180px;
            margin-bottom: 1.75rem;
        }
        .error-icon {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
            background: linear-gradient(135deg, var(--kc-blue), var(--kc-green));
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--kc-blue);
            margin-bottom: 0.5rem;
        }
        .error-card h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }
        .error-card p {
            color: var(--kc-muted);
            margin-bottom: 0;
        }
        .error-detail {
            margin-top: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 0.875rem;
            text-align: left;
            word-break: break-word;
        }
        .error-actions {
            margin-top: 1.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }
        .btn-kc {
            background: linear-gradient(135deg, var(--kc-blue), var(--kc-green));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            border-radius: 50px;
        }
        .btn-kc:hover, .btn-kc:focus {
            color: #fff;
            opacity: 0.92;
        }
        .btn-kc-outline {
            border: 2px solid var(--kc-blue);
            color: var(--kc-blue);
            font-weight: 600;
            padding: 0.55rem 1.4rem;
            border-radius: 50px;
            background: transparent;
        }
        .btn-kc-outline:hover, .btn-kc-outline:focus {
            background: var(--kc-blue);
            color: #fff;
        }
        .error-footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="brand-bar"></div>
        <div class="card-inner">
            <a href="{{ url('/') }}">
                <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT" class="error-logo"
                     onerror="this.style.display='none'">
            </a>
            <div>
                <span class="error-icon"><i class="fas @yield('icon', 'fa-exclamation-triangle')"></i></span>
            </div>
            <div class="error-code">Error @yield('code')</div>
            <h1>@yield('heading')</h1>
            <p>@yield('message')</p>
            @yield('detail')
            <div class="error-actions">
                @yield('actions')
                <a href="{{ url('/') }}" class="btn btn-kc"><i class="fas fa-home me-2"></i>Go to Home</a>
            </div>
            <div class="error-footer">&copy; {{ date('Y') }} Kiboko Group &middot; K-Connect</div>
        </div>
    </main>
</body>
</html>
