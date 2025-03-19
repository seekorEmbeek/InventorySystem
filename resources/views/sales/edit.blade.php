@extends('adminlte::page')

@section('title', 'Edit Penjualan')

@section('content_header')
<h1>Edit Penjualan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.update', $sales->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Sales Information -->
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="buyerName" label="Nama Pembeli" value="{{$sales->buyerName}}" placeholder="Masukan Nama Pembeli" required />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="date" type="date" value="{{$sales->date}}" label="Tanggal" required />
                </div>

            </div>

            <!-- Sales Items Table -->
            <div class="mt-3">
                <h4>Item Penjualan</h4>
                <table class="table table-bordered" id="salesItemsTable">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Satuan</th>
                            <th>Sisa Stock</th>
                            <th>Qty Pembelian</th>
                            <th>Harga Per Unit</th>
                            <th>Harga Jual Per Unit</th>
                            <th>Total Harga</th>
                            <th> <x-adminlte-button label="+" theme="success" id="addItem" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales->items as $item)
                        <tr>
                            <td>
                                <select name="items[{{ $loop->index }}][stockId]" class="form-control product-select">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}"
                                        data-uom="{{ $stock->uom }}"
                                        data-price="{{ $stock->sellingPricePerUnit }}"
                                        data-remaining="{{ $stock->remainingStock }}"
                                        data-pricePerUnit="{{$stock->pricePerUnit}}"
                                        data-productName="{{$stock->productName}}"
                                        data-productId="{{$stock->productId}}"
                                        {{ $item->stock_id == $stock->id ? 'selected' : '' }}>
                                        {{ $stock->productName }} - {{ $stock->remainingStock }} {{ $stock->uom }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][productName]" value="{{ $item->productName }}">
                                <input type="hidden" name="items[{{ $loop->index }}][productId]" value="{{ $item->productId }}" class="productId">
                                <input type="hidden" name="items[{{ $loop->index }}][stock_id]" value="{{ $item->stock_id }}" class="stock_Id">
                                <input type="hidden" name="items[{{ $loop->index }}][stockId]" value="{{ $item->stock_id }}" class="stockId">
                            </td>
                            <td><input type="text" name="items[{{ $loop->index }}][uom]" class="form-control uom" value="{{ $item->uom }}" readonly></td>
                            <td><input type="number" name="items[{{ $loop->index }}][remaining]" class="form-control remaining" value="{{ $stocks->where('id', $item->stock_id)->first()?->remainingStock ?? 0 }}" readonly></td>
                            <td><input type="number" name="items[{{ $loop->index }}][qty]" class="form-control qty" value="{{ $item->qty }}" required></td>
                            <td><input type="number" name="items[{{ $loop->index }}][pricePerUnit]" class="form-control pricePerUnit" value="{{ $item->pricePerUnit }}" readonly></td>
                            <td>
                                <!-- <input type="number" name="items[{{ $loop->index }}][priceView]" class="form-control priceView" value="{{  number_format($item->sellingPricePerUnit, 0, ',', '.')}}" readonly> -->
                                <input type="number" name="items[{{ $loop->index }}][sellingPricePerUnit]" value="{{ $item->sellingPricePerUnit}}" class="form-control price">
                                <!-- <input type="number" name="items[{{ $loop->index }}][pricePerUnit]" value="{{ $item->pricePerUnit }}" class="pricePerUnit" readonly> -->
                            </td>
                            <td><input type="number" name="items[{{ $loop->index }}][totalPrice]" class="form-control total" value="{{ $item->totalSellingPrice  }}" readonly></td>
                            <td><button type="button" class="btn btn-danger removeItem"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- TOTAL PAYMENT ROW -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="totalPrice">Total Harga</label>
                        <input type="text" class="form-control totalPriceView" value="{{ number_format($sales->totalPrice, 0, ',', '.') }}" readonly>
                        <input type="hidden" id="totalPrice" name="totalPrice" value="{{$sales->totalPrice}}">
                    </div>

                    <div class="col-md-4">
                        <label for="totalPayment">Total Pembayaran</label>
                        <input type="text" class="form-control" id="formattedTotalPayment" value="{{ number_format($sales->totalPayment, 0, ',', '.') }}" placeholder="Total Pembayaran" required>
                        <input type="hidden" id="totalPayment" name="totalPayment" value="{{$sales->totalPayment}}">
                    </div>

                    <div class="col-md-4">
                        <x-adminlte-select name="status" id="status" value="{{ $sales->status }}" label="Status" readonly>
                            <option value="LUNAS" {{ $sales->status == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                            <option value="BELUM LUNAS" {{ $sales->status == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS</option>
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
    // $(document).ready(function() {
    //     console.log("AdminLTE & jQuery Loaded!");

    //     $("form").on("submit", function(e) {
    //         e.preventDefault(); // Prevent default form submission
    //         let formData = new FormData(this);
    //         console.log("Form Data:", [...formData.entries()]); // Show form data in console
    //         this.submit(); // Continue submission after logging
    //     });
    // });

    document.addEventListener("DOMContentLoaded", function() {

        function updateProductDetails(row) {
            let selectedOption = row.querySelector(".product-select").selectedOptions[0];
            row.querySelector(".uom").value = selectedOption.getAttribute("data-uom");
            row.querySelector(".pricePerUnit").value = selectedOption.getAttribute("data-pricePerUnit");
            row.querySelector(".price").value = selectedOption.getAttribute("data-price");
            row.querySelector(".remaining").value = selectedOption.getAttribute("data-remaining");
            row.querySelector(".productName").value = selectedOption.getAttribute("data-productName");
            row.querySelector(".productId").value = selectedOption.getAttribute("data-productId");
            row.querySelector(".stockId").value = selectedOption.value; // Ensure stockId is assigned properly

            let priceValue = selectedOption.getAttribute("data-price");
            let formattedPrice = new Intl.NumberFormat('id-ID').format(priceValue);
            row.querySelector(".price").value = priceValue;
            row.querySelector(".priceView").value = formattedPrice;

        }

        function updateTotalPrice(row) {
            let qty = parseFloat(row.querySelector(".qty").value) || 0;
            let price = parseFloat(row.querySelector(".price").value.replace(/\./g, '')) || 0; // Remove thousand separator before calculation

            let remaining = parseFloat(row.querySelector(".remaining").value) || 0;
            if (qty > remaining) {
                // Reset qty to remaining stock and show error
                row.querySelector(".qty").value = remaining;
                toastr.error('Qty pembelian melebihi sisa stock', 'Error');
            }

            let total = Math.round(qty * price);
            row.querySelector(".total").value = total;

            updateTotalPriceHeader();
        }

        function updateTotalPriceHeader() {
            let totalPrice = 0;
            document.querySelectorAll(".total").forEach(input => {
                let value = parseFloat(input.value.replace(/\./g, '')) || 0;
                totalPrice += value;
            });

            document.querySelector("#totalPrice").value = totalPrice;
            document.querySelector(".totalPriceView").value = totalPrice.toLocaleString('id-ID');
        }

        function updateEventListeners() {
            document.querySelectorAll(".product-select").forEach(select => {
                select.addEventListener("change", function() {
                    let row = this.closest("tr");
                    updateProductDetails(row);
                    updateTotalPrice(row);
                });
            });

            document.querySelectorAll(".qty").forEach(input => {
                input.addEventListener("input", function() {
                    let row = this.closest("tr");
                    updateTotalPrice(row);
                });
            });

            document.querySelectorAll(".price").forEach(input => {
                input.addEventListener("input", function() {
                    let row = this.closest("tr");
                    updateTotalPrice(row);
                });
            });

            document.querySelectorAll(".removeItem").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                    updateProductDropdowns();
                    updateTotalPriceHeader();
                });
            });
        }

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

        // Ensure existing rows have event listeners
        updateEventListeners();

        document.querySelector("#addItem").addEventListener("click", function() {
            let tableBody = document.querySelector("#salesItemsTable tbody");
            let rowCount = tableBody.rows.length;
            let newRow = document.createElement("tr");

            newRow.innerHTML = `
            <td>
                <select name="items[${rowCount}][stockId]" class="form-control product-select" required>
                    <option value="" selected disabled>Pilih Produk</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}" 
                            data-uom="{{ $stock->uom }}" 
                            data-price="{{ $stock->sellingPricePerUnit }}" 
                            data-remaining="{{ $stock->remainingStock }}" 
                            data-pricePerUnit="{{ $stock->pricePerUnit }}" 
                            data-productName="{{ $stock->productName }}"
                            data-productId="{{ $stock->productId }}">
                            {{ $stock->productName }} - {{ $stock->remainingStock }} - {{ $stock->uom }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${rowCount}][productName]" class="productName">
                <input type="hidden" name="items[${rowCount}][productId]" class="productId">
                <input type="hidden" name="items[${rowCount}][stockId]" class="stockId">
            </td>
            <td><input type="text" name="items[${rowCount}][uom]" class="form-control uom" readonly required></td>
            <td><input type="number" name="items[${rowCount}][remaining]" class="form-control remaining" readonly required></td>
            <td><input type="number" name="items[${rowCount}][qty]" class="form-control qty" required min="1"></td>
            <td><input type="number" name="items[${rowCount}][pricePerUnit]" class="form-control pricePerUnit" readonly></td>
            <td>
                <input type="number" name="items[${rowCount}][sellingPricePerUnit]" class="form-control price" required>
            </td>
            <td>
                <input type="number" name="items[${rowCount}][totalPrice]" class="form-control total" readonly>
            </td>
            <td><button type="button" class="btn btn-danger removeItem"><i class="fas fa-trash"></i></button></td>
        `;

            tableBody.appendChild(newRow);
            updateEventListeners();
            updateProductDropdowns();
        });

        // Format Total Payment Input
        const formattedInput = document.getElementById("formattedTotalPayment");
        const hiddenInput = document.getElementById("totalPayment");
        const statusSelect = document.getElementById("status");

        formattedInput.addEventListener("input", function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            console.log('raw', rawValue);
            if (rawValue !== "") {
                formattedInput.value = parseInt(rawValue).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = rawValue;

                let totalPrice = parseInt(document.getElementById("totalPrice").value);
                statusSelect.value = (parseInt(rawValue) >= totalPrice) ? "LUNAS" : "BELUM LUNAS";
            } else {
                hiddenInput.value = "";
            }
        });

        //if status is LUNAS, then total payment is equal to total price
        statusSelect.addEventListener("change", function(e) {
            if (e.target.value === "LUNAS") {
                formattedInput.value = parseInt(document.getElementById("totalPrice").value).toLocaleString("id-ID").replace(/,/g, ".");
                hiddenInput.value = document.getElementById("totalPrice").value;
            }
        });

    });
</script>
@endsection