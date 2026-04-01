@extends('layouts.main')
@push('css')
    <style>
        #search-card .card-body {
            overflow-x: auto;
            /* scroll horizontal */
        }

        #example th,
        #example td {
            min-width: 120px;
            /* sesuaikan kolom */
        }
    </style>
    @section('title', 'Report Recipient')
@section('page_title', 'Report Recipient')
@section('button')

@endsection
@section('content')
    <div class="container-xl">
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <label for="bank_account" class="form-label">Bank Account</label>
                <input type="text" id="bank_account" class="form-control" placeholder="Bank Account">
            </div>

            <div class="col-md-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" class="form-control" placeholder="Phone Number">
            </div>

            <div class="col-md-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" id="product_name" class="form-control" placeholder="Product Name">
            </div>

            <div class="col-md-3">
                <label for="pol_number" class="form-label">Pol Number</label>
                <input type="text" id="pol_number" class="form-control" placeholder="Pol Number">
            </div>
        </div>
        <div class="mb-3">
            <button class="btn btn-primary" onclick="filterData()">Search</button>
            <button class="btn btn-secondary" onclick="resetData()">Reset</button>
        </div>
        <div class="card mb-4" id="search-card" style="display: none;">
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Note:</strong> Only batches with status at least <b>Approved</b> are displayed.
                    Batches still in progress will not appear.
                </div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Scheduled Date</th>
                                <th>Batch Name</th>
                                <th>Name</th>
                                <th>Mobile Num</th>
                                <th>Product</th>
                                <th>Pol Num</th>
                                <th>Bank Account</th>
                                <th>Amount</th>
                                <th>Bank BR Code</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>







@endsection
@push('script')
    <script>
        var table;
        var tableInitialized = false;




        function filterData() {
            var bankAccount = $('#bank_account').val();
            var phone = $('#phone').val();
            var productName = $('#product_name').val();
            var polNumber = $('#pol_number').val();
            console.log('Filter criteria:', {
                bank_account: bankAccount,
                phone: phone,
                product_name: productName,
                pol_num: polNumber
            });

            if (!bankAccount && !phone && !productName && !polNumber) {
                alertFail('Please fill at least one filter criteria');
                return;
            }


            $('#search-card').show();
            if (!tableInitialized) {
                table = $('#example').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    autoWidth: false,
                    order: [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('report-recipient.data') }}",
                        type: 'POST',
                        data: function(d) {
                            d.bank_account = $('#bank_account').val();
                            d.phone = $('#phone').val();
                            d.product_name = $('#product_name').val();
                            d.pol_number = $('#pol_number').val();
                        },
                    },
                    columns: [{
                            data: 'batch.scheduled_at',
                            name: 'batch.scheduled_at'
                        },
                        {
                            data: 'batch.name',
                            name: 'batch.name'
                        },
                        {
                            data: 'name',
                            name: 'name',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                            width: '150px',
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'pol_num',
                            name: 'pol_num'
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
                            data: 'status',
                            name: 'status',
                            className: "text-center",
                            searchable: false,
                        },

                    ],
                    columnDefs: [{
                            targets: 0,
                            width: '180px',
                            createdCell: (td) => $(td).css('min-width', '180px')
                        }, // Name
                        {
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
                            createdCell: (td) => $(td).css('min-width', '120px')
                        }, // Pol Num
                        {
                            targets: 4,
                            width: '150px',
                            createdCell: (td) => $(td).css('min-width', '150px')
                        }, // Bank Account
                        {
                            targets: 5,
                            width: '120px',
                            createdCell: (td) => $(td).css('min-width', '120px')
                        }, // Amount
                        {
                            targets: 6,
                            width: '100px',
                            createdCell: (td) => $(td).css('min-width', '100px')
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
                tableInitialized = true;
            } else {
                table.ajax.reload();
            }


            $('#search-card').show();
        }
    </script>
@endpush
