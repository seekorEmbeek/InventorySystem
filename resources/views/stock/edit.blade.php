@extends('adminlte::page')

@section('css')
<style>
    .select2-container {
        width: 1120.74px;
        border: 1px solid #ccc !important;
        padding: 5px;

    }
</style>

@section('title', 'Edit Stock Barang')
@section('content_header')
<h1>Edit Stock Barang</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('stock.update',$data->id,false) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="productName" id="productName" value="{{$data->productName}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="uom" id="uom" value="{{$data->uom}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Sisa Stock</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="remainingStock" id="remainingStock" value="{{$data->remainingStock}}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Harga Rata2 per Satuan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pricePerUnit" value="{{ number_format($data->pricePerUnit, 0, ',', '.') }}" name="pricePerUnit" placeholder="Harga Beli" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Harga Jual per Satuan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="formattedsellingPricePerUnit" value="{{ number_format($data->sellingPricePerUnit, 0, ',', '.') }}" placeholder="Harga Beli">
                                <input type="hidden" id="sellingPricePerUnit" name="sellingPricePerUnit" value="{{$data->sellingPricePerUnit}}">
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i>
                                Save</button>
                            <a href="{{ route('stock.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('js')
<script>
    //KONVERSI HARGA KE NILAI INDONESIA
    document.addEventListener("DOMContentLoaded", function() {
        const formattedInput = document.getElementById("formattedsellingPricePerUnit");
        const hiddenInput = document.getElementById("sellingPricePerUnit");

        formattedInput.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedInput.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = rawValue; // Store raw number in hidden input
            } else {
                hiddenInput.value = "";
            }
        });
    });
</script>