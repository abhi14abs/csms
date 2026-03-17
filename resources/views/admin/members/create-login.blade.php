@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row pt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white"><em class="icon ni ni-user-add me-2"></em>Create Member Login</h5>
                        <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-light">
                            <em class="icon ni ni-arrow-left me-1"></em> Back to Members
                        </a>
                    </div>
                    <div class="card-body p-4">

                        @if (session('message'))
                            <div class="alert alert-{{ session('type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
                                role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.members.create-login') }}" method="POST">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_id" class="form-label fw-bold">Select Member <span
                                                class="text-danger">*</span></label>
                                        <select name="employee_id" id="employee_id"
                                            class="form-select @error('employee_id') is-invalid @enderror" required>
                                            <option value="">-- Select Member --</option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ old('employee_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }} ({{ $member->empCode }}) -
                                                    {{ $member->email ?? 'No email' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Only members without login credentials and have an email
                                            address set are shown.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label fw-bold">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" required minlength="6"
                                            placeholder="Enter temporary password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 6 characters. The username will be the email
                                            address.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <em class="icon ni ni-save me-1"></em> Create Login
                                </button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
