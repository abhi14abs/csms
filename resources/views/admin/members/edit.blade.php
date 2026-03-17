@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="card card-bordered">
                    <div class="card-header">
                        <h5 class="card-title">Edit Member</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.members.update', $member->id) }}">
                            @csrf

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Employee Code</label>
                                        <input name="empCode" class="form-control" value="{{ $member->empCode }}"
                                            placeholder="Enter employee code" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Name</label>
                                        <input name="name" class="form-control" value="{{ $member->name }}"
                                            placeholder="Enter name" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Region</label>
                                        <input name="region" class="form-control" value="{{ $member->region }}"
                                            placeholder="Enter region">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone / Mobile</label>
                                        <input name="mobile" class="form-control" value="{{ $member->mobile }}"
                                            placeholder="Enter phone/mobile">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Designation</label>
                                        <input name="designationAtPresent" class="form-control"
                                            value="{{ $member->designationAtPresent }}"
                                            placeholder="Enter designation (Present)" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">E-mail</label>
                                        <input name="email" type="email" class="form-control"
                                            value="{{ $member->email }}" placeholder="Enter email address">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Section</label>
                                        <input name="section" class="form-control" value="{{ $member->section }}"
                                            placeholder="Enter section">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Birth</label>
                                        <input name="dateOfBirth" type="date" class="form-control"
                                            value="{{ $member->dateOfBirth }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Appointment</label>
                                        <input name="dateOfAppointment" type="date" class="form-control"
                                            value="{{ $member->dateOfAppointment }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nominee</label>
                                        <input name="nominee_name" class="form-control" value="{{ $member->nominee_name }}"
                                            placeholder="Enter nominee name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Relationship of Nominee</label>
                                        <input name="nominee_relationship" class="form-control"
                                            value="{{ $member->nominee_relationship }}"
                                            placeholder="Enter nominee relationship">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="EXISTING" {{ $member->status == 'EXISTING' ? 'selected' : '' }}>
                                                EXISTING
                                            </option>
                                            <option value="RETIRED" {{ $member->status == 'RETIRED' ? 'selected' : '' }}>
                                                RETIRED</option>
                                            <option value="TRANSFERRED"
                                                {{ $member->status == 'TRANSFERRED' ? 'selected' : '' }}>
                                                TRANSFERRED</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Residential Address</label>
                                        <textarea name="residentialAddress" class="form-control" rows="3" placeholder="Enter residential address">{{ $member->residentialAddress }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Permanent Address</label>
                                        <textarea name="permanent_address" class="form-control" rows="3" placeholder="Enter permanent address">{{ $member->permanent_address }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">Update Member</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
