<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

<body>
    <style>
        :root {
            --accent-1: #4e54c8;
            --accent-2: #8f94fb;
            --btn: #ffcc00;
            --text: #ffffff;
            --muted: rgba(255, 255, 255, 0.85)
        }

        html,
        body {
            height: 100%;
            margin: 0
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Helvetica Neue', Arial;
            color: var(--text)
        }

        .hero-viewport {
            height: 100vh;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            overflow: hidden
        }

        .hero-card {
            width: 100%;
            max-width: 1200px;
            margin: 0 20px;
            display: flex;
            gap: 28px;
            align-items: center;
            justify-content: space-between
        }

        .hero-left {
            flex: 1;
            min-width: 280px;
            padding: 34px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.18);
            backdrop-filter: blur(6px)
        }

        .hero-right {
            width: 420px;
            flex-shrink: 0;
            height: 420px;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.12)
        }

        .kicker {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--btn);
            font-weight: 700;
            font-size: 13px
        }

        h1.title {
            margin: 14px 0 10px;
            font-size: 32px;
            color: var(--text);
            line-height: 1.06
        }

        p.lead {
            margin: 0 0 18px;
            color: var(--muted);
            max-width: 58ch
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 16px;
            background: var(--btn);
            color: #000;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700
        }

        .btn-ghost {
            display: inline-block;
            padding: 10px 16px;
            background: transparent;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: var(--text);
            text-decoration: none
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 18px;
            color: var(--muted);
            font-size: 14px
        }

        .feature {
            display: flex;
            gap: 10px;
            align-items: flex-start
        }

        .feature .dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            background: var(--btn);
            margin-top: 6px
        }

        /* responsive */
        @media (max-width:900px) {
            .hero-card {
                flex-direction: column-reverse;
                align-items: stretch
            }

            .hero-right {
                width: 100%;
                height: 260px
            }
        }
    </style>

    <div class="hero-viewport">
        <div class="hero-card">
            <div class="hero-left">
                <span class="kicker">Textile Committee</span>
                <h1 class="title">Cooperative Society Management System</h1>
                <p class="lead">Centralize member subscriptions, payroll deductions, savings and structured loans for
                    cooperative textile societies. Track disbursals, manage sureties and run monthly batch deductions
                    with precision.</p>

                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
                    @auth
                        @php $user = Illuminate\Support\Facades\Auth::user(); @endphp
                        @if ($user && $user->is_admin)
                            <a href="{{ route('admin.monthly-due') }}" class="btn-primary">Admin Dashboard</a>
                        @else
                            <a href="{{ route('member.dashboard') }}" class="btn-primary">Member Dashboard</a>
                        @endif
                        <a href="{{ route('logout') }}" class="btn-ghost">Sign out</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Log in</a>
                    @endauth
                </div>

                <div class="feature-list">
                    <div class="feature">
                        <div class="dot"></div>
                        <div>Automatic subscription split (Share / Savings)</div>
                    </div>
                    <div class="feature">
                        <div class="dot"></div>
                        <div>Loan underwriting: collateral check & surety management</div>
                    </div>
                    <div class="feature">
                        <div class="dot"></div>
                        <div>Top-up / refinance with repayment checks</div>
                    </div>
                    <div class="feature">
                        <div class="dot"></div>
                        <div>Monthly batch payroll deductions & reporting</div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div style="text-align:center;max-width:320px;padding:8px;">
                    <img src="{{ asset('backAssets/images/logo.png') }}" alt="CSMS"
                        style="width:96px;height:96px;object-fit:contain;margin-bottom:18px;filter:grayscale(10%)">
                    <h3 style="margin:0;font-size:20px;color:var(--ink)">Textile Committee</h3>
                    {{-- <p style="margin:8px 0 0;color:var(--muted);font-size:14px">Connect your mill payroll with cooperative deductions — safe, auditable and member-centric.</p> --}}
                </div>
            </div>
        </div>
    </div>
    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>
</html>
