@extends('adminlte::page')

@section('title','Daftar Stock Barang')
@section('content_header')
<h1>Daftar Stock Barang</h1>
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
                        <!-- <div class="form-group">
                            <label for="dateFrom">Tgl Awal Laporan</label>
                            <input type="date" id="dateFrom" name="dateFrom" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="dateTo">Tgl Akhir Laporan</label>
                            <input type="date" id="dateTo" name="dateTo" class="form-control">
                        </div> -->
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

                    <div class="float-right">
                        <a id="exportStockBtn" id="" class="btn btn-success">
                            <i class="fas fa-fw fa-file"></i>
                            Cetak</a>

                    </div>

                    <table id="stockTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th data-orderable="false" scope="col">#</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Sisa Stock</th>
                                <th scope="col">Harga Rata2 per Satuan</th>
                                <th scope="col">Harga Jual per Satuan</th>
                                <th data-orderable="false" scope="col" width="180px">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data as $c)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $c->productName }}</td>
                                <td>{{ $c->uom }}</td>
                                <td>{{ $c->remainingStock }}</td>
                                <td>{{ number_format($c->pricePerUnit, 0, ',', '.') }}</td>
                                <td>{{ number_format($c->sellingPricePerUnit, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex right-content-around">
                                        <a href="{{ route('stock.edit',$c->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        &nbsp;
                                        <a href="{{ route('stock.conversion',$c->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Conversi">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
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

    //function untuk orderable pada table
    $(document).ready(function() {
        $('#stockTable').DataTable({
            "order": [
                [1, "desc"],
                [3, "asc"]
            ], // Sort by Date (Descending) and Status (Ascending)
            "columnDefs": [{
                    "orderable": true,
                    "targets": [1, 3]
                }, // Enable sorting on Date and Status

            ],
            "language": {
                "search": "Cari:",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
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
            "pageLength": 15, // Default number of entries per page
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

        document.getElementById("exportStockBtn").addEventListener("click", function() {
            exportUrl = "{{ route('export.stock') }}";
            $("#exportModal").modal("show");
        });

        document.getElementById("exportSubmit").addEventListener("click", function() {
            // let dateFrom = document.getElementById("dateFrom").value;
            // let dateTo = document.getElementById("dateTo").value;
            let productName = document.getElementById("productName").value;

            let queryParams = new URLSearchParams({
                // dateFrom: dateFrom,
                // dateTo: dateTo,
                productName: productName
            });

            window.location.href = exportUrl + "?" + queryParams.toString();
            $("#exportModal").modal("hide");
        });
    });
</script>

@stop