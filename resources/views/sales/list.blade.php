@extends('adminlte::page')

@section('title','Daftar Penjualan')
@section('content_header')
<h1>Daftar Penjualan</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <div class="float-right">
                        <a href="{{ route('sales.create')}}" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Create</a>

                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Pembeli</th>
                                <th scope="col">Tgl</th>
                                <th scope="col">Total Harga</th>
                                <th scope="col">Total Bayar</th>
                                <th scope="col">Kekurangan</th>
                                <th scope="col">Status</th>
                                <th scope="col" width="350px">Action</th>
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p><strong>Menampilkan {{ $data->count() }} dari {{ $data->total() }} data</strong></p>
                        <div>
                            {{ $data->links() }}
                        </div>
                    </div>
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