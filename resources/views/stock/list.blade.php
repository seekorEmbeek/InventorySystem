@extends('adminlte::page')

@section('title','Daftar Stock Barang')
@section('content_header')
<h1>Daftar Stock Barang</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Sisa Stock</th>
                                <th scope="col">Harga Rata2 per Satuan</th>
                                <th scope="col">Harga Jual per Satuan</th>
                                <th scope="col" width="350px">Action</th>
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
                                    <div class="d-flex justify-content-around">
                                        <a href="{{ route('stock.edit',$c->id)}}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                            Edit</a>

                                        <a href="{{ route('stock.conversion',$c->id)}}" class="btn btn-warning">
                                            <i class="fas fa-exchange-alt"></i>
                                            Conversi</a>
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
    //fungsi dibawah untuk menghilangkan alert dengan efek fadeout   
    $("#success-alert").fadeTo(2000, 500).fadeOut(500, function() {
        $("#success-alert").fadeOut(500);
    });
</script>

@stop