@extends('layouts.main')
@section('title', 'User')
@section('page_title', 'User')
@section('button')
    @can('CREATE-USER')
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('user.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create User
                </a>
                <a href="{{ route('user.create') }}" class="btn btn-primary d-sm-none btn-icon">
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
                                <th>Email</th>
                                <th width="20%">Role</th>
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



    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Change Password</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formData">
                        <input type="hidden" id="id" name="id">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" name="email" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Password</label>
                            <input type="password" id="password" class="form-control" name="password">
                            <span style="color:red;" class="error"></span>
                            <div id="emailHelp" class="form-text">Between 6-12 Characters.</div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation" class="form-control"
                                name="password_confirmation">
                            <span style="color:red;" class="error"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="btnSave" onClick="formSubmit()" class="btn btn-primary">Save</button>
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
                [3, 'desc']
            ],
            ajax: {
                url: "{{ route('user.data') }}",
                type: 'POST',
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'role',
                    name: 'role'
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
            ],
        });

        function changePassword(email, id) {
            $('#email').val(email);
            $('#id').val(id);
            $('#staticBackdrop').modal('show');
        }

        function formSubmit() {
            $('#btnSave').prop('disabled', true);

            var formData = $('#formData').serialize();
            $('.form-control').removeClass('is-invalid');
            $('span.error').empty();

            $.ajax({
                type: 'POST',
                url: "{{ route('user.password') }}",
                data: formData,
                success: function(response) {
                    $('#email').val('');
                    $('#id').val('');
                    $('#password').val('');
                    $('#password_confirmation').val('');
                    $('#btnSave').prop('disabled', false);
                    $('#staticBackdrop').modal('hide');
                    alertSuccess(response.success);
                },
                error: function(error) {
                    $('#btnSave').prop('disabled', false);
                    if (error.status == 422) {
                        let error_validation = error.responseJSON.errors;
                        $.each(error_validation, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).next('span').html(value[0]); // Display only the first error
                        });
                    }

                }
            });
        }


        function deleteConfirm(id) {
            let url = "{{ route('user.destroy', ':id') }}";
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
                            alertSuccess(result.success);
                            table.ajax.reload();
                        },
                        error: function(error) {
                            alertFail('Error, please contact administrator.');
                        }
                    });
                }
            });
        }
    </script>
@endpush
