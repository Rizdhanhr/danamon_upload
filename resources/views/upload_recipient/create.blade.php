@extends('layouts.main')
@section('title', 'Upload New Recipient')
@section('page_title', 'Upload New Recipient')
@section('button')

@endsection
@section('content')
    <div class="container-xl">
        <div class="card mb-4">
            {{-- <div class="card-header">
            <a type="button" href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
        </div> --}}
            <div class="card-body">
                <form method="POST" action="{{ route('upload-recipient.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-12 ">
                            <label for="exampleInputEmail1" class="form-label">Batch Name <span
                                    style="color:red;">*</span></label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}">
                            <span style="color:red;">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-8">
                            <label for="exampleInputEmail1" class="form-label">File <span
                                    style="color:red;">*</span></label>
                            <input type="file" id="file" class="form-control @error('file') is-invalid @enderror"
                                name="file" value="{{ old('file') }}">
                            <span style="color:red;">
                                @error('file')
                                    {{ $message }}
                                @enderror
                            </span>
                            <span class="form-text">
                                Max 10 MB, only .xlsx file allowed.
                            </span>
                        </div>
                        <div class="col-md-4">
                            <label for="exampleInputEmail1" class="form-label">Schedule Date <span
                                    style="color:red;">*</span></label>
                            <input type="text" class="form-control @error('schedule_date') is-invalid @enderror"
                                name="schedule_date" id="schedule_date" value="{{ old('schedule_date') }}">
                            <span style="color:red;">
                                @error('schedule_date')
                                    {{ $message }}
                                @enderror
                            </span>
                            <span class="form-text">
                                Batch will processed automatically on the schedule date.
                            </span>
                        </div>
                        <div class="col-md-12">
                            <label for="exampleInputEmail1" class="form-label">Notes </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes"
                                id="exampleFormControlTextarea1" rows="3">{{ old('notes') }}</textarea>
                            <span style="color:red;">
                                @error('notes')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>
                    <a type="button" href="{{ route('upload-recipient.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#schedule_date').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePickerIncrement: 1,
            timePicker24Hour: true,
            timePickerSeconds: false,
            showDropdowns: true,
            drops: 'down',
            locale: {
                format: 'YYYY-MM-DD HH:mm',
            },
            minDate: moment(),
        });
    </script>
@endpush
