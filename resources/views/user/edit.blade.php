@extends('layouts.main')
@section('title', 'Update User')
@section('page_title', 'Update User')
@section('button')

@endsection
@section('content')
    <div class="container-xl">
        <div class="card mb-4">
            {{-- <div class="card-header">
            <a type="button" href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
        </div> --}}
            <div class="card-body">
                <form method="POST" action="{{ route('user.update',$user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name',$user->name) }}">
                        <span style="color:red;">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email',$user->email) }}">
                        <span style="color:red;">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class='form-label' for="role">Role</label>
                        <select class="form-select-sm @error('role') is-invalid @enderror" id="role" name='role'>
                            @foreach ($role as $r)
                                <option {{ old('role',$user->role_id) == $r->id? 'selected' : '' }} value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <span style="color: red;">
                            @error('role')
                            {{ $message }}
                            @enderror
                        </span>
                    </div>

                    <a type="button" href="{{ route('user.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>




@endsection
@push('script')
<script>
    $(document).ready(function() {
        $('#role').select2({
            theme: "bootstrap-5",
            width: '100%'
        });
    });



</script>
@endpush
