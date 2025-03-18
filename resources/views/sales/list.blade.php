@extends('adminlte::page')

@section('title','Daftar Penjualan')
@section('content_header')
<h1>Daftar Penjualan</h1>
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
                    <div class="float-right">
                        <a href="{{ route('sales.create')}}" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Create</a>

                        <a id="exportSalesBtn" id="" class="btn btn-success">
                            <i class="fas fa-fw fa-file"></i>
                            Cetak</a>

                    </div>
                    <table id="salesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th data-orderable="false" scope="col">#</th>
                                <th data-orderable="false" scope="col">Nama Pembeli</th>
                                <th data-orderable="true" scope="col">Tgl</th>
                                <th data-orderable="true" scope="col">Total Harga</th>
                                <th data-orderable="true" scope="col">Total Bayar</th>
                                <th data-orderable="true" scope="col">Kekurangan</th>
                                <th data-orderable="true" scope="col">Status</th>
                                <th scope="col" width="180px">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data as $c)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $c->buyerName }}</td>
                                <td>{{ \Carbon\Carbon::parse($c-> date)->format('d-M-Y') }}</td>
                                <td>{{ number_format($c->totalPrice, 0, ',', '.') }}</td>
                                <td>{{ number_format($c->totalPayment, 0, ',', '.') }}</td>
                                <td>{{ number_format($c->remainingPayment, 0, ',', '.') }}</td>
                                <td>{{ $c->status }}</td>
                                <td>
                                    <div class="d-flex justify-content-around">
                                        <a href="{{ route('sales.edit',$c->id)}}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                            Edit</a>
                                        <form action="{{ route('sales.destroy',$c->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                                Delete</button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                            @endforeach


                        </tbody>
                    </table>

                    <!-- PAGINATION -->
                    <!-- <div class="d-flex justify-content-between align-items-center mb-2"> -->
                    <!-- <p><strong>Menampilkan {{ $data->count() }} dari {{ $data->total() }} data</strong></p> -->
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
    //

    //fungsi dibawah untuk menghilangkan alert dengan efek fadeout   
    $("#success-alert").fadeTo(2000, 500).fadeOut(500, function() {
        $("#success-alert").fadeOut(500);
    });

    //function untuk orderable pada table
    $(document).ready(function() {
        $('#salesTable').DataTable({
            "order": [
                [2, "desc"],
                [6, "asc"]
            ], // Sort by Date (Descending) and Status (Ascending)
            "columnDefs": [{
                    "orderable": true,
                    "targets": [2, 6]
                }, // Enable sorting on Date and Status
                {
                    "orderable": false,
                    "targets": [7]
                } // Disable sorting on Action column
            ],
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

        document.getElementById("exportSalesBtn").addEventListener("click", function() {
            exportUrl = "{{ route('export.sales') }}";
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