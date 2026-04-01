@extends('layouts.main')
@section('title', 'Profile')
@section('page_title', 'Profile')
@section('button')

@endsection
@section('content')

    <div class="container-xl">
        <div class="card">
            <div class="row g-0">
                <div class="col d-flex flex-column">
                    <div class="card-body">
                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" id="staticEmail"
                                    value="{{ Auth::user()->name }}" readonly>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Email / Username</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" id="staticEmail"
                                    value="{{ Auth::user()->email }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" id="staticEmail"
                                    value="{{ strtoupper(Auth::user()->role->name) }}" readonly>
                            </div>
                        </div>
                        {{-- <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" id="staticEmail"
                                    value="{{ strtoupper(Auth::user()->username) }}" readonly>
                            </div>
                        </div> --}}
                        @if (Auth::user()->provider == 'local')
                            <div>

                                <button data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="btn">
                                    Set new password
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Change Password</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-data" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Password <span
                                    style="color:red;">*</span></label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password">
                            <span style="color: red;"> @error('password')
                                    {{ $message }}
                                @enderror
                            </span>
                            <div id="emailHelp" class="form-text">6-12 Characters.</div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Confirm Password <span
                                    style="color:red;">*</span></label>
                            <input id="password_confirmation" type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation">
                            <span style="color: red;"> @error('password_confirmation')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="form-data" id="btnSubmit">Submit</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@push('script')
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#staticBackdrop').modal('show');
            @endif
        });
    </script>
@endpush
