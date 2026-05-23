@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Batch Results</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Success</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $r)
                    <tr>
                        <td class="text-{{ $r['success'] ? 'secondary' : 'danger' }}">{{ $r['member'] }}</td>
                        <td class="text-{{ $r['success'] ? 'secondary' : 'danger' }}">{{ $r['success'] ? 'Yes' : 'No' }}</td>
                        <td class="text-{{ $r['success'] ? 'secondary' : 'danger' }}">{{ $r['message'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
