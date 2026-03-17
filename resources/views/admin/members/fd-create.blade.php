@extends('layouts.app')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Create Fixed Deposit</h3>
                <div class="nk-block-des text-soft">
                    <p>Add a new Fixed Deposit for <strong>{{ $member->name }}</strong> ({{ $member->empCode }})</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('admin.members.index') }}"
                    class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                        class="icon ni ni-arrow-left"></em><span>Back to List</span></a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('admin.members.fd.store', $member->id) }}" method="POST">
                    @csrf
                    <div class="row g-gs">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="amount">FD Amount (₹)</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="amount" name="amount" required
                                        min="1" step="0.01" placeholder="Enter Amount">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="tx_date">Deposit Date</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" id="tx_date" name="tx_date" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Remarks / Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" id="description" name="description"
                                        placeholder="e.g. Manual FD creation from society funds"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Fixed Deposit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
