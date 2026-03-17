@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Top-Up Application</h3>
    <p>Existing Loan: #{{ $loanAccount->account_id }} | Outstanding: ₹ {{ number_format($loanAccount->current_balance,2) }}</p>
    <form method="POST" action="{{ route('member.topup.submit') }}">
        @csrf
        <div class="form-group">
            <label>New Loan Amount (max 800000)</label>
            <input type="number" name="new_amount" class="form-control" required min="1000" max="800000">
        </div>
        <button class="btn btn-primary">Apply for Top-Up</button>
    </form>
</div>
@endsection
