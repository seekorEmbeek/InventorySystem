@extends('adminlte::page')

@section('css')
<style>
    .select2-container {
        width: 1120.74px;
        border: 1px solid #ccc !important;
        padding: 5px;

    }
</style>

@section('title', 'Pembelian Baru')
@section('content_header')
<h1>Buat Pembelian Baru</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('purchasing.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Supplier</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="supplierName" id="supplierName" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Tanggal Pembelian</label>
                            <div class="col-sm-10">
                                <x-adminlte-input-date type="date" name="date" id="date" required />

                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Barang</label>
                            <div class="col-sm-10">
                                <x-adminlte-select2 name="productId" id="product" required>
                                    <!-- <option value="" disabled selected>Harap pilih barang ...</option> -->
                                    @foreach ($product as $c)

                                    <option value="{{ $c->id}}">{{ $c-> name }}</option>
                                    @endforeach
                                </x-adminlte-select2>
                                <input type="hidden" id="productName" name="productName">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Jumlah Pembelian </label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" id="purchaseQty" name="purchaseQty" placeholder="Jumlah Beli"
                                    required>
                            </div>
                            <div class="col-sm-3">
                                <!-- <input type="text" class="form-control"  id="purchaseUom" name="purchaseUom" placeholder="Satuan Beli"
                                    required> -->
                                <x-uom-dropdown : selected="" name="purchaseUom" id="purchaseUom" />
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="formattedPurchasePrice" placeholder="Harga Beli" required>
                                <input type="hidden" id="purchasePrice" name="purchasePrice">
                            </div>
                        </div>

                        <!-- Checkbox "Ada Konversi" -->
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hasConversion">
                                    <label class="form-check-label" for="hasConversion">
                                        Ada Konversi
                                    </label>
                                </div>
                            </div>
                        </div>



                        <!-- Conversion Section (Hidden by Default) -->
                        <div id="conversionSection" style="display: none;">
                            <div class="row mb-3">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Konversi</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" id="smallQty" name="smallQty" placeholder="Jumlah Conversi">
                                </div>
                                <div class="col-sm-3">
                                    <!-- <input type="text" class="form-control" id="smallUom" name="smallUom" placeholder="Satuan Conversi"> -->
                                    <x-uom-dropdown :selected="" name="smallUom" id="smallUom" />
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" id="formattedSmallPrice" placeholder="Harga Beli">
                                    <input type="hidden" id="smallPrice" name="smallPrice">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i>
                                Save</button>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('js')

<!-- Load jQuery First -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AUTO UPDATE CONVERSION FIELDS -->
<script>
    // ENABLING CONVERSION FIELDS
    document.addEventListener("DOMContentLoaded", function() {
        const hasConversion = document.getElementById("hasConversion");
        const purchaseQty = document.getElementById("purchaseQty");
        const purchaseUom = document.getElementById("purchaseUom");
        const purchasePrice = document.getElementById("purchasePrice");
        const smallQty = document.getElementById("smallQty");
        const smallUom = document.getElementById("smallUom");
        const smallPrice = document.getElementById("smallPrice");

        function updateConversionFields() {
            if (hasConversion.checked) {
                // Jika checkbox dicentang, input manual
                //menampilkan inputan
                document.getElementById("conversionSection").style.display = "block";
                document.getElementById("formattedSmallPrice").required = true;

                //remove value
                smallQty.value = "";
                smallUom.value = "";
                smallPrice.value = "";

            } else {
                // Jika tidak dicentang, ambil nilai dari Jumlah Pembelian
                smallQty.value = purchaseQty.value;
                smallUom.value = purchaseUom.value;
                smallPrice.value = purchasePrice.value;

                //menyembunyikan inputan
                document.getElementById("conversionSection").style.display = "none";
                document.getElementById("formattedSmallPrice").required = false;
            }
        }

        // Saat checkbox berubah
        hasConversion.addEventListener("change", updateConversionFields);

        // Saat jumlah pembelian berubah, jika checkbox tidak dicentang, update otomatis
        purchaseQty.addEventListener("input", updateConversionFields);
        purchaseUom.addEventListener("input", updateConversionFields);
        purchasePrice.addEventListener("input", updateConversionFields);
    });


    //KONVERSI HARGA KE NILAI INDONESIA
    document.addEventListener("DOMContentLoaded", function() {
        const formattedInput = document.getElementById("formattedPurchasePrice");
        const formattedSmallPrice = document.getElementById("formattedSmallPrice");
        const hiddenInput = document.getElementById("purchasePrice");
        const smallPrice = document.getElementById("smallPrice");

        formattedInput.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedInput.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = rawValue; // Store raw number in hidden input
                smallPrice.value = rawValue;
            } else {
                hiddenInput.value = "";
            }
        });

        formattedSmallPrice.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedSmallPrice.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                smallPrice.value = rawValue; // Store raw number in hidden input
            } else {
                smallPrice.value = "";
            }
        });
    });

    //SELECT2 get product name from selected option
    $(document).ready(function() {

        if ($("#product").length) {
            $("#product").select2(); // Initialize Select2
            $("#product").on("select2:select", function(e) {
                var selectedOption = e.params.data;
                $("#productName").val(selectedOption.text);
            });
        }
    });
</script>