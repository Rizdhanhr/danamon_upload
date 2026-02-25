@extends('layouts.main')
@section('title', 'Upload Recipient Detail')
@section('page_title', 'Upload Recipient Detail')
@section('button')

@endsection
@section('content')
    <div class="container-xl">
        <div class="card mb-4">
            {{-- <div class="card-header">
            <a type="button" href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
        </div> --}}
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="information" style="width:100%">
                        <tbody>
                            <tr>
                                <th style="width:30%">Batch Name</th>
                                <td>{{ $upload->name }}</td>
                            </tr>

                            <tr>
                                <th>Total Recipient</th>
                                <td>{{ number_format($upload->total_recipient, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>Rp {{ number_format($upload->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>{{ $upload->notes }}</td>
                            </tr>

                            <tr>
                                <th>Created At</th>
                                <td>{{ $upload->created_at }} - ({{ $upload->creator->name }})</td>
                            </tr>
                            @if ($upload->status > 1 || $upload->status == -2)
                                <tr>
                                    <th>Approved At</th>
                                    <td>{{ $upload->approved_at }} - ({{ $upload->approver->name }})</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Schedule Date</th>
                                <td>{{ $upload->scheduled_at }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($upload->status == 0)
                                        <span class="badge bg-warning">Uploading</span>
                                    @elseif($upload->status == 1)
                                        <span class="badge bg-secondary">Waiting For Approval</span>
                                    @elseif($upload->status == 2)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($upload->status >= 3)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($upload->status == -1)
                                        <span onclick="showExceptionModal(`{{ e($upload->exception) }}`)"
                                            class="badge bg-danger">Failed</span>
                                    @elseif($upload->status == -2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($upload->status == -3)
                                        <span class="badge bg-danger">Canceled</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($upload->status == 1 && Gate::allows('APPROVE-UPLOAD-RECIPIENT'))
                                @php
                                    $data = [
                                        'name' => $upload->name ?? '-',
                                        'id' => $upload->id,
                                        'total_recipient' => number_format($upload->total_recipient, 0, ',', '.'),
                                        'total_amount' => number_format($upload->total_amount, 0, ',', '.'),
                                    ];

                                    $approveData = array_merge($data, ['action' => 2]);
                                    $rejectData = array_merge($data, ['action' => -2]);

                                    $jsonApprove = htmlspecialchars(json_encode($approveData), ENT_QUOTES, 'UTF-8');
                                    $jsonReject = htmlspecialchars(json_encode($rejectData), ENT_QUOTES, 'UTF-8');
                                @endphp

                                <tr>
                                    <th>Action</th>
                                    <td>
                                        <button class="btn btn-success btn-sm rounded-pill px-4 shadow-sm fw-semibold me-2"
                                            onclick="approvalConfirm({!! $jsonApprove !!})">
                                            <i class="bi bi-check-circle me-1"></i> Approve
                                        </button>

                                        <button class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm fw-semibold"
                                            onclick="approvalConfirm({!! $jsonReject !!})">
                                            <i class="bi bi-x-circle me-1"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>Original File</th>
                                <td>
                                    @if ($upload->path)
                                        <a href="{{ route('upload-recipient.download_original', $upload->id) }}"
                                            class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-semibold"
                                            target="_blank">
                                            <i class="bi bi-download me-1"></i> Download File
                                        </a>
                                    @else
                                        <span class="text-muted">No file</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                                <th>Name</th>
                                <th width="15%">Mobile Num</th>
                                <th width="12%">Pol Num</th>
                                <th width="10%">Bank Br Code</th>
                                <th width="10%">Bank Name</th>
                                <th width="15%">Product</th>
                                <th width="17%">Amount</th>
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
        let id = "{{ $upload->id }}";
        let link = "{{ route('upload-recipient.detail_data', ':id') }}";
        link = link.replace(':id', parseInt(id));
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: link,
                type: 'POST',
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'pol_num',
                    name: 'pol_num'
                },
                {
                    data: 'bank_br_code',
                    name: 'bank_br_code'
                },
                {
                    data: 'bank_name',
                    name: 'bank_name'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
            ],
        });



        function approvalConfirm(data) {
            let htmlTable = `
                <table class="table table-bordered text-start" style="width:100%">
                    <tr><th>Batch Name</th><td>${data.name}</td></tr>
                    <tr><th>Total Recipient</th><td>${data.total_recipient}</td></tr>
                    <tr><th>Total Amount</th><td>Rp. ${data.total_amount}</td></tr> 
                </table>
            `;


            let url = "{{ route('upload-recipient.approve', ':id') }}";
            url = url.replace(':id', data.id);
            Swal.fire({
                title: data.action == 2 ?
                    'Are you sure want to approve this batch?' : 'Are you sure want to reject this batch?',
                icon: 'warning',
                html: htmlTable,
                width: "650px",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: url,
                        data: {
                            status: data.action,
                        },
                        success: function(result) {
                            alertSuccess(result.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function(error) {
                            alertFail(error.responseJSON.error_message)
                        },
                    });
                }
            });
        }

        function showExceptionModal(text) {
            $('#exceptionText').text(text || '');
            $('#exceptionModal').modal('show');
        }
    </script>
@endpush
