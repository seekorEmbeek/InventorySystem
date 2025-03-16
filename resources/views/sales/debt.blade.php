@extends('adminlte::page')

@section('title','Daftar Catatan Hutang')
@section('content_header')
<h1>Daftar Catatan Hutang</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <!-- <div class="float-right">
                        <a href="{{ route('sales.create')}}" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Create</a>

                    </div> -->
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
                                            <!-- @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                                Delete</button> -->
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
</script>

@stop