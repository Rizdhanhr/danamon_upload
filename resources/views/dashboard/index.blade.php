@extends('layouts.main')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('content')

    <div class="container-xl">
        <div class="d-flex">
            <h3 class="card-title">Summary ({{ date('M Y') }})</h3>
            <div class="ms-auto">

            </div>
        </div>
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span
                                    class="bg-success text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                    <i class="bi bi-stack"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <strong>Total Completed Batch</strong>
                                </div>
                                <div class="text-muted">
                                    {{ $total_batch }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span
                                    class="bg-warning text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                    <i class="bi bi-cash-stack"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <strong>Total Completed Amount</strong>
                                </div>
                                <div class="text-muted">
                                    Rp {{ number_format($total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span
                                    class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                    <i class="bi bi-people"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <strong>Total Completed Recipient</strong>
                                </div>
                                <div class="text-muted">
                                    {{ $total_recipient }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <h3 class="card-title">Total Batch</h3>
                            <div class="ms-auto">
                                <div class="dropdown">
                                    <a id="dropdownToggle" class="dropdown-toggle text-muted" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Nominal IDR</a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item active" onclick="setActive(this,'idr')">Total Nominal
                                            IDR</a>
                                        <a class="dropdown-item" onclick="setActive(this,'qty')">Total Recipient</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div id="chart-social-referrals"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('script')
    <script>
        var tipe = 'idr';
        var chart = null;

        // tetap nama sama
        function setActive(element, type) {
            tipe = type;
            $('.dropdown-item.active').removeClass('active');
            $(element).addClass('active');
            $('#dropdownToggle').text($(element).text());
            getData(tipe);
        }


        $(document).ready(function() {
            chart = new ApexCharts($('#chart-social-referrals')[0], {
                chart: {
                    type: "line",
                    fontFamily: 'inherit',
                    height: 288,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1
                },
                stroke: {
                    width: 2,
                    lineCap: "round",
                    curve: "smooth",
                },
                series: [],
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                },
                colors: [tabler.getColor("facebook")],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return value.toLocaleString('en-US');
                        },
                    },
                },
            });

            chart.render();
            getData(tipe);
        });

        // tetap nama sama
        function getData(type) {
            $.ajax({
                method: "GET",
                url: "{{ route('dashboard.data') }}",
                data: {
                    tipe: type
                },
                success: function(result) {
                    chart.updateSeries([{
                        name: 'Total',
                        data: result.data
                    }]);
                }
            });
        }
    </script>
@endpush
