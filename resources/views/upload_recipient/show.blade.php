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
                                <td>{{ $upload->notes ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Template SMS</th>
                                <td>{{ $upload->template ?? '-' }}</td>
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
                                <th>Original Filename</th>
                                <td>
                                    {{ $upload->original_filename ?? '-' }}
                                </td>
                            </tr>
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
            @if ($upload->status > 2)
                <div class="card-header d-flex gap-2">
                    <a href="{{ route('upload-recipient.export', $upload->id) }}"
                        class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-semibold">
                        <i class="bi bi-download me-1"></i> Download Report
                    </a>
                    @can('CREATE-UPLOAD-RECIPIENT')
                        {{-- @if ($upload->summary_amount > 0) --}}
                        <form action="{{ route('upload-recipient.notify_finance', $upload->id) }}" method="POST"
                            class="d-inline" onsubmit="return confirmSendEmail(event)">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm fw-semibold">
                                <i class="bi bi-envelope me-1"></i> Mail Finance
                            </button>
                        </form>
                        {{-- @endif --}}
                    @endcan

                </div>
            @endif
            <div class="card-body">
                <div class="row mb-4">

                    <div class="col-md-4">
                        <div class="card border-warning shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Amount Pending</small>
                                    <h4 class="mb-0 fw-bold">Rp. {{ number_format($summary->pending_amount, 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $summary->pending_count }} recipient
                                    </small>
                                </div>
                                <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-success shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Amount Sent</small>
                                    <h4 class="mb-0 fw-bold">Rp. {{ number_format($summary->sent_amount, 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $summary->sent_count }} recipient
                                    </small>
                                </div>
                                <i class="bi bi-check-circle fs-2 text-success"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-danger shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Amount Failed</small>
                                    <h4 class="mb-0 fw-bold">Rp. {{ number_format($summary->failed_amount, 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $summary->failed_count }} recipient
                                    </small>
                                </div>
                                <i class="bi bi-x-circle fs-2 text-danger"></i>
                            </div>
                        </div>
                    </div>



                </div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th> <!-- 🔥 wajib -->
                                <th width="18%">Name</th>
                                <th width="15%">Mobile Num</th>
                                <th width="20%">Product</th>
                                <th>Pol Num</th>
                                {{-- <th width="15%">Bank Name</th> --}}
                                <th width="15%">Bank Account</th>
                                <th width="17%">Amount</th>
                                <th width="17%">Bank BR Code</th>
                                <th width="17%">Valid Phone</th>
                                <th width="13%">Status</th>
                                {{-- <th width="12%">Pol Num</th> --}}
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


    <div class="modal fade" id="failedModal" tabindex="-1">
        <div class="modal-dialog  modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Recipient Failed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="failedText"></div>
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
        let isNotify = {{ $upload->flag_info_marketing == 1 ? 'true' : 'false' }};
        console.log(isNotify);

        link = link.replace(':id', parseInt(id));
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,

            scrollCollapse: true,
            order: [
                [0, 'asc']
            ],
            ajax: {
                url: link,
                type: 'POST',
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                }, {
                    data: 'name',
                    name: 'name',
                    width: '300px'
                },
                {
                    data: 'phone',
                    name: 'phone',

                },
                {
                    data: 'product_name',
                    name: 'product_name',
                    width: '300px'
                },
                {
                    data: 'pol_num',
                    name: 'pol_num',

                },
                {
                    data: 'bank_account',
                    name: 'bank_account'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'bank_br_code',
                    name: 'bank_br_code'
                },

                {
                    data: 'valid_phone',
                    name: 'valid_phone',
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                },
                {
                    data: 'status',
                    name: 'status',
                    className: "text-center",
                    searchable: false,
                },
            ],
            columnDefs: [{
                    targets: 1,
                    width: '180px',
                    createdCell: (td) => $(td).css('min-width', '180px')
                }, // Phone
                {
                    targets: 2,
                    width: '200px',
                    createdCell: (td) => $(td).css('min-width', '200px')
                }, // Product
                {
                    targets: 3,
                    width: '120px',
                    createdCell: (td) => $(td).css('min-width', '150px')
                }, // Pol Num
                {
                    targets: 4,
                    width: '150px',
                    createdCell: (td) => $(td).css('min-width', '150px')
                }, // Bank Account
                {
                    targets: 5,
                    width: '120px',
                    createdCell: (td) => $(td).css('min-width', '150px')
                }, // Amount
                {
                    targets: 6,
                    width: '100px',
                    createdCell: (td) => $(td).css('min-width', '150px')
                },
                {
                    targets: 7,
                    width: '120px',
                    createdCell: (td) => $(td).css('min-width', '120px')
                },
                {
                    targets: 8,
                    width: '120px',
                    createdCell: (td) => $(td).css('min-width', '120px')
                },
                {
                    targets: 9,
                    width: '120px',
                    createdCell: (td) => $(td).css('min-width', '120px')
                },

            ]
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

        function showFailedModal(text) {
            $('#failedText').text(text || '');
            $('#failedModal').modal('show');
        }

        function confirmSendEmail(e) {


            if (!isNotify) {
                // ✅ langsung submit tanpa alert
                return true;
            }


            e.preventDefault();

            Swal.fire({
                title: 'Email already sent',
                text: 'This report has already been sent to finance. Do you want to send it again?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            e.target.submit();
                        }
                    });
                }
            });

            return false;
        }
    </script>
@endpush
