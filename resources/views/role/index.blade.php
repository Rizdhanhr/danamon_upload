@extends('layouts.main')
@section('title', 'Role')
@section('page_title', 'Role')
@section('button')
    @can('CREATE-ROLE')
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('role.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create Role
                </a>
                <a href="{{ route('role.create') }}" class="btn btn-primary d-sm-none btn-icon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                </a>
            </div>
        </div>
    @endcan
@endsection
@section('content')
    <div class="container-xl">
        <div class="card mb-4">
            {{-- <div class="card-header">
            <a type="button" href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
        </div> --}}
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="11%">Last Update</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($user as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->email }}</td>
                            <td><span class="badge text-bg-secondary">{{ $r->role->name }} </span></td>
                            <td>
                                <!-- Example single danger button -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Opsi
                                    </button>
                                    <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('user.edit',$r->id) }}">Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.show',$r->id) }}">Detail</a></li>
                                    <li><button class="dropdown-item" id="error" data-bs-toggle="modal" data-bs-target="#staticBackdrop-{{ $r->id }}">Ganti Password</button></li>
                                    <li>
                                        <form action="{{ route('user.destroy',$r->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                        <button type="submit" class="dropdown-item" onclick="deleteConfirm(event)" href="#">Hapus</button>
                                        </form>
                                    </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [2, 'desc']
            ],
            ajax: {
                url: "{{ route('role.data') }}",
                type: 'POST',
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    searchable: false,
                    orderable: false
                },
            ]
        });

        function deleteConfirm(id) {
            let url = "{{ route('role.destroy', ':id') }}";
            url = url.replace(':id', parseInt(id));
            Swal.fire({
                title: `Are you sure want to delete this data?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(result) {
                            alertSuccess(result.message);
                            table.ajax.reload();
                        },
                        error: function(error) {

                            alertFail(error.responseJSON.error_message);
                        }
                    });
                }
            });
        }
    </script>
@endpush
