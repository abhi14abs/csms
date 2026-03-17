@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Batch Results</h3>
    <table class="table">
        <thead><tr><th>Member</th><th>Success</th><th>Message</th></tr></thead>
        <tbody>
            @foreach($results as $r)
            <tr>
                <td>{{ $r['member'] }}</td>
                <td>{{ $r['success'] ? 'Yes' : 'No' }}</td>
                <td>{{ $r['message'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
