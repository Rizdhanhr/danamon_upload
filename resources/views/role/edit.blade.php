@extends('layouts.main')
@section('title', 'Edit Role')
@section('page_title', 'Edit Role')
@section('button')

@endsection
@section('content')
    <div class="container-xl">
        <div class="card mb-4">
            {{-- <div class="card-header">
            <a type="button" href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
        </div> --}}
            <div class="card-body">
                <form method="POST" action="{{ route('role.update', $role->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name', $role->name) }}">
                        <span style="color:red;">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Description</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                            name="description" value="{{ old('description', $role->description) }}">
                        <span style="color:red;">
                            @error('description')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Access</label>
                        <div class="table-responsive ">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        {{-- <th scope="col">#</th> --}}
                                        <th scope="col">Menu</th>
                                        @foreach ($module as $m)
                                            <th class="text-center" width="15%" scope="col">
                                                {{ $m->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($menu as $m)
                                        <tr>
                                            <th>{{ $m->name }}</th>
                                            @foreach ($module as $md)
                                                <td class="text-center">
                                                    @if (isset($array[$m->id][$md->id]))
                                                        @php $val = $array[$m->id][$md->id]; @endphp
                                                        <div class="form-check form-switch  d-flex justify-content-center">
                                                            <input class="form-check-input" name="permission[]"
                                                                type="checkbox" value="{{ $val }}"
                                                                @if (in_array($val, old('permission', $permission_selected))) checked @endif>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a type="button" href="{{ route('role.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script></script>
@endpush
