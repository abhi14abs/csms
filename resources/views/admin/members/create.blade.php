@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="card card-bordered">
                    <div class="card-header">
                        <h5 class="card-title">Create Member</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.members.store') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input name="name" class="form-control" placeholder="Enter full name"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                                        <input name="empCode" class="form-control" placeholder="Enter employee code"
                                            value="{{ old('empCode') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Select gender</option>
                                            <option value="MALE" {{ old('gender') == 'MALE' ? 'selected' : '' }}>MALE
                                            </option>
                                            <option value="FEMALE" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>FEMALE
                                            </option>
                                            <option value="OTHER" {{ old('gender') == 'OTHER' ? 'selected' : '' }}>OTHER
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select name="category" class="form-control" required>
                                            <option value="">Select category</option>
                                            <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>
                                                General</option>
                                            <option value="OBC" {{ old('category') == 'OBC' ? 'selected' : '' }}>OBC
                                            </option>
                                            <option value="SC" {{ old('category') == 'SC' ? 'selected' : '' }}>SC
                                            </option>
                                            <option value="ST" {{ old('category') == 'ST' ? 'selected' : '' }}>ST
                                            </option>
                                            <option value="EWS" {{ old('category') == 'EWS' ? 'selected' : '' }}>EWS
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input name="dateOfBirth" type="date" class="form-control"
                                            value="{{ old('dateOfBirth') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Appointment <span
                                                class="text-danger">*</span></label>
                                        <input name="dateOfAppointment" type="date" class="form-control"
                                            value="{{ old('dateOfAppointment') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Retirement <span
                                                class="text-danger">*</span></label>
                                        <input name="dateOfRetirement" type="date" class="form-control"
                                            value="{{ old('dateOfRetirement') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="EXISTING"
                                                {{ old('status', 'EXISTING') == 'EXISTING' ? 'selected' : '' }}>EXISTING
                                            </option>
                                            <option value="RETIRED" {{ old('status') == 'RETIRED' ? 'selected' : '' }}>
                                                RETIRED</option>
                                            <option value="TRANSFERRED"
                                                {{ old('status') == 'TRANSFERRED' ? 'selected' : '' }}>TRANSFERRED</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Designation at Appointment <span
                                                class="text-danger">*</span></label>
                                        <input name="designationAtAppointment" class="form-control"
                                            placeholder="Enter designation at appointment"
                                            value="{{ old('designationAtAppointment') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Present Posting <span class="text-danger">*</span></label>
                                        <input name="presentPosting" class="form-control"
                                            placeholder="Enter present posting" value="{{ old('presentPosting') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone / Mobile</label>
                                        <input name="mobile" class="form-control"
                                            placeholder="Enter phone or mobile number" value="{{ old('mobile') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">E-mail</label>
                                        <input name="email" type="email" class="form-control"
                                            placeholder="Enter email address" value="{{ old('email') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Home Town</label>
                                        <input name="homeTown" class="form-control" placeholder="Enter home town"
                                            value="{{ old('homeTown') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Residential Address</label>
                                        <textarea name="residentialAddress" class="form-control" rows="2" placeholder="Enter residential address">{{ old('residentialAddress') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">Create Member</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
