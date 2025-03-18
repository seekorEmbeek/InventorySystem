@extends('adminlte::page')

@section('css')
<style>
    .select2-container {
        width: 1120.74px;
        border: 1px solid #ccc !important;
        padding: 5px;

    }
</style>

@section('title', 'Konversi Stock Barang')
@section('content_header')
<h1>Konversi Stock Barang</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('stock.conversion',$data->id) }}" method="post" enctype="multipart/form-data">
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
                                <input type="number" class="form-control" name="remainingStock" id="remainingStock" value="{{$data->remainingStock}}" readonly>
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
                                <input type="text" class="form-control" value="{{ number_format($data->sellingPricePerUnit, 0, ',', '.') }}" placeholder="Harga Beli" readonly>
                            </div>
                        </div>

                        <!-- Konversi -->
                        <hr>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Jumlah yang Ingin DiKonversi</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="conversionQty" id="conversionQty" max="{{ $data->remainingStock }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Hasil Conversi</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" id="smallQty" name="smallQty" placeholder="Jumlah" required>
                            </div>

                            <div class="col-sm-3">
                                <!-- <input type="text" class="form-control" id="smallUom" name="smallUom" placeholder="Satuan" required> -->
                                <x-uom-dropdown :selected name="smallUom" id="smallUom" />
                            </div>

                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="formattedsmallPrice" placeholder="Harga per Satuan" readonly>
                                <input type="hidden" name="smallPrice" id="smallPrice">
                            </div>

                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="formattedsellingPricePerUnit" placeholder="Harga Jual per Satuan Baru" required>
                                <input type="hidden" name="sellingPricePerUnit" id="sellingPricePerUnit">
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

        const formattedsmallPrice = document.getElementById("formattedsmallPrice");
        const smallPrice = document.getElementById("smallPrice");

        formattedInput.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedInput.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = rawValue; // Store raw number in hidden input
            } else {
                hiddenInput.value = "";
            }
        });

        formattedsmallPrice.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedsmallPrice.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                smallPrice.value = rawValue; // Store raw number in hidden input
            } else {
                smallPrice.value = "";
            }
        });
    });

    //ADD VALIDATION FOR CONVERSION THAT CONVERSION QTY MUST BE LESS THAN REMAINING STOCK
    document.addEventListener("DOMContentLoaded", function() {
        const conversionQty = document.getElementById("conversionQty");
        const smallUom = document.getElementById("smallUom");

        conversionQty.addEventListener("input", function(e) {
            if (parseInt(e.target.value) > parseInt(e.target.max)) {
                e.target.setCustomValidity("Jumlah yang ingin dikonversi tidak boleh melebihi sisa stock");
            } else {
                e.target.setCustomValidity("");
            }
        });

        //ADD VALIDATION FOR SMALL UOM THAT SMALL UOM MUST BE DIFFERENT FROM UOM
        smallUom.addEventListener("input", function(e) {
            const uom = document.getElementById("uom").value;
            if (e.target.value === uom) {
                e.target.setCustomValidity("Satuan hasil konversi tidak boleh sama dengan satuan sebelumnya");
            } else {
                e.target.setCustomValidity("");
            }
        });
    });

    //calculate smallPrice
    document.addEventListener("DOMContentLoaded", function() {
        const conversionQty = document.getElementById("conversionQty");
        const smallQty = document.getElementById("smallQty");
        const pricePerUnit = document.getElementById("pricePerUnit");
        const formattedsmallPrice = document.getElementById("formattedsmallPrice");
        const smallPrice = document.getElementById("smallPrice");

        smallQty.addEventListener("input", function(e) {
            let rawValue = e.target.value;
            let rawPricePerUnit = pricePerUnit.value.replace(/\D/g, "");
            let rawConversionQty = conversionQty.value ?? 0;

            if (rawValue !== "") {
                let result = (rawConversionQty * rawPricePerUnit) / rawValue;
                formattedsmallPrice.value = parseInt(result).toLocaleString("id-ID").replace(/,/g, ".");
                smallPrice.value = result;
            } else {
                formattedsmallPrice.value = "";
                smallPrice.value = "";
            }
        });

        conversionQty.addEventListener("input", function(e) {
            let rawValue = e.target.value;
            let rawPricePerUnit = pricePerUnit.value.replace(/\D/g, "");
            let rawSmallQty = smallQty.value ?? 0;

            if (rawValue !== "") {
                let result = (rawValue * rawPricePerUnit) / rawSmallQty;
                formattedsmallPrice.value = parseInt(result).toLocaleString("id-ID").replace(/,/g, ".");
                smallPrice.value = result;
            } else {
                formattedsmallPrice.value = "";
                smallPrice.value = "";
            }
        });
    });
</script>