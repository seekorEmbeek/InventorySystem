@extends('adminlte::page')

@section('title', 'Print Sales Receipt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Nota Balanja</h4>
                </div>
                <div class="card-body">
                    <p><strong>Tanggal :</strong> {{ date('d-M-Y', strtotime($sales->date)) }}</p>
                    <p><strong>Pembeli :</strong> {{ $sales->buyerName }}</p>
                    <hr>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->items as $item)
                            <tr>
                                <td>{{ $item->productName }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->uom }}</td>
                                <td>Rp {{ number_format($item->pricePerUnit, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->totalSellingPrice, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>
                    <h5 class="text-right"><strong>Total: Rp {{ number_format($sales->totalPrice, 0, ',', '.') }}</strong></h5>
                    <h5 class="text-right"><strong>Dibayar: Rp {{ number_format($sales->totalPayment, 0, ',', '.') }}</strong></h5>
                    @if($sales->remainingPayment <= 0)
                        <h5 class="text-right"><strong>
                            Kembalian: Rp {{ number_format($sales->totalPayment - $sales->totalPrice, 0, ',', '.') }}
                        </strong></h5>
                        @else
                        <h5 class="text-right"><strong>
                                Kekurangan: Rp -{{ number_format($sales->remainingPayment, 0, ',', '.') }}
                            </strong></h5>
                        @endif
                        <hr>

                        <p class="text-center">Terima Kasih telah berbelanja!</p>
                </div>
                <div class="card-footer text-center">
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    @media print {

        .card-footer,
        .btn {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: white !important;
        }
    }
</style>
@endsection