@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <a href="{{ route('gates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Gate</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table id="gates-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gate</th>
                        <th>Point (Source)</th>
                        <th>Time Start</th>
                        <th>Time End</th>
                        <th>Type</th>
                        <th>Duration (Minutes)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gates as $gate)
                    <tr>
                        <td>{{ $gate->id }}</td>
                        <td>{{ $gate->gate }}</td>
                        <td>{{ $gate->source->location_name ?? $gate->point }}</td>
                        <td>{{ $gate->timestart }}</td>
                        <td>{{ $gate->timeend }}</td>
                        <td>{{ $gate->type }}</td>
                        <td>{{ $gate->duration_minutes }}</td>
                        <td>
                            <a href="{{ route('gates.edit', $gate->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('gates.destroy', $gate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus gate ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
{{-- Script DataTables --}}
@push('scripts')
<script>
$(document).ready(function() {
    $('#gates-table').DataTable({
        responsive: true,
        order: [[0, 'asc']],
    });
});
</script>
@endpush
@endsection
