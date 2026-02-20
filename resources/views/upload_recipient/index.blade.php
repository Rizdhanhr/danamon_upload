@extends('layouts.main')
@section('title', 'Upload Recipient')
@section('page_title', 'Upload Recipient')
@section('button')
    @can('CREATE-UUPLOAD-RECIPIENT')
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('upload-recipient.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Upload New Recipient
                </a>
                <a href="{{ route('upload-recipient.create') }}" class="btn btn-primary d-sm-none btn-icon">
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
                                <th>Batch Name</th>
                                <th>Total Recipient</th>
                                <th>Total Amount</th>
                                <th width="10%">Created At</th>
                                <th width="10%">Schedule Date</th>
                                <th width="11%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>

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
                [3, 'desc']
            ],
            ajax: {
                url: "{{ route('upload-recipient.data') }}",
                type: 'POST',
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'total_recipient',
                    name: 'total_recipient'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'scheduled_at',
                    name: 'scheduled_at'
                },
                {
                    data: 'status',
                    name: 'status'
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
    </script>
@endpush
