@extends('adminlte::page')

@section('title', 'Buat Barang')
@section('content_header')
<h1>Buat Barang Baru</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('product.store',[],false) }}" method="post">
                        @csrf

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="uom" id="uom" required>
                            </div>
                        </div>


                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i>
                                Simpan</button>
                            <a href="{{ route('product.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@stop