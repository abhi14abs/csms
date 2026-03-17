@extends('layouts.app')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Mid Term Loan Application</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <form method="POST"
                    action="{{ isset($isAdminView) && $isAdminView ? route('loans.store') : route('member.loan.submit') }}">
                    @csrf

                    <h6 class="title mb-3">Personal Details</h6>
                    <div class="row g-4 mb-4">
                        @if (isset($isAdminView) && $isAdminView)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Select Applicant <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <select name="employee_id" id="applicant-select"
                                            class="form-select js-select2 @error('employee_id') is-invalid @enderror"
                                            data-search="on" data-placeholder="Search Employee..." required>
                                            <option value="">-- select --</option>
                                            @foreach ($membersList as $m)
                                                <option value="{{ $m->id }}"
                                                    @if (old('employee_id') == $m->id) selected @endif>
                                                    #{{ $m->empCode }} - {{ $m->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Applicant Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="applicant-name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Employee No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="applicant-emp" readonly>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Applicant Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" value="{{ $member->name ?? '' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Employee No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" value="{{ $member->empCode ?? '' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Aadhaar No</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="aadhaar" value="{{ old('aadhaar') }}"
                                        class="form-control @error('aadhaar') is-invalid @enderror">
                                    @error('aadhaar')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Mobile No</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="mobile"
                                        class="form-control @error('mobile') is-invalid @enderror"
                                        value="{{ old('mobile', $member->mobile ?? '') }}">
                                    @error('mobile')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Designation</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="designation" id="applicant-designation" class="form-control"
                                        value="{{ $member->designation->name ?? ($member->designationAtPresent ?? '') }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">RO / HQ</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="ro_hq" value="{{ old('ro_hq') }}"
                                        class="form-control @error('ro_hq') is-invalid @enderror">
                                    @error('ro_hq')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Dept</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="dept" id="applicant-dept" class="form-control"
                                        value="{{ $member->department->name ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Service Remaining (months)</label>
                                <div class="form-control-wrap">
                                    <input type="number" name="service_remaining"
                                        value="{{ old('service_remaining') }}"
                                        class="form-control @error('service_remaining') is-invalid @enderror"
                                        step="0.01">
                                    @error('service_remaining')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <div class="form-control-wrap">
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $member->email ?? '') }}">
                                    @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="preview-hr">

                    <h6 class="title mb-3">Loan Requirement</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Reason for Availing Loan</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="reason" value="{{ old('reason') }}"
                                        class="form-control @error('reason') is-invalid @enderror">
                                    @error('reason')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Loan Amount (Rs.) <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="number" name="loan_amount" value="{{ old('loan_amount') }}"
                                        class="form-control @error('loan_amount') is-invalid @enderror" required
                                        min="1000" max="800000">
                                    @error('loan_amount')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Interest Rate (% p.a.)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" value="10.5" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tenure (months) <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="number" name="tenure_months" value="{{ old('tenure_months') }}"
                                        class="form-control @error('tenure_months') is-invalid @enderror" required
                                        min="12" max="120">
                                    @error('tenure_months')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">EMI you would like to pay (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="number" name="emi_desired" value="{{ old('emi_desired') }}"
                                        class="form-control @error('emi_desired') is-invalid @enderror">
                                    @error('emi_desired')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Gross Salary (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="number" name="gross_salary" value="{{ old('gross_salary') }}"
                                        class="form-control @error('gross_salary') is-invalid @enderror">
                                    @error('gross_salary')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Current EMI (if any) (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="number" name="current_emi" value="{{ old('current_emi') }}"
                                        class="form-control @error('current_emi') is-invalid @enderror">
                                    @error('current_emi')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Current Net Salary (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="number" name="net_salary" value="{{ old('net_salary') }}"
                                        class="form-control @error('net_salary') is-invalid @enderror">
                                    @error('net_salary')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Previous Loan (if any) (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="previous_loan" value="{{ old('previous_loan') }}"
                                        class="form-control @error('previous_loan') is-invalid @enderror">
                                    @error('previous_loan')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Loan Outstanding (if any) (Rs.)</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="loan_outstanding" value="{{ old('loan_outstanding') }}"
                                        class="form-control @error('loan_outstanding') is-invalid @enderror">
                                    @error('loan_outstanding')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="preview-hr">

                    <h6 class="title mb-3">Surety Information</h6>
                    <div class="alert alert-icon alert-info" role="alert">
                        <em class="icon ni ni-info"></em>
                        Select three distinct sureties. You can search by Name or Employee ID. Once selected, their details
                        will auto-fill next to them.
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Choose Surety <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Emp No</th>
                                    <th style="width: 20%;">Service Left (months)</th>
                                    <th style="width: 20%;">Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>
                                            <div class="form-control-wrap">
                                                <select name="surety_{{ $i }}"
                                                    class="form-select js-select2 surety-select @error('surety_' . $i) is-invalid @enderror"
                                                    data-search="on" data-placeholder="Search Surety Name or ID..."
                                                    data-index="{{ $i }}" required>
                                                    <option value="">-- select --</option>
                                                    @foreach ($membersList as $m)
                                                        <option value="{{ $m->id }}"
                                                            @if (old('surety_' . $i) == $m->id) selected @endif>
                                                            #{{ $m->empCode }} - {{ $m->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('surety_' . $i)
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="surety_{{ $i }}_emp"
                                                class="form-control surety-emp" data-index="{{ $i }}"
                                                value="{{ old('surety_' . $i . '_emp') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="surety_{{ $i }}_service"
                                                class="form-control surety-service" data-index="{{ $i }}"
                                                step="0.01" value="{{ old('surety_' . $i . '_service') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="surety_{{ $i }}_sign"
                                                class="form-control" value="{{ old('surety_' . $i . '_sign') }}"
                                                readonly>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <hr class="preview-hr">

                    <h6 class="title mb-3">Declaration</h6>
                    <div class="text-soft mb-4">
                        <p>I undertake in the event of the Society sanctioning my application to abide by the rules of the
                            Society and hereby also agree that the monthly EMI could be deducted from my salary immediately
                            due after the disbursement of the loan and to be paid to the Treasurer of the Society. In the
                            event of my death, resignation, retirement, dismissal or otherwise from the service, I hereby
                            authorize Textiles Committee or the other disbursing my salary to recover the whole or part of
                            the salary, gratuity which may be due or become due to me then due to me and pay the same to the
                            treasurer of the society in liquidation of my debt.</p>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-primary">Submit Loan Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Embed members data for autofill
        const members = {
            @foreach ($membersList as $m)
                '{{ $m->id }}': {
                    member_id: '{{ $m->id }}',
                    employee_number: '{{ $m->empCode ?? $m->id }}',
                    full_name: '{{ addslashes($m->name) }}',
                    service_left: '{{ $m->service_left_months ?? 0 }}',
                    designation: '{{ addslashes($m->designation->name ?? ($m->designationAtPresent ?? '')) }}',
                    department: '{{ addslashes($m->department->name ?? '') }}',
                    mobile: '{{ $m->mobile ?? '' }}',
                    email: '{{ $m->email ?? '' }}'
                },
            @endforeach
        };

        $(document).ready(function() {
            // Dashlite initialization for select2 might already handle `.js-select2`, 
            // but we bind our custom change event using jQuery since select2 works best with it.
            $('.surety-select').on('change', function(e) {
                const idx = $(this).data('index');
                const val = $(this).val();
                const empField = $('.surety-emp[data-index="' + idx + '"]');
                const serviceField = $('.surety-service[data-index="' + idx + '"]');

                if (!val) {
                    empField.val('');
                    serviceField.val('');
                    return;
                }
                const info = members[val];
                if (info) {
                    empField.val(info.employee_number);
                    serviceField.val(info.service_left);
                }
            });

            // If Admin view, autofill applicant details on select
            $('#applicant-select').on('change', function(e) {
                const val = $(this).val();
                if (!val) {
                    $('#applicant-name').val('');
                    $('#applicant-emp').val('');
                    $('#applicant-designation').val('');
                    $('#applicant-dept').val('');
                    $('[name="mobile"]').val('');
                    $('[name="email"]').val('');
                    return;
                }
                const info = members[val];
                if (info) {
                    $('#applicant-name').val(info.full_name);
                    $('#applicant-emp').val(info.employee_number);
                    $('#applicant-designation').val(info.designation);
                    $('#applicant-dept').val(info.department);
                    if (!$('[name="mobile"]').val()) $('[name="mobile"]').val(info.mobile);
                    if (!$('[name="email"]').val()) $('[name="email"]').val(info.email);
                }
            });

            // Trigger change on load if old value exists
            if ($('#applicant-select').val()) {
                $('#applicant-select').trigger('change');
            }
        });
    </script>
@endsection
