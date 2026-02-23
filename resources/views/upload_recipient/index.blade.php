@extends('layouts.main')
@section('title', 'Upload Recipient')
@section('page_title', 'Upload Recipient')
@section('button')
    @can('CREATE-UPLOAD-RECIPIENT')
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
        <div class="row g-2 mb-3">
            <div class="col-md-2">
                <label class="form-label" for="startDate">Start Date</label>
                <input type="text" name="start" class="form-control text-center" id="startDate"
                    placeholder="Start Date">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="endDate">End Date</label>
                <input type="text" name="end" class="form-control text-center" id="endDate" placeholder="End Date">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="categoryFilter">Status</label>
                <select class="form-select">
                    <option value="All" selected>All</option>
                    <option value="0">Uploading</option>
                    <option value="1">Waiting For Approval</option>
                    <option value="2">Approved</option>
                    <option value="3">Completed</option>
                    <option value="-1">Failed</option>
                    <option value="-2">Rejected</option>
                    <option value="-3">Canceled</option>
                </select>
            </div>


        </div>
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
                                <th width="10%">Total Recipient</th>
                                <th width="15%">Total Amount</th>
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


    <div class="modal fade" id="exceptionModal" tabindex="-1">
        <div class="modal-dialog  modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Process Failed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="exceptionText"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@push('script')
    <script>
        $('#startDate').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker24Hour: true,
            timePickerSeconds: false,
            showDropdowns: true,
            drops: 'down',
            locale: {
                format: 'YYYY-MM-DD',
            },
        });

        $('#endDate').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker24Hour: true,
            timePickerSeconds: false,
            showDropdowns: true,
            drops: 'down',
            locale: {
                format: 'YYYY-MM-DD',
            },
        });

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
                    name: 'status',
                    className: "text-center",
                    searchable: false,
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


        function cancelConfirm(id) {
            let url = "{{ route('upload-recipient.cancel', ':id') }}";
            url = url.replace(':id', parseInt(id));
            Swal.fire({
                title: `Are you sure want to cancel this batch?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: url,
                        success: function(result) {
                            alertSuccess(result.message);
                            table.ajax.reload();
                        },
                        error: function(error) {
                            alertFail(error.responseJSON.error_message)
                            table.ajax.reload();
                        },
                    });
                }
            })
        }

        function showExceptionModal(text) {
            $('#exceptionText').text(text || '');
            $('#exceptionModal').modal('show');
        }
    </script>
@endpush
