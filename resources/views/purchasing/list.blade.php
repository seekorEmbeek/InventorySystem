@extends('adminlte::page')

@section('title','Daftar Pembelian')
@section('content_header')
<h1>Daftar Pembelian</h1>
@stop


@section('content')
<div class="container-fluid">
    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        <div class="form-group">
                            <label for="dateFrom">Tgl Awal Laporan</label>
                            <input type="date" id="dateFrom" name="dateFrom" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="dateTo">Tgl Akhir Laporan</label>
                            <input type="date" id="dateTo" name="dateTo" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="productName">Nama Barang</label>
                            <x-adminlte-select2 name="productName" id="productName" class="form-control" required>
                                <option value="" disabled selected>🔍 Pilih barang ...</option>
                                @foreach ($product->unique('name') as $c) {{-- Ensure unique values --}}
                                <option value="{{ strtoupper($c->name) }}">{{ strtoupper($c->name) }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="exportSubmit">Export</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <!-- @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
                               <strong>{{  session('success') }}</strong>
                                
                              </div>
                            @endif

                            @if(session('error'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>{{  session('error')}} </strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                            @endif -->

                    <div class="float-right">
                        <a href="{{ route('purchasing.create')}}" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Create</a>

                        <!-- <button class="btn btn-success" id="exportPurchasingBtn">Export Purchasing Data</button> -->
                        <a id="exportPurchasingBtn" id="" class="btn btn-success">
                            <i class="fas fa-fw fa-file"></i>
                            Cetak</a>

                    </div>
                    <table id="purchTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th data-orderable="false" scope="col">#</th>
                                <th data-orderable="false" scope="col">Nama Supplier</th>
                                <th scope="col">Tgl</th>
                                <th data-orderable="false" scope="col">Barang</th>
                                <th data-orderable="false" scope="col">Jumlah</th>
                                <th data-orderable="false" scope="col">Satuan</th>
                                <th scope="col">Harga</th>
                                <th scope="col" width="180px">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data as $c)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $c->supplierName }}</td>
                                <td>{{ \Carbon\Carbon::parse($c-> date)->format('d-M-Y') }}</td>
                                <td>{{ $c->productName }}</td>
                                <td>{{ $c->smallQty }}</td>
                                <td>{{ $c->smallUom }}</td>
                                <td>{{ number_format($c->smallPrice, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex right-content-around">
                                        <a href="{{ route('purchasing.edit',$c->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        &nbsp;
                                        <form action="{{ route('purchasing.destroy',$c->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                            @endforeach


                        </tbody>
                    </table>

                    <!-- PAGINATION -->
                    <!-- <div class="d-flex justify-content-between align-items-center mb-2">
    <p><strong>Menampilkan {{ $data->count() }} dari {{ $data->total() }} data</strong></p> -->
                    <div>
                        {{ $data->links() }}
                    </div>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')

<script>
    //tooltip
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    //fungsi dibawah untuk menghilangkan alert dengan efek fadeout   
    $("#success-alert").fadeTo(2000, 500).fadeOut(500, function() {
        $("#success-alert").fadeOut(500);
    });


    $(document).ready(function() {
        var table = $('#purchTable').DataTable({
            "columnDefs": [{
                    "orderable": true,
                    "targets": [2, 6]
                },
                {
                    "orderable": false,
                    "targets": [7]
                }
            ],
            "language": {
                "paginate": {
                    "previous": "",
                    "next": ""
                }
            },
            "drawCallback": function(settings) {
                var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                if (settings._iDisplayLength >= settings.fnRecordsDisplay()) {
                    pagination.hide();
                } else {
                    pagination.show();
                }
            },
            "lengthChange": false, // Hide "Show X entries" dropdown
            "pageLength": 10, // Default number of entries per page
            "pagingType": "simple_numbers", // Use "simple_numbers" pagination style
            "info": true, // Keep "Showing X of Y entries"
            "ordering": true, // Enable sorting
            "autoWidth": true, // Disable auto column width
            "responsive": true // Make table responsive
        });

    });

    // Select2 Initialization
    $(document).ready(function() {
        // Ensure Select2 initializes correctly inside a modal
        $('#productName').select2({
            theme: 'bootstrap4', // Use AdminLTE theme
            placeholder: "🔍 Pilih barang...",
            allowClear: true // Allow users to clear selection
        });

        // Fix issue where Select2 dropdown gets cut off inside the modal
        $('#exportModal').on('shown.bs.modal', function() {
            $('#productName').select2({
                dropdownParent: $('#exportModal'), // Fixes display inside modal
                theme: 'bootstrap4'
            });
        });
    });

    //Add JavaScript to Handle Export
    document.addEventListener("DOMContentLoaded", function() {
        let exportUrl = "";

        document.getElementById("exportPurchasingBtn").addEventListener("click", function() {
            exportUrl = "{{ route('export.purchasing') }}";
            $("#exportModal").modal("show");
        });

        document.getElementById("exportSubmit").addEventListener("click", function() {
            let dateFrom = document.getElementById("dateFrom").value;
            let dateTo = document.getElementById("dateTo").value;
            let productName = document.getElementById("productName").value;

            let queryParams = new URLSearchParams({
                dateFrom: dateFrom,
                dateTo: dateTo,
                productName: productName
            });

            window.location.href = exportUrl + "?" + queryParams.toString();
            $("#exportModal").modal("hide");
        });
    });
</script>

@stop