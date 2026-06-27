<style>
    .lims-brand-text {
        font-weight: 700;
        font-size: 1.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #2c3e50;
        /* fallback */
        text-shadow: 3px 1px 1px rgba(0, 0, 0, 0.2);
    }

    /* Optional gradient style */
    .text-gradient {
        background: linear-gradient(90deg, #f52601, #3F41D1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="nk-sidebar nk-sidebar-fixed is-light" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ url('/') }}" class="logo-link nk-sidebar-logo d-flex align-items-center">
                <img class="logo-light logo-img me-2" src="{{ asset('backAssets/images/logo.png') }}" alt="logo">
                <img class="logo-dark logo-img me-2" src="{{ asset('backAssets/images/logo.png') }}" alt="logo">
                <img class="logo-small logo-img logo-img-small me-2" src="{{ asset('backAssets/images/logo.png') }}"
                    alt="logo-small">
                <span class="lims-brand-text text-gradient">CSMS</span>
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    @php
                        use Illuminate\Support\Facades\Auth;
                        $user = Auth::user();
                    @endphp

                    @if ($user && $user->is_admin)
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
                                <span class="nk-menu-text">Overview</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.monthly-due') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-download"></em></span>
                                <span class="nk-menu-text">Monthly Due</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.pending-loans') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-alert-fill"></em></span>
                                <span class="nk-menu-text">Pending Loans</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.batch.form') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-repeat"></em></span>
                                <span class="nk-menu-text">Process Batch</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.historical-import') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-file-xls"></em></span>
                                <span class="nk-menu-text">Historical Import</span>
                            </a>
                        </li>
                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                <span class="nk-menu-text">Members</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.members.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">List Members</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.members.create') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">Create Member</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.members.create-login') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">Create Login</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.ledger.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">Member Ledger & Audit</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.ledger.overview') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">Overview</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-wallet-fill"></em></span>
                                <span class="nk-menu-text">Loan Management</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('loans.dashboard') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">Dashboard</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('loans.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">All Loans</span></a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('loans.create') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">New Loan</span></a>
                                </li>
                            </ul>
                        </li>
                    @elseif($user)
                        <li class="nk-menu-item">
                            <a href="{{ route('member.dashboard') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
                                <span class="nk-menu-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('member.loan.apply') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                <span class="nk-menu-text">Apply Loan</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('member.topup') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-arrow-up-right"></em></span>
                                <span class="nk-menu-text">Top Up</span>
                            </a>
                        </li>
                    @else
                        <li class="nk-menu-item">
                            <a href="{{ route('login') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-signin"></em></span>
                                <span class="nk-menu-text">Log in</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
