@extends('adminlte::page')

@section('title', 'Input Penjualan')

@section('content_header')
<h1>Input Penjualan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <!-- Sales Information -->
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="buyerName" label="Nama Pembeli" placeholder="Masukan Nama Pembeli" required />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="date" type="date" label="Tanggal" required />
                </div>

            </div>

            <!-- Sales Items Table -->
            <div class="mt-3">
                <h4>Item Penjualan</h4>
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Satuan</th>
                            <th>Sisa Stock</th>
                            <th>Qty Pembelian</th>
                            <th>Harga Per Unit</th>
                            <th>Total Harga</th>
                            <th> <x-adminlte-button label="+" theme="success" id="addItem" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Rows Here -->
                    </tbody>
                </table>

                <!-- TOTAL PAYMENT ROW -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="totalPrice">Total Harga</label>
                        <input type="text" class="form-control totalPriceView" readonly>
                        <input type="hidden" id="totalPrice" name="totalPrice">
                    </div>

                    <div class="col-md-4">
                        <label for="totalPayment">Total Pembayaran</label>
                        <input type="text" class="form-control" id="formattedTotalPayment" placeholder="Total Pembayaran" required>
                        <input type="hidden" id="totalPayment" name="totalPayment">
                    </div>

                    <div class="col-md-4">
                        <x-adminlte-select name="status" id="status" label="Status" readonly>
                            <option value="" selected disabled></option>
                            <option value="LUNAS">LUNAS</option>
                            <option value="BELUM LUNAS">BELUM LUNAS</option>
                        </x-adminlte-select>
                    </div>
                </div>

                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        Save</button>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>

        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addItemButton = document.getElementById("addItem");
        const itemsTable = document.querySelector("#itemsTable tbody");

        // Add new item row when button is clicked
        addItemButton.addEventListener("click", function() {
            let row = document.createElement("tr");
            let index = document.querySelectorAll(".product-select").length;
            row.innerHTML = `
                <td>
             
                    <select  name="items[${index}][stockId]" class="form-control product-select" required>
                        <option value="" selected disabled>Pilih Produk</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->id }}" 
                            data-uom="{{ $stock->uom }}" 
                            data-price="{{ $stock->sellingPricePerUnit }}" 
                            data-remaining="{{ $stock->remainingStock }}" 
                            data-pricePerUnit="{{$stock->pricePerUnit}}" 
                            data-productName="{{$stock->productName}}"
                            data-productId="{{$stock->productId}}" >
                                {{ $stock->productName }} - {{ $stock->remainingStock }} - {{ $stock->uom }}
                            </option>
                        @endforeach
                    </select>
                     <input type="hidden" name="items[${index}][productName]" class="productName">
                     <input type="hidden" name="items[${index}][productId]" class="productId">
                    <input type="hidden" name="items[${index}][stockId]" class="stockId">
                   
                </td>
                <td><input type="text" name="items[${index}][uom]" class="form-control uom" readonly required></td>
                <td><input type="number" name="items[${index}][remaining]" class="form-control remaining" readonly required></td>
                <td><input type="number" name="items[${index}][qty]" class="form-control qty" required min="1"></td>
                <td>
                <input type="number" class="form-control priceView" readonly required>
                <input type="hidden" name="items[${index}][sellingPricePerUnit]" class="price">
                <input type="hidden" name="items[${index}][pricePerUnit]" class="pricePerUnit" readonly>
                </td>
                <td>
                    <input type="number" name="items[${index}][totalPrice]" class="form-control total" readonly>
                </td>
        
                <td><button type="button" class="btn btn-danger removeItem"><i class="fas fa-trash"></i></button></td>
            `;

            itemsTable.appendChild(row);
            updateEventListeners();
            updateProductDropdowns();
        });

        function updateEventListeners() {
            document.querySelectorAll(".removeItem").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                    updateProductDropdowns();
                });
            });

            // Update product details when product is selected
            document.querySelectorAll(".product-select").forEach(select => {
                select.addEventListener("change", function() {
                    let selectedOption = this.options[this.selectedIndex];
                    let row = this.closest("tr");
                    row.querySelector(".stockId").value = selectedOption.value;
                    row.querySelector(".uom").value = selectedOption.getAttribute("data-uom");
                    row.querySelector(".pricePerUnit").value = selectedOption.getAttribute("data-pricePerUnit");
                    row.querySelector(".productName").value = selectedOption.getAttribute("data-productName");
                    row.querySelector(".productId").value = selectedOption.getAttribute("data-productId");

                    // Format price with thousand separator

                    let priceValue = selectedOption.getAttribute("data-price");
                    let formattedPrice = new Intl.NumberFormat('id-ID').format(priceValue);
                    row.querySelector(".price").value = priceValue;
                    row.querySelector(".priceView").value = formattedPrice;

                    row.querySelector(".remaining").value = selectedOption.getAttribute("data-remaining");

                    updateProductDropdowns();
                });
            });

            // Update total price when quantity or price changes
            document.querySelectorAll(".qty").forEach(input => {
                input.addEventListener("input", function() {
                    let row = this.closest("tr");
                    let qty = parseFloat(row.querySelector(".qty").value) || 0;
                    let price = parseFloat(row.querySelector(".price").value.replace(/\./g, '')) || 0; // Remove thousand separator before calculating

                    let total = Math.round(qty * price); // Calculate total per item
                    row.querySelector(".total").value = total;

                    // Display formatted total with thousands separator (optional)
                    // row.querySelector(".totalFormatted").textContent = total.toLocaleString('id-ID');

                    //add customs validation that qty must be less than remaining stock
                    let remaining = parseFloat(row.querySelector(".remaining").value) || 0;
                    if (qty > remaining) {

                        //use toastr to show error message
                        toastr.error('Qty pembelian melebihi sisa stock', 'Error');

                        //reset qty to remaining stock
                        row.querySelector(".qty").value = remaining;
                    }

                    updateTotalPrice(); // Update total payment when any item changes
                });

            });

            // Update total price when quantity or price changes
            function updateTotalPrice() {
                let totalPrice = 0;
                document.querySelectorAll(".total").forEach(input => {
                    let value = parseFloat(input.value.replace(/\./g, '')) || 0;
                    totalPrice += value;
                });

                // Set total payment value
                document.querySelector("#totalPrice").value = totalPrice;
                document.querySelector(".totalPriceView").value = totalPrice.toLocaleString('id-ID');
            }
        }

        // Update product dropdowns to disable selected products
        function updateProductDropdowns() {
            let selectedProducts = new Set();

            document.querySelectorAll(".product-select").forEach(select => {
                if (select.value) {
                    selectedProducts.add(select.value);
                }
            });

            document.querySelectorAll(".product-select option").forEach(option => {
                if (option.value) {
                    option.disabled = selectedProducts.has(option.value);
                }
            });
        }

        //if status is LUNAS, then total payment is equal to total price
        statusSelect.addEventListener("change", function(e) {
            if (e.target.value === "LUNAS") {
                formattedInput.value = parseInt(document.getElementById("totalPrice").value).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = document.getElementById("totalPrice").value;
            }
        });
    });

    //KONVERSI HARGA KE NILAI INDONESIA
    document.addEventListener("DOMContentLoaded", function() {
        const formattedInput = document.getElementById("formattedTotalPayment");
        const hiddenInput = document.getElementById("totalPayment");
        const statusSelect = document.getElementById("status");

        formattedInput.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, ""); // Remove non-numeric characters
            if (rawValue !== "") {
                formattedInput.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = rawValue; // Store raw number in hidden input

                //if payment is more than total price, set status to LUNAS
                let totalPrice = parseInt(document.getElementById("totalPrice").value);
                if (parseInt(rawValue) >= totalPrice) {
                    statusSelect.value = "LUNAS";
                } else {
                    statusSelect.value = "BELUM LUNAS";
                }
            } else {
                hiddenInput.value = "";
            }
        });

    });
</script>
@endsection