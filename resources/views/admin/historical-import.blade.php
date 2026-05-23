@extends('layouts.app')
@section('title', 'Historical Import')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Historical Data Import</h3>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="alert alert-warning mb-4">
                        <div class="d-flex">
                            <em class="icon ni ni-alert-circle me-2 fs-4"></em>
                            <div>
                                <strong>Important!</strong> This form is strictly for importing <strong>Legacy Excel Format</strong> data containing deductions from the previous 12 months. Ensure the uploaded Excel has the headers configured to include fields like <code>Emp No.</code>, <code>Shares</code>, and <code>EMI_*</code>.
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.historical-import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label" for="month">Select Month</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select form-control" name="month" id="month" required>
                                            <option value="">Choose...</option>
                                            @for($i=1; $i<=12; $i++)
                                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    @error('month')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label" for="year">Select Year</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="year" id="year" value="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}" required>
                                    </div>
                                    @error('year')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-8">
                                <div class="form-group">
                                    <label class="form-label" for="file">Upload Legacy Excel (.xlsx, .xls, .csv)</label>
                                    <div class="form-control-wrap">
                                        <div class="form-file">
                                            <input type="file" class="form-file-input" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                                            <label class="form-file-label" for="file">Choose file</label>
                                        </div>
                                    </div>
                                    @error('file')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                    <em class="icon ni ni-upload me-1"></em> Process Historical Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
