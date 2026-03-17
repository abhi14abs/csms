@extends('layouts.app')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center mb-4">
            <h4 class="nk-block-title">Members</h4>
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary"><em
                    class="icon ni ni-plus-circle"></em>&nbsp; Create Member</a>
        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init-export nowrap table" data-export-title="Export">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee No</th>
                            <th>Name</th>
                            {{-- <th>Region</th> --}}
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Member of Society</th>
                            <th>Nominee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $key => $m)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $m->empCode }}</td>
                                <td>{{ $m->name }}</td>
                                {{-- <td></td> --}}
                                <td>{{ $m->mobile }}</td>
                                <td>{{ $m->email }}</td>
                                <td>{{ $m->designation?->name }}</td>
                                <td>{{ $m->is_society_member }}</td>
                                <td>{{ $m->nominee->name ?? 'N/A' }}</td>
                                <td>{{ $m->status }}</td>
                                <td>
                                    <a href="{{ route('admin.members.edit', $m->id) }}"
                                        class="btn btn-sm btn-outline-success">Edit</a>
                                    <a href="{{ route('admin.members.fd.create', $m->id) }}"
                                        class="btn btn-sm btn-outline-primary">Add FD</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
