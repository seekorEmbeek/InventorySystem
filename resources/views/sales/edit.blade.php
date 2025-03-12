@extends('adminlte::page')

@section('title', 'Edit Penjualan')

@section('content_header')
<h1>Edit Penjualan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.update',$sales->id) }}" method="POST">
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

                                <input type="hidden" name="items[{{ $loop->index }}][productName]" class="productName">
                                <input type="hidden" name="items[{{ $loop->index }}][productId]" class="productId">
                                <input type="hidden" name="items[{{ $loop->index }}][stockId]" class="stockId">
                            </td>
                            <td><input type="text" name="items[{{ $loop->index }}][uom]" class="form-control uom" value="{{ $item->uom }}" readonly></td>
                            <td><input type="number" name="items[{{ $loop->index }}][remaining]" class="form-control remaining" value="{{ $stocks->where('id', $item->stock_id)->first()?->remainingStock ?? 0 }}" readonly></td>
                            <td><input type="number" name="items[{{ $loop->index }}][qty]" class="form-control qty" value="{{ $item->qty }}" required min="1"></td>
                            <td>
                                <input type="number" name="items[{{ $loop->index }}][priceView]" class="form-control priceView" value="{{  number_format($item->sellingPricePerUnit, 0, ',', '.')}}" readonly>
                                <input type="hidden" name="items[{{ $loop->index }}]" class="price">
                                <input type="hidden" name="items[{{ $loop->index }}]" class="pricePerUnit" readonly>
                            </td>
                            <td><input type="number" name="items[{{ $loop->index }}][totalPrice]" class="form-control total" value="{{ $item->totalSellingPrice * $item->qty }}" readonly></td>
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
                        <x-adminlte-select name="status" label="Status" disabled>
                            <option value="LUNAS" {{ $sales->status == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                            <option value="BELUM LUNAS" {{ $sales->status == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS</option>
                        </x-adminlte-select>

                        <!-- Hidden input to store the selected value -->
                        <input type="hidden" name="status" value="{{ $sales->status }}">
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
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Function to update UOM and Price based on selected product
        function updateProductDetails(row) {
            let selectedOption = row.querySelector(".product-select").selectedOptions[0];
            row.querySelector(".uom").value = selectedOption.getAttribute("data-uom");
            row.querySelector(".price").value = selectedOption.getAttribute("data-price");
        }

        // Update total price in header when any item changes
        function updateTotalPriceHeader() {
            let totalPrice = 0;
            document.querySelectorAll(".total").forEach(input => {
                let value = parseFloat(input.value.replace(/\./g, '')) || 0;
                totalPrice += value;
            });

            // Set total payment value
            document.querySelector("#totalPrice").value = totalPrice;
            document.querySelector(".totalPriceView").value = totalPrice.toLocaleString('id-ID');
        }

        // Function to calculate total price
        function updateTotalPrice(row) {
            let qty = row.querySelector(".qty").value || 0;
            let price = row.querySelector(".price").value || 0;

            let remaining = parseFloat(row.querySelector(".remaining").value) || 0;
            if (qty > remaining) {

                //reset qty to remaining stock
                row.querySelector(".qty").value = remaining;
                //use toastr to show error message
                toastr.error('Qty pembelian melebihi sisa stock', 'Error');
                row.querySelector(".total").value = row.querySelector(".qty").value * price;

            } else {

                row.querySelector(".total").value = qty * price;
            }

            updateTotalPriceHeader();
        }

        // Add event listener to product select dropdowns
        document.querySelectorAll(".product-select").forEach(select => {
            select.addEventListener("change", function() {
                let row = this.closest("tr");
                updateProductDetails(row);
                updateTotalPrice(row);
            });
        });

        // Add event listener to quantity inputs
        document.querySelectorAll(".qty").forEach(input => {
            input.addEventListener("input", function() {
                let row = this.closest("tr");
                updateTotalPrice(row);
            });
        });

        //add event listener for looping through all items
        document.querySelectorAll(".product-select").forEach(select => {
            let row = select.closest("tr");
            updateProductDetails(row);
            updateTotalPrice(row);
        });

        const addItemButton = document.getElementById("addItem");
        const itemsTable = document.querySelector("#salesItemsTable  tbody");

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

                    updateTotalPriceHeader(); // Update total payment when any item changes
                });

            });


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