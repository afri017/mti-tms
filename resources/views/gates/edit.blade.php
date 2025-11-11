@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">

    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{ $pageTitle }}</h3>
        </div>

        <form action="{{ route('gates.update', $gate->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Gate</label>
                    <input type="text" name="gate" class="form-control" value="{{ $gate->gate }}" required>
                </div>

                <div class="form-group">
                    <label>Point (Source)</label>
                    <select name="point" class="form-control" required>
                        @foreach ($sources as $src)
                            <option value="{{ $src->id }}" {{ $src->id == $gate->point ? 'selected' : '' }}>
                                {{ $src->location_name }} ({{ $src->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Time Start</label>
                    <input type="time" name="timestart" class="form-control" value="{{ $gate->timestart }}" required>
                </div>

                <div class="form-group">
                    <label>Time End</label>
                    <input type="time" name="timeend" class="form-control" value="{{ $gate->timeend }}" required>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <input type="number" name="type" class="form-control" value="{{ $gate->type }}" required>
                </div>

                <div class="form-group">
                    <label>Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="{{ $gate->duration_minutes }}" required>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('gates.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
</section>
@endsection
